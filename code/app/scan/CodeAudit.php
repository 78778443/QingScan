<?php
declare(strict_types=1);

namespace app\scan;

/**
 * 内置代码审计引擎（纯 PHP 实现，自研，不依赖任何外部工具/命令）
 * 递归扫描代码目录，按内置规则库逐行正则匹配，
 * 输出与外部代码审计工具 --json 兼容的结果结构（check_id/path/start.line/end.line/extra），
 * 可直接被 CodeAuditModel::addDataAll() 解析入库，任务流程无需改动。
 */
class CodeAudit
{
    /**
     * 规则库（可扩展）：新增规则只需在此追加数组元素
     * id      规则唯一标识（与 semgrep check_id 同语义）
     * ext     适用文件扩展名，'*' 表示适用于所有代码文件
     * pattern 正则表达式（使用 # 作定界符，逐行匹配）
     * message 命中提示信息
     * severity 严重级别 ERROR / WARNING
     */
    private const RULES = [
        // ---------- 危险函数调用（PHP） ----------
        ['id' => 'php.lang.security.eval', 'ext' => ['php'], 'pattern' => '#\beval\s*\(#i', 'message' => '检测到 eval() 动态执行代码，若参数包含用户输入可导致任意代码执行', 'severity' => 'ERROR'],
        ['id' => 'php.lang.security.assert', 'ext' => ['php'], 'pattern' => '#\bassert\s*\(#i', 'message' => '检测到 assert() 调用，PHP7.2+ 下 assert 可执行代码，参数含用户输入有代码执行风险', 'severity' => 'ERROR'],
        ['id' => 'php.lang.security.system', 'ext' => ['php'], 'pattern' => '#\bsystem\s*\(#i', 'message' => '检测到 system() 系统命令执行函数', 'severity' => 'ERROR'],
        ['id' => 'php.lang.security.exec', 'ext' => ['php'], 'pattern' => '#\bexec\s*\(#i', 'message' => '检测到 exec() 系统命令执行函数', 'severity' => 'ERROR'],
        ['id' => 'php.lang.security.shell_exec', 'ext' => ['php'], 'pattern' => '#\bshell_exec\s*\(#i', 'message' => '检测到 shell_exec() 系统命令执行函数', 'severity' => 'ERROR'],
        ['id' => 'php.lang.security.passthru', 'ext' => ['php'], 'pattern' => '#\bpassthru\s*\(#i', 'message' => '检测到 passthru() 系统命令执行函数', 'severity' => 'ERROR'],
        ['id' => 'php.lang.security.proc_open', 'ext' => ['php'], 'pattern' => '#\bproc_open\s*\(#i', 'message' => '检测到 proc_open() 系统命令执行函数', 'severity' => 'ERROR'],
        ['id' => 'php.lang.security.popen', 'ext' => ['php'], 'pattern' => '#\bpopen\s*\(#i', 'message' => '检测到 popen() 系统命令执行函数', 'severity' => 'ERROR'],
        ['id' => 'php.lang.security.backtick', 'ext' => ['php'], 'pattern' => '#`#', 'message' => '检测到反引号命令执行，PHP 中反引号内容会作为系统命令执行', 'severity' => 'ERROR'],
        ['id' => 'php.lang.security.preg_replace_e', 'ext' => ['php'], 'pattern' => '#preg_replace\s*\([^)]*[\'"][^\'"]*[eE][\'"]#i', 'message' => '检测到 preg_replace 使用 /e 修饰符，旧版 PHP 将替换串作为代码执行（PHP7 已废弃）', 'severity' => 'ERROR'],
        ['id' => 'php.lang.security.create_function', 'ext' => ['php'], 'pattern' => '#\bcreate_function\s*\(#i', 'message' => '检测到 create_function() 字符串代码执行（PHP7.2+ 已废弃）', 'severity' => 'ERROR'],

        // ---------- SQL 注入 ----------
        ['id' => 'php.lang.security.sql-injection', 'ext' => ['php'], 'pattern' => '#(query|exec|sql|where)\s*\([^)]*(\$_GET|\$_POST|\$_REQUEST)#i', 'message' => '检测到 SQL 查询调用(query/sql/where)附近拼接用户输入($_GET/$_POST/$_REQUEST)，存在 SQL 注入风险', 'severity' => 'ERROR'],
        ['id' => 'php.lang.security.sql-concat', 'ext' => ['php'], 'pattern' => '#\b(select|insert|update|delete)\b[^;]*(\$_GET|\$_POST|\$_REQUEST)#i', 'message' => '检测到 SQL 语句拼接用户输入($_GET/$_POST/$_REQUEST)，存在 SQL 注入风险，建议使用参数化查询', 'severity' => 'ERROR'],

        // ---------- 命令注入 ----------
        ['id' => 'php.lang.security.command-injection', 'ext' => ['php'], 'pattern' => '#(system|exec|shell_exec|passthru|proc_open|popen)\s*\([^)]*\$\w+#i', 'message' => '检测到命令执行函数参数包含变量，若变量可控存在命令注入风险', 'severity' => 'ERROR'],

        // ---------- 文件包含 ----------
        ['id' => 'php.lang.security.file-inclusion', 'ext' => ['php'], 'pattern' => '#\b(include|include_once|require|require_once)\s*[\(]?\s*\$#i', 'message' => '检测到文件包含函数(include/require)参数为变量，若变量可控存在本地/远程文件包含(LFI/RFI)风险', 'severity' => 'WARNING'],

        // ---------- 文件上传 ----------
        ['id' => 'php.lang.security.file-upload', 'ext' => ['php'], 'pattern' => '#move_uploaded_file\s*\([^)]*\$_FILES#i', 'message' => '检测到文件上传(move_uploaded_file)操作，需校验上传文件类型、大小与存储路径', 'severity' => 'WARNING'],

        // ---------- SSRF ----------
        ['id' => 'php.lang.security.ssrf', 'ext' => ['php'], 'pattern' => '#(file_get_contents|curl_init|curl_exec|fopen)\s*\([^)]*(\$_GET|\$_POST|\$_REQUEST)#i', 'message' => '检测到网络请求函数(file_get_contents/curl/fopen)参数包含用户输入($_GET/$_POST/$_REQUEST)，存在 SSRF 风险', 'severity' => 'WARNING'],

        // ---------- XSS 输出 ----------
        ['id' => 'php.lang.security.xss', 'ext' => ['php'], 'pattern' => '#\b(echo|print|print_r)\b\s*\(?[^;]*(\$_GET|\$_POST|\$_REQUEST)#i', 'message' => '检测到用户输入($_GET/$_POST/$_REQUEST)未经转义直接输出(echo/print)，存在 XSS 风险', 'severity' => 'WARNING'],

        // ---------- 反序列化 ----------
        ['id' => 'php.lang.security.unserialize', 'ext' => ['php'], 'pattern' => '#unserialize\s*\([^)]*(\$_GET|\$_POST|\$_REQUEST)#i', 'message' => '检测到 unserialize() 反序列化用户输入($_GET/$_POST/$_REQUEST)，存在对象注入/反序列化漏洞风险', 'severity' => 'ERROR'],

        // ---------- 变量覆盖 ----------
        ['id' => 'php.lang.security.variable-overwrite', 'ext' => ['php'], 'pattern' => '#(extract\s*\(\s*(\$_GET|\$_POST|\$_REQUEST)|parse_str\s*\()#i', 'message' => '检测到 extract($_GET/$_POST/$_REQUEST) 或 parse_str()，可导致变量覆盖漏洞', 'severity' => 'WARNING'],

        // ---------- 弱口令硬编码（通用，适用于所有语言） ----------
        ['id' => 'generic.secrets.hardcoded-password', 'ext' => ['*'], 'pattern' => '#password\s*=\s*[\'"][^\'"]{1,12}[\'"]#i', 'message' => '检测到硬编码弱口令(password=)，建议使用配置文件/环境变量管理凭据', 'severity' => 'WARNING'],
    ];

    /** 扫描的代码文件扩展名 */
    private const EXTENSIONS = ['php', 'java', 'py', 'go', 'js', 'ts', 'jsp', 'asp', 'xml'];

    // ============================================================
    // L2 语法感知层：tokenizer 污点分析（PHP）
    // source（用户输入）→ 变量传播 → sink（危险函数）→ 命中
    // ============================================================

    /** 用户输入来源（超全局 + ThinkPHP input()） */
    private const TAINT_SOURCES = ['$_GET', '$_POST', '$_REQUEST', '$_FILES', '$_COOKIE', 'input'];

    /** 污点传播目标（sink），按漏洞类型分组 */
    private const TAINT_RULES = [
        ['id' => 'php.lang.taint.sql-injection', 'sinks' => ['query', 'exec', 'sql', 'where', 'mysqli_query', 'pg_query', 'pdo_query'], 'message' => '污点分析：用户输入经过变量传播流入 SQL 查询，存在 SQL 注入风险', 'severity' => 'ERROR'],
        ['id' => 'php.lang.taint.command-injection', 'sinks' => ['system', 'exec', 'shell_exec', 'passthru', 'proc_open', 'popen', 'pcntl_exec'], 'message' => '污点分析：用户输入流入命令执行函数，存在命令注入风险', 'severity' => 'ERROR'],
        ['id' => 'php.lang.taint.code-execution', 'sinks' => ['eval', 'assert', 'create_function'], 'message' => '污点分析：用户输入流入代码执行函数，存在任意代码执行风险', 'severity' => 'ERROR'],
        ['id' => 'php.lang.taint.xss', 'sinks' => ['echo', 'print', 'print_r', 'printf'], 'message' => '污点分析：用户输入未经转义直接输出，存在 XSS 风险', 'severity' => 'WARNING'],
        ['id' => 'php.lang.taint.file-inclusion', 'sinks' => ['include', 'include_once', 'require', 'require_once'], 'message' => '污点分析：用户输入流入文件包含函数，存在文件包含(LFI/RFI)风险', 'severity' => 'WARNING'],
        ['id' => 'php.lang.taint.ssrf', 'sinks' => ['file_get_contents', 'curl_init', 'curl_exec', 'fopen', 'fsockopen', 'file_put_contents', 'fwrite'], 'message' => '污点分析：用户输入流入网络/文件访问函数，存在 SSRF/任意文件读写风险', 'severity' => 'WARNING'],
        ['id' => 'php.lang.taint.unserialize', 'sinks' => ['unserialize'], 'message' => '污点分析：用户输入流入 unserialize()，存在反序列化漏洞风险', 'severity' => 'ERROR'],
    ];

    /** 净化函数：参数经过这些函数后视为安全（不再传播污点） */
    private const TAINT_SANITIZERS = [
        'intval', 'abs', 'floatval', 'boolval', 'htmlspecialchars', 'htmlentities',
        'strip_tags', 'addslashes', 'mysqli_real_escape_string', 'pg_escape_string',
        'sqlite_escape_string', 'urlencode', 'rawurlencode', 'json_encode', 'md5', 'sha1',
        'crc32', 'bin2hex', 'base64_encode', 'trim', 'ltrim', 'rtrim', 'strtolower',
        'strtoupper', 'ucfirst', 'intdiv', 'sprintf',
    ];

    /** 跳过的目录 */
    private const SKIP_DIRS = ['.git', 'vendor', 'node_modules', 'runtime', 'dist'];

    /** 单文件大小上限（1MB） */
    private const MAX_FILE_SIZE = 1048576;

    /** 每文件每规则最大命中数（防结果爆炸） */
    private const MAX_PER_FILE_RULE = 5;

    /** 总命中条数上限 */
    private const MAX_TOTAL = 2000;

    /** extra.lines 截断长度 */
    private const LINE_MAX_LEN = 500;

    /**
     * 返回规则库（供调试/扩展）
     */
    public static function rules(): array
    {
        return self::RULES;
    }

    /**
     * 扫描代码目录，将结果以 semgrep 兼容 JSON 写入 $outPath
     * @param string $codePath 待扫描代码目录
     * @param string $outPath  结果 JSON 输出路径
     * @return int 命中结果条数
     */
    public static function scan(string $codePath, string $outPath): int
    {
        $results = [];
        if (!is_dir($codePath)) {
            self::writeJson($outPath, $results);
            return 0;
        }

        $baseLen = strlen(rtrim($codePath, '/\\'));
        $total = 0;

        foreach (self::collectFiles($codePath) as $file) {
            if ($total >= self::MAX_TOTAL) break;

            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            $relativePath = ltrim(str_replace('\\', '/', substr($file, $baseLen)), '/');

            $content = @file_get_contents($file);
            if ($content === false) continue;
            $lines = explode("\n", $content);

            // L2：PHP 文件先做 tokenizer 污点分析，并收集注释行供 L1 过滤
            $commentLines = [];
            if ($ext === 'php') {
                $commentLines = self::commentLines($content);
                foreach (self::taintAnalyze($content) as $hit) {
                    if ($total >= self::MAX_TOTAL) break 2;
                    $results[] = [
                        'check_id' => $hit['id'],
                        'path' => $relativePath,
                        'start' => ['line' => $hit['line']],
                        'end' => ['line' => $hit['line']],
                        'extra' => [
                            'message' => $hit['message'],
                            'severity' => $hit['severity'],
                            'lines' => substr(trim($hit['code']), 0, self::LINE_MAX_LEN),
                        ],
                    ];
                    $total++;
                }
            }

            $skipL1 = $ext === 'php' ? [
                'php.lang.security.sql-injection', 'php.lang.security.sql-concat',
                'php.lang.security.command-injection', 'php.lang.security.xss',
                'php.lang.security.ssrf', 'php.lang.security.unserialize',
                'php.lang.security.file-inclusion',
            ] : [];

            foreach (self::RULES as $rule) {
                if ($total >= self::MAX_TOTAL) break 2;
                if (!self::extMatch($rule['ext'], $ext)) continue;
                if (in_array($rule['id'], $skipL1, true)) continue;

                $fileRuleCount = 0;
                foreach ($lines as $lineNo => $line) {
                    if ($fileRuleCount >= self::MAX_PER_FILE_RULE) break;
                    if (isset($commentLines[$lineNo + 1])) continue; // 注释行跳过
                    if (!self::matchLine($rule['pattern'], $line)) continue;

                    $results[] = [
                        'check_id' => $rule['id'],
                        'path' => $relativePath,
                        'start' => ['line' => $lineNo + 1],
                        'end' => ['line' => $lineNo + 1],
                        'extra' => [
                            'message' => $rule['message'],
                            'severity' => $rule['severity'],
                            'lines' => substr(trim($line), 0, self::LINE_MAX_LEN),
                        ],
                    ];
                    $fileRuleCount++;
                    $total++;
                    if ($total >= self::MAX_TOTAL) break 2;
                }
            }
        }

        self::writeJson($outPath, $results);
        return $total;
    }

    /** 收集 PHP 代码中注释所在的行号集合 */
    private static function commentLines(string $code): array
    {
        $lines = [];
        foreach (@token_get_all($code) as $tok) {
            if (is_array($tok) && ($tok[0] === T_COMMENT || $tok[0] === T_DOC_COMMENT)) {
                $lines[$tok[2]] = true;
            }
        }
        return $lines;
    }

    /**
     * 递归收集待扫描代码文件
     */
    private static function collectFiles(string $dir): array
    {
        $files = [];
        $items = @scandir($dir);
        if ($items === false) return $files;

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;
            $path = $dir . '/' . $item;

            if (is_dir($path)) {
                if (in_array($item, self::SKIP_DIRS, true)) continue;
                $files = array_merge($files, self::collectFiles($path));
            } elseif (is_file($path)) {
                if (filesize($path) >= self::MAX_FILE_SIZE) continue;
                if (in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), self::EXTENSIONS, true)) {
                    $files[] = $path;
                }
            }
        }
        return $files;
    }

    /**
     * 规则扩展名是否匹配：'*' 表示适用于所有代码文件
     */
    private static function extMatch(array $ruleExts, string $fileExt): bool
    {
        return in_array('*', $ruleExts, true) || in_array($fileExt, $ruleExts, true);
    }

    /**
     * 单行正则匹配（规则使用 # 定界符）
     */
    private static function matchLine(string $pattern, string $line): bool
    {
        return @preg_match($pattern, $line) === 1;
    }

    /**
     * 写入结果 JSON（目录不存在则创建）
     */
    private static function writeJson(string $outPath, array $results): void
    {
        $dir = dirname($outPath);
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        @file_put_contents($outPath, json_encode(['results' => $results], JSON_UNESCAPED_UNICODE));
    }

    // ============================================================
    // L2 污点分析实现
    // ============================================================

    /**
     * 对 PHP 代码做 tokenizer 污点分析（单函数作用域 + 净化识别）
     * @return array [['id','line','message','severity','code'], ...]
     */
    private static function taintAnalyze(string $code): array
    {
        $tokens = @token_get_all($code);
        if (empty($tokens)) {
            return [];
        }

        $hits = [];
        $n = count($tokens);
        // 变量污点表（当前作用域）：$var => 来源行号
        $varTaint = [];
        // 函数作用域栈：记录函数体起始大括号深度
        $scopeStack = [];
        $funcPending = false;
        $depth = 0;

        $i = 0;
        while ($i < $n) {
            $tok = $tokens[$i];
            if (is_array($tok)) {
                $id = $tok[0];
                $text = $tok[1];
                $line = $tok[2];
            } else {
                $id = null;
                $text = $tok;
                $line = 0;
            }

            // 函数定义：标记即将进入函数体
            if ($id === T_FUNCTION) {
                $funcPending = true;
            }
            // 大括号深度管理 + 函数作用域
            if ($text === '{') {
                $depth++;
                if ($funcPending) {
                    $scopeStack[] = $depth;
                    $varTaint = [];
                    $funcPending = false;
                }
            } elseif ($text === '}') {
                $depth--;
                if ($scopeStack && $depth < end($scopeStack)) {
                    array_pop($scopeStack);
                    $varTaint = [];
                }
            }
            // 赋值语句：$var = expr（跳过 '=' 前的空白 token）
            if ($text === '=' && $i > 0) {
                $j = $i - 1;
                while ($j > 0 && is_array($tokens[$j]) && $tokens[$j][0] === T_WHITESPACE) {
                    $j--;
                }
                if (is_array($tokens[$j]) && $tokens[$j][0] === T_VARIABLE) {
                    $target = $tokens[$j][1];
                    [$tainted, $srcLine] = self::exprTaint($tokens, $i + 1, $varTaint, $line);
                    if ($tainted) {
                        $varTaint[$target] = $srcLine ?: $line;
                    } else {
                        unset($varTaint[$target]);
                    }
                }
            }
            // 函数调用 sink 检测（含 eval/echo/include 等关键字 token；echo/print 无括号也可）
            $sinkIds = [T_STRING, T_VARIABLE, T_EVAL, T_ECHO, T_PRINT, T_INCLUDE, T_INCLUDE_ONCE, T_REQUIRE, T_REQUIRE_ONCE];
            $isEchoStmt = in_array($id, [T_ECHO, T_PRINT], true);
            if (in_array($id, $sinkIds, true) && ($isEchoStmt || self::isCallParen($tokens, $i))) {
                $fname = strtolower($text);
                $sinkRule = self::sinkRuleOf($fname);
                if ($sinkRule !== null) {
                    [$tainted, $srcLine] = self::exprTaint($tokens, $i + 1, $varTaint, $line);
                    if ($tainted) {
                        $hits[] = [
                            'id' => $sinkRule['id'],
                            'line' => $line,
                            'message' => $sinkRule['message'] . '（输入来源第 ' . $srcLine . ' 行）',
                            'severity' => $sinkRule['severity'],
                            'code' => self::extractLine($code, $line),
                        ];
                    }
                }
            }
            $i++;
        }
        return $hits;
    }

    /**
     * 表达式污点求值：从 $tokens[$start] 起解析表达式，直到语句分隔符
     * @return array [tainted(bool), sourceLine(int)]
     */
    private static function exprTaint(array $tokens, int $start, array $varTaint, int $defaultLine): array
    {
        $tainted = false;
        $srcLine = 0;
        $n = count($tokens);
        $depth = 0;
        $i = $start;

        for (; $i < $n; $i++) {
            $tok = $tokens[$i];
            if (is_string($tok)) {
                if ($tok === ';' || ($tok === ')' && $depth === 0)) {
                    break; // 语句边界
                }
                if ($tok === ',') {
                    continue; // 多参数调用：继续求值后续参数（任一污点即命中）
                }
                if ($tok === '(' || $tok === '[' || $tok === '{') {
                    $depth++;
                } elseif ($tok === ')' || $tok === ']' || $tok === '}') {
                    $depth--;
                }
                if ($tok === '.') {
                    // 拼接：继续求值右侧，结果取或
                    continue;
                }
                if ($tok === '?') {
                    // 三元：取两侧任意污点（简化为继续求值）
                    continue;
                }
                if ($tok === '=' && $depth === 0) {
                    break; // 嵌套赋值不处理
                }
                continue;
            }
            $id = $tok[0];
            $text = $tok[1];
            $line = $tok[2];

            if ($id === T_VARIABLE) {
                if (in_array($text, self::TAINT_SOURCES, true)) {
                    $tainted = true;
                    if (!$srcLine) $srcLine = $line;
                } elseif (isset($varTaint[$text])) {
                    $tainted = true;
                    if (!$srcLine) $srcLine = $varTaint[$text];
                }
                continue;
            }
            if ($id === T_STRING && self::isCallParen($tokens, $i)) {
                $fname = strtolower($text);
                if (in_array($fname, self::TAINT_SANITIZERS, true)) {
                    // 净化函数：跳过参数（在括号内），结果为 clean
                    $i = self::skipParen($tokens, $i + 1);
                    $tainted = false;
                    $srcLine = 0;
                    continue;
                }
                // 其他函数：参数 tainted 则结果 tainted（保守传播）
                [$t2, $s2] = self::exprTaint($tokens, $i + 1, $varTaint, $line);
                if ($t2) {
                    $tainted = true;
                    if (!$srcLine) $srcLine = $s2 ?: $line;
                }
                $i = self::skipParen($tokens, $i + 1);
                continue;
            }
            // 其他 token（数字/字符串/关键字等）不引入污点
        }
        return [$tainted, $srcLine];
    }

    /** 判断 $i 之后（跳过空白）是否为函数调用 '(' */
    private static function isCallParen(array $tokens, int $i): bool
    {
        $n = count($tokens);
        $j = $i + 1;
        while ($j < $n && is_array($tokens[$j]) && $tokens[$j][0] === T_WHITESPACE) {
            $j++;
        }
        return $j < $n && $tokens[$j] === '(';
    }

    /** 跳过括号（从 '(' 开始），返回 ')' 的索引 */
    private static function skipParen(array $tokens, int $openIdx): int
    {
        $n = count($tokens);
        $depth = 0;
        for ($i = $openIdx; $i < $n; $i++) {
            $tok = $tokens[$i];
            if (is_string($tok)) {
                if ($tok === '(') $depth++;
                elseif ($tok === ')') {
                    $depth--;
                    if ($depth === 0) return $i;
                }
            }
        }
        return $n - 1;
    }

    /** 查找函数名对应的污点规则（含 SQL 关键字语句匹配） */
    private static function sinkRuleOf(string $fname): ?array
    {
        // 直接函数名匹配
        foreach (self::TAINT_RULES as $rule) {
            if (in_array($fname, $rule['sinks'], true)) {
                return $rule;
            }
        }
        // SQL 语句关键字（select/insert/update/delete 作为"函数"调用场景少见，交给 L1 正则）
        return null;
    }

    /** 取指定行代码（截断） */
    private static function extractLine(string $code, int $line): string
    {
        $lines = explode("\n", $code);
        $text = $lines[$line - 1] ?? '';
        return substr(trim($text), 0, self::LINE_MAX_LEN);
    }
}

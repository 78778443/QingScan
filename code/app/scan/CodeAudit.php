<?php
declare(strict_types=1);

namespace app\scan;

/**
 * 内置代码审计引擎（纯 PHP 实现，自研，不依赖任何外部工具/命令）
 * 替代外部 semgrep 工具：递归扫描代码目录，按内置规则库逐行正则匹配，
 * 输出与 semgrep --json 兼容的结果结构（check_id/path/start.line/end.line/extra），
 * 可直接被 SemgrepModel::addDataAll() 解析入库，任务流程无需改动。
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

            foreach (self::RULES as $rule) {
                if ($total >= self::MAX_TOTAL) break 2;
                if (!self::extMatch($rule['ext'], $ext)) continue;

                $fileRuleCount = 0;
                foreach ($lines as $lineNo => $line) {
                    if ($fileRuleCount >= self::MAX_PER_FILE_RULE) break;
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
}

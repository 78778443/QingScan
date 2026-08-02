<?php
declare(strict_types=1);

namespace app\scan;

/**
 * 基础 Web 漏洞检测引擎（纯 PHP 自研，替代 nuclei / xray / vulmap 的基础检测）
 *
 * 用法: VulnScan::scan($url) 返回检测结果数组，每条:
 *   ['name'=>string, 'severity'=>string(low/medium/high/critical),
 *    'url'=>string, 'payload'=>string, 'description'=>string]
 */
class VulnScan
{
    // ==================== 内置检测规则（可扩展：新增规则往此数组追加即可） ====================
    private const RULES = [
        [
            'name' => 'phpinfo 信息泄露',
            'severity' => 'high',
            'paths' => ['/info.php', '/phpinfo.php', '/test.php', '/php_info.php'],
            'keywords' => ['phpinfo()', 'PHP Version'],
            'description' => '目标站点存在 phpinfo() 页面，可能泄露 PHP 配置、环境变量等敏感信息',
        ],
        [
            'name' => '备份文件泄露',
            'severity' => 'medium',
            'paths' => [
                '/index.php.bak', '/config.php.bak', '/db.sql', '/backup.zip',
                '/backup.tar.gz', '/www.zip', '/site.zip', '/web.zip', '/index.html.bak',
                '/config.old', '/config.bak', '/.bak', '/data.sql',
            ],
            'keywords' => ['<?php', 'mysql', 'mysqli', 'INSERT INTO', 'CREATE TABLE', 'DB_HOST', 'db_host', 'password'],
            'description' => '目标站点存在可访问的备份/源码文件，可能泄露源代码或数据库信息',
            'checkContentType' => true,
        ],
        [
            'name' => '目录遍历',
            'severity' => 'high',
            'paths' => [
                '/%2e%2e%2f%2e%2e%2f%2e%2e%2fetc%2fpasswd',
                '/..%2f..%2f..%2fetc%2fpasswd',
                '/WEB-INF/web.xml',
                '/..%2f..%2f..%2f..%2fwindows%2fwin.ini',
            ],
            'keywords' => ['root:', '[fonts]', 'web-app'],
            'description' => '目标站点存在目录遍历漏洞，可读取服务器任意文件',
        ],
        [
            'name' => 'Git 配置泄露',
            'severity' => 'medium',
            'paths' => ['/.git/config'],
            'keywords' => ['[core]', 'repositoryformatversion'],
            'description' => '目标站点泄露 .git/config，可能暴露源码仓库配置信息',
        ],
        [
            'name' => '环境配置泄露',
            'severity' => 'medium',
            'paths' => ['/.env'],
            'keywords' => ['APP_', 'DB_', 'SECRET'],
            'description' => '目标站点泄露 .env 环境配置文件，可能包含数据库账号等敏感信息',
        ],
        [
            'name' => 'web.config 泄露',
            'severity' => 'medium',
            'paths' => ['/web.config'],
            'keywords' => ['<configuration'],
            'description' => '目标站点泄露 web.config 配置文件',
        ],
        [
            'name' => '.htaccess 配置泄露',
            'severity' => 'medium',
            'paths' => ['/.htaccess'],
            'keywords' => ['RewriteEngine'],
            'description' => '目标站点泄露 .htaccess 配置文件',
        ],
        [
            'name' => '目录配置泄露',
            'severity' => 'medium',
            'paths' => ['/config/'],
            'keywords' => ['<?php', 'DB_', 'db_', 'password', 'database', 'define('],
            'description' => '目标站点 /config/ 目录可访问且包含敏感配置信息',
        ],
        [
            'name' => 'Spring Actuator 未授权访问',
            'severity' => 'high',
            'paths' => ['/actuator', '/actuator/env', '/actuator/health'],
            'keywords' => ['{"status"', 'spring', 'activeProfiles'],
            'description' => 'Spring Actuator 监控接口未授权访问，可能泄露运行环境与配置信息',
        ],
        [
            'name' => 'Druid 未授权访问',
            'severity' => 'high',
            'paths' => ['/druid/index.html', '/druid'],
            'keywords' => ['druid', 'Druid'],
            'description' => 'Druid 监控面板未授权访问，可能泄露数据库连接与 SQL 执行信息',
        ],
        [
            'name' => 'Nacos 未授权访问',
            'severity' => 'high',
            'paths' => ['/nacos/', '/nacos/v1/console/health/liveness'],
            'keywords' => ['nacos', 'Nacos'],
            'description' => 'Nacos 服务未授权访问，可能泄露微服务配置信息',
        ],
    ];

    /** 反射型 XSS 检测注入载荷 */
    private const XSS_PAYLOAD = '<script>alert(document.domain)</script>';

    /** robots.txt 内容摘要最大长度 */
    private const ROBOTS_SUMMARY_LEN = 200;

    /**
     * 对单个 URL 执行基础漏洞检测
     */
    public static function scan(string $url): array
    {
        $results = [];

        $root = self::getRoot($url);
        if ($root === null) {
            return $results;
        }

        $cache = [];

        // 基线请求：根路径 body 用于过滤统一 404 / 软 404 页面
        $baseBody = '';
        $baseResp = HttpClient::request($root . '/');
        if ($baseResp !== null) {
            $baseBody = (string)($baseResp['body'] ?? '');
        }

        // 1. 路径探测类规则
        $rules = array_merge(self::RULES, self::customRules('vuln'));
        foreach ($rules as $rule) {
            foreach ($rule['paths'] as $path) {
                $resp = self::requestPath($root, $path, $cache);
                if (!self::isCandidate($resp, $baseBody)) {
                    continue;
                }
                $body = (string)($resp['body'] ?? '');
                $hit = false;
                foreach ($rule['keywords'] as $keyword) {
                    if (stripos($body, $keyword) !== false) {
                        $hit = true;
                        break;
                    }
                }
                // 备份文件规则：状态码 200 且 Content-Type 非 text/html 也算命中
                if (!$hit && !empty($rule['checkContentType'])) {
                    $contentType = strtolower((string)($resp['headers']['content-type'] ?? ''));
                    if ($contentType !== '' && strpos($contentType, 'text/html') === false) {
                        $hit = true;
                    }
                }
                if ($hit) {
                    $results[] = self::result($rule['name'], $rule['severity'], $root . $path, $path, $rule['description']);
                }
            }
        }

        // 2. robots.txt 信息泄露（仅记录存在信息，severity low，url 字段记录内容摘要）
        $resp = self::requestPath($root, '/robots.txt', $cache);
        if ($resp !== null && (int)($resp['code'] ?? 0) === 200) {
            $body = (string)($resp['body'] ?? '');
            if (stripos($body, 'Disallow') !== false) {
                $summary = trim(preg_replace('/\s+/u', ' ', $body));
                $summary = function_exists('mb_substr') ? mb_substr($summary, 0, self::ROBOTS_SUMMARY_LEN) : substr($summary, 0, self::ROBOTS_SUMMARY_LEN);
                $results[] = self::result('robots.txt 信息泄露', 'low', $summary, '/robots.txt', '目标站点存在 robots.txt，可能泄露敏感路径信息');
            }
        }

        // 3. 反射型 XSS（仅对原始 URL 的 GET 参数检测，无参数则跳过）
        foreach (self::xssPayloadUrls($url) as $xssUrl) {
            $resp = HttpClient::request($xssUrl);
            if ($resp === null || (int)($resp['code'] ?? 0) !== 200) {
                continue;
            }
            $body = (string)($resp['body'] ?? '');
            if (stripos($body, self::XSS_PAYLOAD) !== false) {
                $results[] = self::result('反射型 XSS', 'medium', $xssUrl, self::XSS_PAYLOAD, 'GET 参数值未过滤直接回显，存在反射型 XSS 漏洞');
            }
        }

        return $results;
    }

    /**
     * 拼接规则路径请求（同一路径去重缓存，避免重复请求）
     */
    private static function requestPath(string $root, string $path, array &$cache): ?array
    {
        if (array_key_exists($path, $cache)) {
            return $cache[$path];
        }
        $resp = HttpClient::request($root . $path);
        $cache[$path] = $resp;
        return $resp;
    }

    /**
     * 候选响应过滤：请求失败 / 404 / 403 / 与基线响应体完全一致（统一 404 页）则跳过
     */
    private static function isCandidate(?array $resp, string $baseBody): bool
    {
        if ($resp === null) {
            return false;
        }
        $code = (int)($resp['code'] ?? 0);
        if ($code !== 200) {
            return false;
        }
        $body = (string)($resp['body'] ?? '');
        if ($baseBody !== '' && $body === $baseBody) {
            return false;
        }
        return true;
    }

    /**
     * 取 scheme://host[:port] 作为探测根
     */
    private static function getRoot(string $url): ?string
    {
        $parts = parse_url($url);
        if (empty($parts['scheme']) || empty($parts['host'])) {
            return null;
        }
        $scheme = strtolower((string)$parts['scheme']);
        if (!in_array($scheme, ['http', 'https'], true)) {
            return null;
        }
        $root = $scheme . '://' . $parts['host'];
        if (!empty($parts['port'])) {
            $root .= ':' . $parts['port'];
        }
        return $root;
    }

    /**
     * 生成 XSS 注入 URL 列表：原始 URL 每个 GET 参数单独替换为注入载荷（urlencode）
     */
    private static function xssPayloadUrls(string $url): array
    {
        $urls = [];
        $parts = parse_url($url);
        if (empty($parts['query'])) {
            return $urls;
        }
        parse_str($parts['query'], $params);
        if (empty($params)) {
            return $urls;
        }
        foreach (array_keys($params) as $key) {
            $newParams = $params;
            $newParams[$key] = self::XSS_PAYLOAD;
            $newQuery = http_build_query($newParams);
            $pos = strpos($url, '?');
            $urls[] = ($pos === false ? $url . '?' : substr($url, 0, $pos + 1)) . $newQuery;
        }
        return $urls;
    }

    private static function result(string $name, string $severity, string $url, string $payload, string $description): array
    {
        return [
            'name' => $name,
            'severity' => $severity,
            'url' => $url,
            'payload' => $payload,
            'description' => $description,
        ];
    }
    /** 返回规则库（内置 + 自定义，供管理界面展示） */
    public static function rules(): array
    {
        return array_merge(self::RULES, self::customRules('vuln'));
    }

    /** 返回内置漏洞检测规则（供规则管理界面展示） */
    public static function builtinRules(): array
    {
        return self::RULES;
    }

    /** 返回自定义漏洞检测规则（extend/rules/vuln.php，供规则管理界面展示） */
    public static function customRulesPublic(): array
    {
        return self::customRules('vuln');
    }

    /** 加载自定义规则（extend/rules/vuln.php） */
    private static function customRules(string $name): array
    {
        $file = dirname(__DIR__, 2) . '/extend/rules/' . $name . '.php';
        if (is_file($file)) {
            $rules = @include $file;
            if (is_array($rules)) {
                return $rules;
            }
        }
        return [];
    }
}

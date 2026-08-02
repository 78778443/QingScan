<?php
declare(strict_types=1);

namespace app\scan;

/**
 * 内置 SQL 注入检测引擎（纯 PHP 实现，替代外部 sqlmap 工具）
 * 检测方式：
 *  1. error-based   通过注入错误 payload 触发数据库报错回显
 *  2. boolean-based 通过 AND 1=1 / AND 1=2 两次响应差异判断盲注
 * 依赖 \app\scan\HttpClient 发起请求，可脱离框架独立运行
 */
class SqlInjectScan
{
    /** error-based 探测 payload 集（追加到原参数值后） */
    private const ERROR_PAYLOADS = [
        '`',
        "'",
        '"',
        "')",
        "'-- -",
        "')-- -",
        '"/',
        "' OR 1=1-- -",
    ];

    /**
     * SQL 错误回显特征（pattern => 命中描述）
     * 采用严格特征，避免 PHP 页面自身包含 "syntax error" 等字样造成误报
     */
    private const ERROR_PATTERNS = [
        'You have an error in your SQL syntax' => 'MySQL错误回显',
        'SQLSTATE' => '数据库错误回显',
        'mysql_' => 'MySQL错误回显',
        'ORA-\d{5}' => 'Oracle错误回显',
        'Unclosed quotation mark' => 'MSSQL错误回显',
        'MariaDB server' => 'MariaDB错误回显',
    ];

    /** 布尔盲注差异阈值（字节） */
    private const BOOLEAN_DIFF_MIN = 50;

    /** 布尔盲注 payload */
    private const BOOLEAN_TRUE_PAYLOAD = "' AND 1=1-- -";
    private const BOOLEAN_FALSE_PAYLOAD = "' AND 1=2-- -";

    /**
     * 扫描 URL 中每个 GET 参数是否存在 SQL 注入
     *
     * @param string $url 带 query 参数的 URL
     * @return array 每条 ['url'=>string, 'parameter'=>string, 'payload'=>string, 'result'=>string]
     */
    public static function scan(string $url): array
    {
        $results = [];
        $parts = parse_url($url);
        if (!isset($parts['query']) || $parts['query'] === '') {
            return $results;
        }

        $params = [];
        parse_str($parts['query'], $params);
        if (empty($params)) {
            return $results;
        }

        foreach ($params as $key => $value) {
            $value = (string)$value;
            $hit = null;

            // 1. error-based 检测
            foreach (self::ERROR_PAYLOADS as $payload) {
                $resp = self::requestWithParam($url, (string)$key, $value . $payload);
                if ($resp === null) {
                    // 请求失败，跳过该 payload
                    continue;
                }
                $desc = self::matchError($resp['body']);
                if ($desc !== null) {
                    $hit = [
                        'parameter' => (string)$key,
                        'payload' => $value . $payload,
                        'result' => $desc,
                    ];
                    break;
                }
            }

            // 2. boolean-based 检测（error-based 未命中时才检测）
            if ($hit === null) {
                $boolHit = self::booleanCheck($url, (string)$key, $value);
                if ($boolHit !== null) {
                    $hit = $boolHit;
                }
            }

            if ($hit !== null) {
                $hit['url'] = $url;
                $results[] = $hit;
            }
        }

        return $results;
    }

    /**
     * 去掉 URL 的 query 部分，返回基础地址
     */
    public static function extractBaseUrl(string $url): string
    {
        $parts = parse_url($url);
        $scheme = $parts['scheme'] ?? 'http';
        $host = $parts['host'] ?? '';
        $port = isset($parts['port']) ? ':' . $parts['port'] : '';
        $path = $parts['path'] ?? '/';
        return $scheme . '://' . $host . $port . $path;
    }

    /**
     * 将指定参数替换为新值后发起 GET 请求（保持原参数顺序，其余参数原样保留）
     */
    private static function requestWithParam(string $url, string $key, string $newValue): ?array
    {
        $target = self::buildUrl($url, $key, $newValue);
        if ($target === null) {
            return null;
        }
        return HttpClient::request($target);
    }

    /**
     * 拼接替换参数后的 URL
     */
    private static function buildUrl(string $url, string $key, string $newValue): ?string
    {
        $parts = parse_url($url);
        if (!isset($parts['query']) || $parts['query'] === '') {
            return null;
        }

        $query = '';
        $replaced = false;
        foreach (explode('&', $parts['query']) as $pair) {
            if ($pair === '') {
                continue;
            }
            [$k, $v] = array_pad(explode('=', $pair, 2), 2, '');
            if (urldecode($k) === $key) {
                $v = urlencode($newValue);
                $replaced = true;
            }
            $query .= ($query === '' ? '' : '&') . $k . '=' . $v;
        }
        if (!$replaced) {
            $query .= ($query === '' ? '' : '&') . urlencode($key) . '=' . urlencode($newValue);
        }

        $scheme = $parts['scheme'] ?? 'http';
        $host = $parts['host'] ?? '';
        $port = isset($parts['port']) ? ':' . $parts['port'] : '';
        $path = $parts['path'] ?? '/';
        return $scheme . '://' . $host . $port . $path . '?' . $query;
    }

    /**
     * 检测响应 body 是否包含数据库错误回显特征
     *
     * @return string|null 命中描述，未命中返回 null
     */
    private static function matchError(string $body): ?string
    {
        foreach (self::ERROR_PATTERNS as $pattern => $desc) {
            if (preg_match('/' . $pattern . '/i', $body)) {
                return $desc;
            }
        }
        return null;
    }

    /**
     * 布尔盲注检测：AND 1=1 与 AND 1=2 两次响应 body 长度差超过阈值且状态码相同则命中
     *
     * @return array|null ['parameter'=>string, 'payload'=>string, 'result'=>string]
     */
    private static function booleanCheck(string $url, string $key, string $value): ?array
    {
        $trueResp = self::requestWithParam($url, $key, $value . self::BOOLEAN_TRUE_PAYLOAD);
        $falseResp = self::requestWithParam($url, $key, $value . self::BOOLEAN_FALSE_PAYLOAD);
        if ($trueResp === null || $falseResp === null) {
            return null;
        }
        // 状态码不同则不判定为盲注
        if ($trueResp['code'] !== $falseResp['code']) {
            return null;
        }
        $diff = abs(strlen($trueResp['body']) - strlen($falseResp['body']));
        if ($diff > self::BOOLEAN_DIFF_MIN) {
            return [
                'parameter' => $key,
                'payload' => $value . self::BOOLEAN_FALSE_PAYLOAD,
                'result' => '布尔盲注差异',
            ];
        }
        return null;
    }
}

<?php

namespace app\scan;

/**
 * 自研 WAF 识别引擎（纯 PHP 实现，替代 wafw00f 外部 python 工具）
 * 识别流程：
 *   1. 正常请求基线，记录响应头/Cookie/页面 body（body 截断 5000）
 *   2. 特征规则库匹配（命中即 detected=1）
 *   3. 未命中时主动注入恶意 payload 探测，状态码变化或被拦截特征出现即判定
 * 返回: ['detected'=>int(0/1), 'firewall'=>string, 'manufacturer'=>string]
 */
class WafScan
{
    /**
     * WAF 特征规则库（可扩展：向数组追加元素即可）
     * 规则格式：
     *   headers: 响应头特征，'key' 表示该响应头存在；'key:value' 表示该响应头值包含 value（均不区分大小写）
     *   cookie : Cookie 特征（匹配 set-cookie 中出现的 cookie 名，不区分大小写）
     *   body   : 响应 body 特征关键字（不区分大小写）
     *   firewall/manufacturer: 命中的防火墙名称与厂商
     * 任一特征命中即判定为该防火墙
     */
    private const RULES = [
        // ---- 响应头特征 ----
        ['headers' => ['x-waf'],                 'cookie' => [], 'body' => [], 'firewall' => 'aliyun',       'manufacturer' => '阿里云盾'],
        ['headers' => ['x-powered-by-anquanbao'],'cookie' => [], 'body' => [], 'firewall' => 'anquanbao',    'manufacturer' => '安全宝'],
        ['headers' => ['x-cdn'],                 'cookie' => [], 'body' => [], 'firewall' => 'ChinaCache',   'manufacturer' => '加速乐'],
        ['headers' => ['server:sucuri'],         'cookie' => [], 'body' => [], 'firewall' => 'Sucuri',       'manufacturer' => 'Sucuri'],
        ['headers' => ['x-iss-waf'],             'cookie' => [], 'body' => [], 'firewall' => 'ISSWAF',       'manufacturer' => '艾赛'],
        ['headers' => ['x-ds-waf'],              'cookie' => [], 'body' => [], 'firewall' => 'DS-WAF',       'manufacturer' => '盾'],
        ['headers' => ['x-safedog'],             'cookie' => [], 'body' => [], 'firewall' => 'safedog',      'manufacturer' => '安全狗'],
        // ---- Cookie 特征 ----
        ['headers' => [], 'cookie' => ['yunwaf'],     'body' => [], 'firewall' => 'yunsuo',        'manufacturer' => '云锁'],
        ['headers' => [], 'cookie' => ['__jsluid_s'], 'body' => [], 'firewall' => 'ChinaCache',    'manufacturer' => '加速乐'],
        ['headers' => [], 'cookie' => ['cfduid'],     'body' => [], 'firewall' => 'Cloudflare',    'manufacturer' => 'Cloudflare'],
        ['headers' => [], 'cookie' => ['safedog'],    'body' => [], 'firewall' => 'safedog',       'manufacturer' => '安全狗'],
        ['headers' => [], 'cookie' => ['__cdid'],     'body' => [], 'firewall' => 'ChinaCache',    'manufacturer' => '加速乐'],
        ['headers' => [], 'cookie' => ['_waf'],       'body' => [], 'firewall' => 'unknown',       'manufacturer' => '通用WAF'],
        ['headers' => [], 'cookie' => ['ns_af'],      'body' => [], 'firewall' => 'ChinaNetCenter', 'manufacturer' => '网宿'],
        ['headers' => [], 'cookie' => ['yd_cookie'],  'body' => [], 'firewall' => 'aliyun',        'manufacturer' => '云盾'],
        // ---- body 特征 ----
        ['headers' => [], 'cookie' => [], 'body' => ['mod_security'],             'firewall' => 'ModSecurity',  'manufacturer' => 'ModSecurity'],
        ['headers' => [], 'cookie' => [], 'body' => ['360wzws'],                  'firewall' => '360',          'manufacturer' => '360网站卫士'],
        ['headers' => [], 'cookie' => [], 'body' => ['safedog'],                  'firewall' => 'safedog',      'manufacturer' => '安全狗'],
        ['headers' => [], 'cookie' => [], 'body' => ['yundun'],                   'firewall' => 'aliyun',       'manufacturer' => '阿里云盾'],
        ['headers' => [], 'cookie' => [], 'body' => ['wangzhan.360'],             'firewall' => '360',          'manufacturer' => '360'],
        ['headers' => [], 'cookie' => [], 'body' => ['web application firewall'], 'firewall' => 'unknown',      'manufacturer' => '通用WAF'],
        ['headers' => [], 'cookie' => [], 'body' => ['__jsl'],                    'firewall' => 'ChinaCache',   'manufacturer' => '加速乐'],
    ];

    /** 主动探测：URL 已有参数时追加的恶意 payload */
    private const PROBE_PARAM = 'id=1%27%20AND%201%3D1--%20';

    /** 主动探测：URL 无参数时注入的恶意参数 */
    private const PROBE_PARAM_NOQUERY = "a=1%27";

    /** 主动探测后响应状态码落入该集合（且与基线不同）即视为被 WAF 拦截 */
    private const BLOCK_CODES = [403, 406, 503];

    /** 主动探测后响应 body 含以下关键字即视为被 WAF 拦截 */
    private const BLOCK_BODY_KEYWORDS = ['forbidden', 'blocked', '拦截', 'attack', '安全', '验证'];

    /** 基线/探测响应 body 截断长度 */
    private const BODY_LIMIT = 5000;

    /**
     * WAF 识别入口
     */
    public static function scan(string $url): array
    {
        $baseline = HttpClient::request($url);
        if ($baseline === null) {
            return ['detected' => 0, 'firewall' => '', 'manufacturer' => ''];
        }

        $headers = $baseline['headers'] ?? [];
        $body = substr((string)($baseline['body'] ?? ''), 0, self::BODY_LIMIT);

        // 1. 响应头 / Cookie / body 特征检测
        foreach (self::RULES as $rule) {
            if (self::matchHeaders($headers, $rule['headers'])
                || self::matchCookies($headers, $rule['cookie'])
                || self::matchBody($body, $rule['body'])) {
                return [
                    'detected'    => 1,
                    'firewall'    => $rule['firewall'],
                    'manufacturer' => $rule['manufacturer'],
                ];
            }
        }

        // 2. 主动探测（特征未命中时）
        return self::activeProbe($url, $baseline);
    }

    /**
     * 主动探测：注入恶意参数再请求一次，状态码变为拦截码或 body 出现拦截特征即判定有 WAF
     */
    private static function activeProbe(string $url, array $baseline): array
    {
        $probeUrl = (strpos($url, '?') !== false)
            ? $url . '&' . self::PROBE_PARAM
            : $url . '?' . self::PROBE_PARAM_NOQUERY;

        $probe = HttpClient::request($probeUrl);
        if ($probe === null) {
            return ['detected' => 0, 'firewall' => '', 'manufacturer' => ''];
        }

        $baseCode = (int)($baseline['code'] ?? 0);
        $probeCode = (int)($probe['code'] ?? 0);

        // 状态码变化为 403/406/503（与基线不同）视为被拦截
        if (in_array($probeCode, self::BLOCK_CODES, true) && $probeCode !== $baseCode) {
            return ['detected' => 1, 'firewall' => 'unknown', 'manufacturer' => '未知WAF'];
        }

        // body 出现基线中不存在的拦截关键字视为被拦截（避免站点自身页面含该词造成误报）
        $baseBody = substr((string)($baseline['body'] ?? ''), 0, self::BODY_LIMIT);
        $probeBody = substr((string)($probe['body'] ?? ''), 0, self::BODY_LIMIT);
        foreach (self::BLOCK_BODY_KEYWORDS as $keyword) {
            $keyword = strtolower($keyword);
            if (strpos(strtolower($baseBody), $keyword) !== false) {
                continue;
            }
            if (strpos(strtolower($probeBody), $keyword) !== false) {
                return ['detected' => 1, 'firewall' => 'unknown', 'manufacturer' => '未知WAF'];
            }
        }

        return ['detected' => 0, 'firewall' => '', 'manufacturer' => ''];
    }

    /**
     * 响应头特征匹配：'key' 存在即命中；'key:value' 需值包含 value
     */
    private static function matchHeaders(array $headers, array $patterns): bool
    {
        foreach ($patterns as $pattern) {
            $pattern = strtolower($pattern);
            if (strpos($pattern, ':') !== false) {
                [$key, $value] = explode(':', $pattern, 2);
                if (isset($headers[$key]) && strpos(strtolower((string)$headers[$key]), $value) !== false) {
                    return true;
                }
            } elseif (isset($headers[$pattern])) {
                return true;
            }
        }
        return false;
    }

    /**
     * Cookie 特征匹配：在 set-cookie 响应头中查找 cookie 名
     */
    private static function matchCookies(array $headers, array $names): bool
    {
        if (empty($names) || empty($headers['set-cookie'])) {
            return false;
        }
        $setCookie = strtolower((string)$headers['set-cookie']);
        foreach ($names as $name) {
            if (strpos($setCookie, strtolower($name)) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * body 特征关键字匹配（不区分大小写）
     */
    private static function matchBody(string $body, array $keywords): bool
    {
        if (empty($keywords)) {
            return false;
        }
        $body = strtolower($body);
        foreach ($keywords as $keyword) {
            if (strpos($body, strtolower($keyword)) !== false) {
                return true;
            }
        }
        return false;
    }
}

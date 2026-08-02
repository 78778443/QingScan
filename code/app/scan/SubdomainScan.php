<?php
declare(strict_types=1);

namespace app\scan;

/**
 * 内置 PHP 子域名枚举引擎（替代外部子域名收集工具调用）
 */
class SubdomainScan
{
    /**
     * 枚举子域名：对字典逐条查询 DNS A 记录，仅保留有 A 记录（IPv4）的条目
     *
     * @param string $domain 根域名，如 qq.com
     * @return array 每条记录结构：
     *               [
     *                   'subdomain' => string, 完整子域名 host
     *                   'ip'        => string, 解析出的 IPv4
     *                   'cname'     => string|null, CNAME 目标（若有）
     *                   'resolve'   => string, 解析结果（同 ip）
     *                   'url'       => string, http://host
     *                   'level'     => int, 子域名层级数（如 a.b.domain 为 3）
     *               ]
     */
    public static function scan(string $domain): array
    {
        $domain = strtolower(trim($domain));
        $domain = ltrim($domain, '.');
        if ($domain === '' || !preg_match('/^[a-z0-9]([a-z0-9.-]*[a-z0-9])?$/', $domain)) {
            return [];
        }

        $result = [];
        foreach (Dicts::subdomains() as $prefix) {
            $prefix = trim((string)$prefix);
            if ($prefix === '') {
                continue;
            }
            $host = $prefix . '.' . $domain;

            // 查询 A 记录，失败或为空时静默跳过
            $aRecords = @dns_get_record($host, DNS_A);
            if ($aRecords === false || empty($aRecords)) {
                continue;
            }

            // 取第一条 A 记录的 IPv4（过滤 IPv6 等非法地址）
            $ip = '';
            foreach ($aRecords as $rec) {
                if (isset($rec['type']) && strtoupper($rec['type']) === 'A'
                    && isset($rec['ip'])
                    && filter_var($rec['ip'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)
                ) {
                    $ip = $rec['ip'];
                    break;
                }
            }
            if ($ip === '') {
                continue;
            }

            // 查询 CNAME 记录（若有）
            $cname = null;
            $cRecords = @dns_get_record($host, DNS_CNAME);
            if ($cRecords !== false && !empty($cRecords)) {
                foreach ($cRecords as $rec) {
                    if (isset($rec['type']) && strtoupper($rec['type']) === 'CNAME'
                        && isset($rec['target']) && $rec['target'] !== ''
                    ) {
                        $cname = rtrim($rec['target'], '.');
                        break;
                    }
                }
            }

            $result[] = [
                'subdomain' => $host,
                'ip'        => $ip,
                'cname'     => $cname,
                'resolve'   => $ip,
                'url'       => 'http://' . $host,
                'level'     => substr_count($host, '.') + 1,
            ];
        }
        return $result;
    }

    /**
     * 从 URL 提取根域名（参考原 SubdomainModel 逻辑：取 host、过滤 IP、去 www 前缀）
     *
     * @param string $url
     * @return string 根域名；无法解析（IP 或空 host）时返回空字符串
     */
    public static function extractDomain(string $url): string
    {
        $host = (string)parse_url($url, PHP_URL_HOST);
        if ($host === '' || filter_var($host, FILTER_VALIDATE_IP)) {
            return '';
        }
        $hostArr = explode('.', $host);
        // 去 www 前缀
        if (isset($hostArr[0]) && strtolower($hostArr[0]) === 'www') {
            array_shift($hostArr);
        }
        return implode('.', $hostArr);
    }
}

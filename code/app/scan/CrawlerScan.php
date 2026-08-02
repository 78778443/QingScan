<?php
declare(strict_types=1);

namespace app\scan;

/**
 * 内置 Web 爬虫引擎（替代 rad / crawlergo 外部浏览器爬虫）
 *
 * 基础爬虫：正则提取 a / iframe / form 标签中的链接，
 * BFS 递归抓取同域 URL，不做 JS 渲染（相比浏览器爬虫为降级方案）。
 */
class CrawlerScan
{
    /** 黑名单扩展名：静态资源不抓取 */
    private const BLACK_EXT = [
        '.js', '.css', '.png', '.jpg', '.jpeg', '.gif', '.mp3', '.mp4',
        '.ico', '.bmp', '.wmv', '.avi', '.psd', '.svg', '.woff', '.woff2',
        '.ttf', '.eot', '.map',
    ];

    /**
     * 爬取目标 URL，返回页面中出现的同域链接列表
     *
     * @param string $url      起始 URL
     * @param int    $maxDepth 最大抓取深度（0 表示仅抓取起始页）
     * @param int    $maxUrls  最多返回的 URL 数量
     * @return array<int, array{url:string, method:string}> method: get/post
     */
    public static function crawl(string $url, int $maxDepth = 2, int $maxUrls = 50): array
    {
        $targetHost = strtolower((string)parse_url($url, PHP_URL_HOST));
        if ($targetHost === '') {
            return [];
        }

        $result = [];   // 最终结果
        $seen = [];     // 已处理 URL 去重
        $queue = [[$url, 'get', 0]]; // [url, method, depth]

        while ($queue && count($result) < $maxUrls) {
            [$curUrl, $curMethod, $depth] = array_shift($queue);

            $norm = self::normalize($curUrl, $targetHost);
            if ($norm === null || isset($seen[$norm])) {
                continue;
            }
            $seen[$norm] = true;
            // 用规范化后的 URL（已去 # 锚点）
            $result[] = ['url' => $norm, 'method' => $curMethod];

            // 达到深度或数量上限则不再递归抓取
            if ($depth >= $maxDepth || count($result) >= $maxUrls) {
                continue;
            }

            // 单次请求超时 8s，抓取失败直接跳过
            $response = HttpClient::request($curUrl, ['timeout' => 8]);
            if ($response === null) {
                continue;
            }
            $body = $response['body'] ?? '';

            foreach (self::extractLinks($body) as $link => $method) {
                // 协议相对地址（//host/path）补全协议
                if (str_starts_with($link, '//')) {
                    $link = (parse_url($curUrl, PHP_URL_SCHEME) ?: 'http') . ':' . $link;
                }
                $abs = HttpClient::resolveUrl($curUrl, $link);
                $n = self::normalize($abs, $targetHost);
                if ($n !== null && !isset($seen[$n])) {
                    $queue[] = [$n, $method, $depth + 1];
                }
            }
        }

        return array_slice($result, 0, $maxUrls);
    }

    /**
     * 提取页面中的链接：a[href] / iframe[src] / form[action]
     *
     * @return array<string, string> link => method
     */
    private static function extractLinks(string $body): array
    {
        $links = [];

        if (preg_match_all('/<a[^>]+href=["\']([^"\']+)["\']/i', $body, $m)) {
            foreach ($m[1] as $href) {
                $links[trim($href)] = 'get';
            }
        }
        if (preg_match_all('/<iframe[^>]+src=["\']([^"\']+)["\']/i', $body, $m)) {
            foreach ($m[1] as $src) {
                $links[trim($src)] = 'get';
            }
        }
        // form 解析整个标签，提取 action 与 method（默认 get，post 表单记为 post）
        if (preg_match_all('/<form[^>]*>/i', $body, $forms)) {
            foreach ($forms[0] as $form) {
                if (preg_match('/action=["\']([^"\']+)["\']/i', $form, $am)) {
                    $method = 'get';
                    if (preg_match('/method=["\']([^"\']+)["\']/i', $form, $mm) && strtolower($mm[1]) === 'post') {
                        $method = 'post';
                    }
                    $links[trim($am[1])] = $method;
                }
            }
        }

        return $links;
    }

    /**
     * 规范化并过滤 URL：仅保留同域 http/https、去掉 # 锚点、过滤黑名单扩展名
     *
     * @return string|null 通过返回原 URL，否则返回 null
     */
    private static function normalize(string $url, string $targetHost): ?string
    {
        $url = html_entity_decode(trim($url), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        // 去掉 # 锚点
        $pos = strpos($url, '#');
        if ($pos !== false) {
            $url = substr($url, 0, $pos);
        }
        if ($url === '') {
            return null;
        }

        $parts = parse_url($url);
        $scheme = isset($parts['scheme']) ? strtolower($parts['scheme']) : '';
        $host = isset($parts['host']) ? strtolower($parts['host']) : '';
        if (!in_array($scheme, ['http', 'https'], true) || $host !== $targetHost) {
            return null;
        }

        // 黑名单扩展名过滤（参照原 rad 的路径匹配逻辑）
        $path = isset($parts['path']) ? strtolower($parts['path']) : '';
        if ($path !== '') {
            foreach (self::BLACK_EXT as $ext) {
                if (strpos($path, $ext) !== false) {
                    return null;
                }
            }
        }

        return $url;
    }
}

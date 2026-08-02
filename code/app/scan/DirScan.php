<?php
declare(strict_types=1);

namespace app\scan;

/**
 * 内置目录扫描引擎
 * 纯 PHP 实现，替换外部 dirmap(python) 工具调用
 */
class DirScan
{
    /**
     * 目录扫描
     *
     * @param string     $url  目标 URL，可能已带路径或端口
     * @param array|null $dict 字典，默认 \app\scan\Dicts::dirs()
     * @return array [['url'=>string, 'code'=>int, 'type'=>string, 'size'=>string], ...] 按 code 升序
     */
    public static function scan(string $url, ?array $dict = null): array
    {
        $dict = $dict ?: Dicts::dirs();
        // 字典存在重复项，去重避免重复请求
        $dict = array_values(array_unique($dict));

        // $url 末尾补 '/' 后拼接路径（$url 可能已带路径或端口）
        $base = rtrim($url, '/') . '/';

        // 自定义 404 过滤：先请求 $url 作为基线，
        // 后续路径若 body 与基线完全相同且 code 相同则视为统一 404 页面跳过
        $baseline = HttpClient::request($url, ['timeout' => 8, 'method' => 'GET']);

        $results = [];
        foreach ($dict as $path) {
            $resp = HttpClient::request($base . $path, ['timeout' => 8, 'method' => 'GET']);
            if ($resp === null) {
                // HttpClient 请求失败（超时/连接失败）跳过
                continue;
            }
            $code = (int)$resp['code'];
            // code >= 400 跳过
            if ($code >= 400) {
                continue;
            }
            // 与基线完全相同（code + body）视为统一 404 页面，跳过
            if ($baseline !== null && $code === (int)$baseline['code'] && $resp['body'] === $baseline['body']) {
                continue;
            }
            // type 取响应头 content-type，截断 50 字符
            $type = substr($resp['headers']['content-type'] ?? '', 0, 50);
            // size 为 body 长度转 KB，保留 1 位小数
            $size = number_format(strlen($resp['body']) / 1024, 1);
            $results[] = [
                'url' => $base . $path,
                'code' => $code,
                'type' => $type,
                'size' => $size,
            ];
        }

        // 按 code 升序
        usort($results, static function ($a, $b) {
            return $a['code'] <=> $b['code'];
        });

        return $results;
    }
}

<?php
declare(strict_types=1);

namespace app\scan;

/**
 * 内置 Web 指纹识别引擎（替代 whatweb / dismap / Finger.py 外部工具）
 */
class FingerScan
{
    /**
     * 对单个 URL 进行指纹识别
     * 返回: ['code'=>int, 'title'=>string|null, 'server'=>string|null,
     *        'headers'=>[k=>v], 'fingerprints'=>[string...], 'body_preview'=>string(前2000字符)]
     * 请求失败时返回空结果数组（code=0, 其余为空）
     */
    public static function scan(string $url): array
    {
        $empty = [
            'code' => 0,
            'title' => null,
            'server' => null,
            'headers' => [],
            'fingerprints' => [],
            'body_preview' => '',
        ];

        $response = HttpClient::request($url);
        if ($response === null) {
            return $empty;
        }

        $headers = $response['headers'] ?? [];
        $body = $response['body'] ?? '';
        $bodyPreview = mb_substr($body, 0, 2000);
        $fingerprints = [];

        foreach (Dicts::fingerprints() as $rule) {
            $hit = false;
            // headers 规则：对应 key 存在且值包含规则值（规则值为空串时只要 key 存在即命中）
            foreach (($rule['headers'] ?? []) as $key => $value) {
                $k = strtolower((string)$key);
                if (isset($headers[$k])) {
                    if ($value === '' || $value === null || stripos($headers[$k], (string)$value) !== false) {
                        $hit = true;
                        break;
                    }
                }
            }
            // body 规则：body_preview 中大小写不敏感匹配
            if (!$hit) {
                foreach (($rule['body'] ?? []) as $pattern) {
                    if (preg_match('/' . preg_quote((string)$pattern, '/') . '/i', $bodyPreview)) {
                        $hit = true;
                        break;
                    }
                }
            }
            if ($hit) {
                $fingerprints[] = $rule['name'];
            }
        }

        return [
            'code' => (int)($response['code'] ?? 0),
            'title' => $response['title'] ?? null,
            'server' => $headers['server'] ?? null,
            'headers' => $headers,
            'fingerprints' => array_values(array_unique($fingerprints)),
            'body_preview' => $bodyPreview,
        ];
    }

    /**
     * 批量指纹识别（简单循环，供调度方使用）
     * 返回: ['url1' => 结果数组, 'url2' => 结果数组, ...]
     */
    public static function scanMany(array $urls): array
    {
        $results = [];
        foreach ($urls as $url) {
            $results[$url] = self::scan($url);
        }
        return $results;
    }
}

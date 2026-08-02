<?php
declare(strict_types=1);

namespace app\scan;

/**
 * 内置扫描引擎共用的 HTTP 请求封装
 * 输出: ['code'=>int, 'headers'=>[k=>v], 'body'=>string, 'title'=>string|null] 失败返回 null
 */
class HttpClient
{
    public static function request(string $url, array $options = []): ?array
    {
        $timeout = $options['timeout'] ?? 10;
        $maxRedirect = $options['maxRedirect'] ?? 3;
        $method = $options['method'] ?? 'GET';
        $sendHeaders = $options['headers'] ?? [];
        $userAgent = $options['userAgent'] ?? 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0 Safari/537.36';

        if (!function_exists('curl_init')) {
            return self::requestStream($url, $timeout, $method, $sendHeaders, $userAgent, $maxRedirect);
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => $maxRedirect,
            CURLOPT_CONNECTTIMEOUT => min($timeout, 5),
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_USERAGENT => $userAgent,
            CURLOPT_HTTPHEADER => array_merge(['Accept: */*'], $sendHeaders),
            CURLOPT_HEADER => true,
        ]);
        if ($method === 'HEAD') {
            curl_setopt($ch, CURLOPT_NOBODY, true);
        }
        $response = curl_exec($ch);
        if ($response === false) {
            curl_close($ch);
            return null;
        }
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $headerText = substr($response, 0, $headerSize);
        $body = substr($response, $headerSize);
        $headers = self::parseHeaders($headerText);
        if (isset($headers['location']) && $code >= 300 && $code < 400) {
            $target = self::resolveUrl($url, $headers['location']);
            $redirect = self::request($target, ['timeout' => $timeout, 'maxRedirect' => $maxRedirect - 1]);
            if ($redirect) {
                return $redirect;
            }
        }

        return [
            'code' => $code,
            'headers' => $headers,
            'body' => $body,
            'title' => self::extractTitle($body),
        ];
    }

    private static function requestStream(string $url, int $timeout, string $method, array $headers, string $userAgent, int $maxRedirect): ?array
    {
        $ctx = stream_context_create([
            'http' => [
                'method' => $method,
                'timeout' => $timeout,
                'follow_location' => $maxRedirect > 0 ? 1 : 0,
                'max_redirects' => $maxRedirect,
                'ignore_errors' => true,
                'user_agent' => $userAgent,
                'header' => array_merge(['Accept: */*'], $headers),
            ],
            'ssl' => ['verify_peer' => false, 'verify_peer_name' => false],
        ]);
        $body = @file_get_contents($url, false, $ctx);
        if ($body === false) {
            return null;
        }
        $statusLine = $http_response_header[0] ?? '';
        preg_match('#HTTP/\S+\s+(\d+)#', $statusLine, $m);
        $headers = [];
        foreach ($http_response_header as $line) {
            if (strpos($line, ':') !== false) {
                [$k, $v] = explode(':', $line, 2);
                $headers[strtolower(trim($k))] = trim($v);
            }
        }
        return [
            'code' => (int)($m[1] ?? 0),
            'headers' => $headers,
            'body' => $body,
            'title' => self::extractTitle($body),
        ];
    }

    private static function parseHeaders(string $headerText): array
    {
        $headers = [];
        foreach (explode("\r\n", $headerText) as $line) {
            if (strpos($line, ':') !== false) {
                [$k, $v] = explode(':', $line, 2);
                $headers[strtolower(trim($k))] = trim($v);
            }
        }
        return $headers;
    }

    public static function extractTitle(string $body): ?string
    {
        if (preg_match('/<title[^>]*>([^<]{1,200})<\/title>/is', $body, $m)) {
            return trim($m[1]);
        }
        return null;
    }

    public static function resolveUrl(string $base, string $location): string
    {
        if (str_starts_with($location, 'http://') || str_starts_with($location, 'https://')) {
            return $location;
        }
        $parts = parse_url($base);
        $scheme = $parts['scheme'] ?? 'http';
        $host = $parts['host'] ?? '';
        $port = isset($parts['port']) ? ':' . $parts['port'] : '';
        $path = $parts['path'] ?? '/';
        if (str_starts_with($location, '/')) {
            return "{$scheme}://{$host}{$port}{$location}";
        }
        $dir = substr($path, 0, strrpos($path, '/') + 1);
        return "{$scheme}://{$host}{$port}{$dir}{$location}";
    }
}

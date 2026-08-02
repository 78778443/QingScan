<?php
declare(strict_types=1);

namespace app\scan;

/**
 * 内置端口扫描引擎，替代 nmap/masscan 外部工具调用
 */
class PortScan
{
    /**
     * 扫描指定 IP 的端口列表，返回所有开放端口及服务识别结果
     *
     * @param string $ip      目标 IP 或主机名
     * @param array  $ports   待扫描端口列表，如 [21, 22, 80]
     * @param float  $timeout 单个端口连接超时时间（秒）
     * @return array 形如 [['port' => int, 'service' => string], ...]，仅包含开放的端口
     */
    public static function scan(string $ip, array $ports, float $timeout = 1.0): array
    {
        $result = [];
        foreach ($ports as $port) {
            $port = (int)$port;

            $fp = @stream_socket_client(
                "tcp://{$ip}:{$port}",
                $errno,
                $errstr,
                (float)$timeout
            );

            // 连接失败（超时/拒绝/不可达）则跳过该端口
            if (!$fp || $errno != 0) {
                if (is_resource($fp)) {
                    fclose($fp);
                }
                continue;
            }

            $service = self::getService($port, $fp);
            fclose($fp);

            $result[] = ['port' => $port, 'service' => $service];
        }

        return $result;
    }

    /**
     * 读取 banner 并识别服务，无 banner 或无法识别时用端口默认服务兜底
     *
     * @param int      $port
     * @param resource $fp
     * @return string
     */
    private static function getService(int $port, $fp): string
    {
        // 非阻塞读取 banner，最多等待 0.2 秒，读不到不报错
        stream_set_blocking($fp, false);
        stream_set_timeout($fp, 0, 200000);

        $banner = '';
        $endTime = microtime(true) + 0.2;
        while (microtime(true) < $endTime) {
            $chunk = @fread($fp, 1024);
            if ($chunk === false) {
                break;
            }
            if ($chunk === '') {
                // 暂无数据，稍等片刻再试
                usleep(10000);
                continue;
            }
            $banner .= $chunk;
            if (strlen($banner) >= 1024) {
                break;
            }
        }

        $service = self::matchService($banner, $port);
        if ($service !== '') {
            return $service;
        }

        return Dicts::portServices()[$port] ?? 'unknown';
    }

    /**
     * 根据 banner 内容匹配常见服务
     *
     * @param string $banner
     * @param int    $port
     * @return string 匹配到的服务名，未匹配返回空字符串
     */
    private static function matchService(string $banner, int $port): string
    {
        if ($banner === '') {
            return '';
        }

        if (preg_match('/^SSH-\d/', $banner)) {
            return 'ssh';
        }
        if (preg_match('/^220.*smtp/i', $banner)) {
            return 'smtp';
        }
        if (preg_match('/^220.*ftp/i', $banner)) {
            return 'ftp';
        }
        if (preg_match('/^HTTP\//i', $banner)) {
            return $port === 443 ? 'https' : 'http';
        }
        // MySQL 握手包（4.x 以 \x05\x00\x00\x01 开头；4.1+ 第4字节为协议版本 0x0a 且含版本号）或 MariaDB 标识
        $isMysql = preg_match('/^\x05\x00\x00\x01/', $banner)
            || (isset($banner[3]) && ord($banner[3]) === 0x0a && preg_match('/\d+\.\d+\.\d+/', $banner));
        if ($isMysql || stripos($banner, 'MariaDB') !== false) {
            return 'mysql';
        }
        if (preg_match('/^\+OK/i', $banner)) {
            return 'pop3';
        }
        if (preg_match('/^\*OK/i', $banner)) {
            return 'imap';
        }

        return '';
    }
}

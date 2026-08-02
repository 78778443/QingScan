<?php
declare(strict_types=1);

namespace app\scan;

/**
 * 内置弱口令爆破引擎，替代 hydra 外部工具调用
 *
 * 纯 PHP 实现（socket 直连），禁止调用任何外部工具/命令。
 * 支持协议：ftp、telnet、mysql（mysql_native_password）、redis。
 * 不支持 ssh 等其它协议时静默跳过并记录日志。
 */
class BruteScan
{
    /** 每个端口最多尝试的账号密码组合数 */
    private const MAX_ATTEMPTS = 20;

    /** 单次连接/读写超时时间（秒） */
    private const TIMEOUT = 3;

    /**
     * 对目标主机开放端口进行弱口令爆破
     *
     * @param string      $host      目标 IP 或主机名
     * @param array       $ports     待爆破端口列表，如 [21, 23, 3306, 6379]
     * @param array|null  $usernames 用户名列表，null 使用内置字典
     * @param array|null  $passwords 密码列表，null 使用内置字典
     * @return array 形如 [['port'=>int, 'service'=>string, 'username'=>string, 'password'=>string], ...]
     */
    public static function scan(string $host, array $ports, ?array $usernames = null, ?array $passwords = null): array
    {
        $usernames = self::normalizeDict($usernames, Dicts::usernames());
        $passwords = self::normalizeDict($passwords, Dicts::passwords());

        $results = [];
        foreach ($ports as $port) {
            $port = (int)$port;
            if ($port <= 0) {
                continue;
            }

            // 先连接读取 banner 识别协议，识别失败用端口默认服务兜底
            $service = self::detectService($host, $port);
            if ($service === 'ssh') {
                self::log("主机 {$host} 端口 {$port} 为 SSH 服务，内置引擎暂不支持 SSH 爆破，跳过");
                continue;
            }

            switch ($service) {
                case 'ftp':
                    $results = array_merge($results, self::bruteFtp($host, $port, $usernames, $passwords));
                    break;
                case 'telnet':
                    $results = array_merge($results, self::bruteTelnet($host, $port, $usernames, $passwords));
                    break;
                case 'mysql':
                    $results = array_merge($results, self::bruteMysql($host, $port, $usernames, $passwords));
                    break;
                case 'redis':
                    $results = array_merge($results, self::bruteRedis($host, $port, $passwords));
                    break;
                default:
                    self::log("主机 {$host} 端口 {$port} 服务「{$service}」暂不支持爆破，跳过");
            }
        }

        return $results;
    }

    /**
     * 连接端口并读取 banner 识别服务，识别失败用端口默认服务兜底
     */
    private static function detectService(string $host, int $port): string
    {
        $fp = @stream_socket_client("tcp://{$host}:{$port}", $errno, $errstr, self::TIMEOUT);
        if (!$fp) {
            if (is_resource($fp)) {
                fclose($fp);
            }
            return '';
        }
        stream_set_timeout($fp, self::TIMEOUT);
        stream_set_blocking($fp, false);

        $banner = '';
        $endTime = microtime(true) + 0.6;
        while (microtime(true) < $endTime) {
            $chunk = @fread($fp, 1024);
            if ($chunk === false || $chunk === '') {
                $meta = stream_get_meta_data($fp);
                if (!empty($meta['timed_out'])) {
                    break;
                }
                usleep(50000);
                continue;
            }
            $banner .= $chunk;
            if (strlen($banner) >= 1024) {
                break;
            }
        }
        fclose($fp);

        // banner 特征识别，注意顺序：SSH > FTP(220) > MySQL > Redis
        if (stripos($banner, 'SSH') !== false) {
            return 'ssh';
        }
        if (strpos($banner, '220') === 0) {
            return 'ftp';
        }
        if (stripos($banner, 'MariaDB') !== false || stripos($banner, 'mysql') !== false) {
            return 'mysql';
        }
        if (stripos($banner, 'Redis') !== false) {
            return 'redis';
        }

        // 无 banner 或无法识别时按端口默认服务兜底
        return Dicts::portServices()[$port] ?? '';
    }

    /**
     * 生成爆破候选：同名密码（u/u、u/123、u/123456）优先，再走完整字典
     */
    private static function candidates(array $usernames, array $passwords): array
    {
        $list = [];
        foreach ($usernames as $u) {
            $list[] = [$u, $u];
            if (in_array($u . '123', $passwords, true)) {
                $list[] = [$u, $u . '123'];
            }
            if (in_array($u . '123456', $passwords, true)) {
                $list[] = [$u, $u . '123456'];
            }
        }
        foreach ($usernames as $u) {
            foreach ($passwords as $p) {
                $list[] = [$u, $p];
            }
        }
        return $list;
    }

    /**
     * FTP 爆破：USER -> 331 -> PASS -> 230 即成功
     */
    private static function bruteFtp(string $host, int $port, array $usernames, array $passwords): array
    {
        $found = [];
        $attempts = 0;
        foreach (self::candidates($usernames, $passwords) as [$user, $pass]) {
            if ($attempts >= self::MAX_ATTEMPTS) {
                break;
            }
            $attempts++;
            if (self::ftpTry($host, $port, $user, $pass)) {
                $found[] = ['port' => $port, 'service' => 'ftp', 'username' => $user, 'password' => $pass];
            }
        }
        return $found;
    }

    private static function ftpTry(string $host, int $port, string $user, string $pass): bool
    {
        $fp = @stream_socket_client("tcp://{$host}:{$port}", $errno, $errstr, self::TIMEOUT);
        if (!$fp) {
            return false;
        }
        stream_set_timeout($fp, self::TIMEOUT);

        $banner = self::readLine($fp);
        if ($banner === null || strpos($banner, '220') !== 0) {
            fclose($fp);
            return false;
        }

        fwrite($fp, "USER {$user}\r\n");
        $userResp = self::readLine($fp);
        if ($userResp === null || strpos($userResp, '331') !== 0) {
            fclose($fp);
            return false;
        }

        fwrite($fp, "PASS {$pass}\r\n");
        $passResp = self::readLine($fp);
        fclose($fp);

        // 230 表示登录成功，530 表示失败
        return $passResp !== null && strpos($passResp, '230') === 0;
    }

    /**
     * Telnet 爆破：依次发送用户名/密码，响应含 shell 提示符特征即判成功（启发式）
     */
    private static function bruteTelnet(string $host, int $port, array $usernames, array $passwords): array
    {
        $found = [];
        $attempts = 0;
        foreach (self::candidates($usernames, $passwords) as [$user, $pass]) {
            if ($attempts >= self::MAX_ATTEMPTS) {
                break;
            }
            $attempts++;
            if (self::telnetTry($host, $port, $user, $pass)) {
                $found[] = ['port' => $port, 'service' => 'telnet', 'username' => $user, 'password' => $pass];
            }
        }
        return $found;
    }

    private static function telnetTry(string $host, int $port, string $user, string $pass): bool
    {
        $fp = @stream_socket_client("tcp://{$host}:{$port}", $errno, $errstr, self::TIMEOUT);
        if (!$fp) {
            return false;
        }
        stream_set_timeout($fp, self::TIMEOUT);

        // 发送用户名
        fwrite($fp, "{$user}\r\n");
        $resp = self::readLine($fp) ?? '';
        // 发送密码
        fwrite($fp, "{$pass}\r\n");

        // 继续读取响应（可能多行），最多等待 1.5 秒
        $endTime = microtime(true) + 1.5;
        while (microtime(true) < $endTime) {
            $chunk = @fread($fp, 4096);
            if ($chunk === false || $chunk === '') {
                $meta = stream_get_meta_data($fp);
                if (!empty($meta['timed_out'])) {
                    break;
                }
                usleep(50000);
                continue;
            }
            $resp .= $chunk;
            if (strlen($resp) >= 8192) {
                break;
            }
        }
        fclose($fp);

        return self::isShellPrompt($resp);
    }

    /**
     * 判断 telnet 响应是否为登录成功后的 shell 提示符：
     * 不含失败关键字且包含 #、$、> 之一
     */
    private static function isShellPrompt(string $resp): bool
    {
        if ($resp === '' || strpos($resp, "\0") !== false) {
            return false;
        }
        $lower = strtolower($resp);
        foreach (['incorrect', 'invalid', 'failed', 'denied', 'password:', 'login:'] as $bad) {
            if (strpos($lower, $bad) !== false) {
                return false;
            }
        }
        return strpos($resp, '#') !== false || strpos($resp, '$') !== false || strpos($resp, '>') !== false;
    }

    /**
     * MySQL 爆破：实现 mysql_native_password 握手认证，
     * MySQL 8 caching_sha2_password（0xFE 认证切换）直接判失败跳过
     */
    private static function bruteMysql(string $host, int $port, array $usernames, array $passwords): array
    {
        $found = [];
        $attempts = 0;
        foreach (self::candidates($usernames, $passwords) as [$user, $pass]) {
            if ($attempts >= self::MAX_ATTEMPTS) {
                break;
            }
            $attempts++;
            if (self::mysqlTry($host, $port, $user, $pass)) {
                $found[] = ['port' => $port, 'service' => 'mysql', 'username' => $user, 'password' => $pass];
            }
        }
        return $found;
    }

    private static function mysqlTry(string $host, int $port, string $user, string $pass): bool
    {
        $fp = @stream_socket_client("tcp://{$host}:{$port}", $errno, $errstr, self::TIMEOUT);
        if (!$fp) {
            return false;
        }
        stream_set_timeout($fp, self::TIMEOUT);

        // 读取服务端握手包
        $handshake = self::mysqlReadPacket($fp);
        if ($handshake === null) {
            fclose($fp);
            return false;
        }
        $payload = substr($handshake, 4);
        if ($payload === '') {
            fclose($fp);
            return false;
        }

        // 解析握手包：协议版本 + 服务器版本 + 连接ID + salt + 能力位 + salt2
        $pos = 0;
        $protoVer = ord($payload[$pos] ?? "\0");
        if ($protoVer === 0xFF) { // 服务端直接返回错误
            fclose($fp);
            return false;
        }
        $pos++;
        $end = strpos($payload, "\0", $pos);
        if ($end === false) {
            fclose($fp);
            return false;
        }
        $pos = $end + 1;          // 跳过服务器版本
        $pos += 4;                // 跳过连接 ID
        $salt1 = substr($payload, $pos, 8);
        $pos += 8;
        $pos += 1;                // 跳过填充字节
        if (strlen($payload) <= $pos + 10) {
            fclose($fp);
            return false;
        }
        $capLow = unpack('v', substr($payload, $pos, 2))[1];
        $pos += 2;                // 能力位低位
        $pos += 1;                // 字符集
        $pos += 2;                // 状态位
        $capHigh = unpack('v', substr($payload, $pos, 2))[1];
        $pos += 2;                // 能力位高位
        $authLen = ord($payload[$pos] ?? "\0");
        $pos += 1;                // auth 插件数据长度
        $pos += 10;               // 保留字段
        $salt2 = rtrim(substr($payload, $pos, max(13, $authLen - 8)), "\0");
        $salt = $salt1 . substr($salt2, 0, 12);
        if (strlen($salt) < 20) { // salt 不足 20 字节无法完成 native 认证
            fclose($fp);
            return false;
        }

        // 构造客户端认证包（mysql_native_password）
        $clientCap = 0x00000001 | 0x00000004 | 0x00000200 | 0x00002000 | 0x00008000 | 0x00020000 | 0x00080000;
        if ($pass === '') {
            $authResp = '';
        } else {
            $hash1 = sha1($pass, true);
            // 注意：内层 sha1($hash1, true) 必须为二进制，否则会得到 hex 字符串导致认证失败
            $authResp = $hash1 ^ sha1($salt . sha1($hash1, true), true);
        }
        $authPayload = pack('V', $clientCap)      // client capabilities
            . pack('V', 16777216)                 // max packet size
            . chr(33)                              // 字符集 utf8_general_ci
            . str_repeat("\0", 23)                 // 保留 23 字节
            . $user . "\0"
            . chr(strlen($authResp))
            . $authResp
            . 'mysql_native_password' . "\0";     // CLIENT_PLUGIN_AUTH 要求附带认证插件名

        fwrite($fp, self::mysqlBuildPacket($authPayload, 1));

        // 读取认证响应
        $resp = self::mysqlReadPacket($fp);
        if ($resp === null) {
            fclose($fp);
            return false;
        }
        $code = ord(substr($resp, 4, 1) ?? "\0");
        if ($code === 0x00) {
            fclose($fp);
            return true;  // OK 登录成功
        }
        if ($code === 0xFE) {
            // AuthSwitchRequest：服务端要求切换认证协议（MySQL 8 默认 caching_sha2_password）
            $ok = false;
            $payload2 = substr($resp, 4);
            $nul = strpos($payload2, "\0");
            $plugin = $nul === false ? '' : substr($payload2, 0, $nul);
            $newSalt = $nul === false ? '' : trim(substr($payload2, $nul + 1), "\0");
            if ($plugin === 'caching_sha2_password' && strlen($newSalt) >= 20 && $pass !== '') {
                // fast auth：SHA256(password) XOR SHA256(SHA256(SHA256(password)) + nonce)
                $hash1 = hash('sha256', $pass, true);
                $scramble = $hash1 ^ hash('sha256', hash('sha256', $hash1, true) . substr($newSalt, 0, 20), true);
                fwrite($fp, self::mysqlBuildPacket("\x01" . $scramble, 3));
                $r1 = self::mysqlReadPacket($fp);
                if ($r1 !== null && ord(substr($r1, 4, 1) ?? "\0") === 0x01) {
                    $r2 = self::mysqlReadPacket($fp);
                    $ok = $r2 !== null && ord(substr($r2, 4, 1) ?? "\0") === 0x00;
                }
            }
            fclose($fp);
            return $ok;
        }
        fclose($fp);
        // 0xFF 认证失败，判失败跳过
        return false;
    }

    /**
     * Redis 爆破：AUTH 密码认证；未授权（无密码 PING 可通）也算一条记录
     */
    private static function bruteRedis(string $host, int $port, array $passwords): array
    {
        $found = [];
        $attempts = 0;

        // 未授权探测：无需密码可直接操作
        $attempts++;
        if (self::redisTry($host, $port, '', true)) {
            $found[] = ['port' => $port, 'service' => 'redis', 'username' => '', 'password' => ''];
            return $found;
        }

        foreach ($passwords as $pass) {
            if ($attempts >= self::MAX_ATTEMPTS) {
                break;
            }
            $attempts++;
            if (self::redisTry($host, $port, $pass, false)) {
                $found[] = ['port' => $port, 'service' => 'redis', 'username' => '', 'password' => $pass];
                break; // Redis 单用户认证，命中一个密码即可
            }
        }
        return $found;
    }

    private static function redisTry(string $host, int $port, string $pass, bool $noAuth): bool
    {
        $fp = @stream_socket_client("tcp://{$host}:{$port}", $errno, $errstr, self::TIMEOUT);
        if (!$fp) {
            return false;
        }
        stream_set_timeout($fp, self::TIMEOUT);

        if ($noAuth) {
            fwrite($fp, "PING\r\n");
        } else {
            fwrite($fp, "AUTH {$pass}\r\n");
        }
        $resp = self::readLine($fp);
        fclose($fp);

        if ($resp === null) {
            return false;
        }
        // +PONG 未授权可用；+OK 认证通过
        return $noAuth ? strpos($resp, '+PONG') === 0 : strpos($resp, '+OK') === 0;
    }

    /**
     * 读取一行文本（读到 \n 或超时/断开），失败返回 null
     */
    private static function readLine($fp, int $max = 2048): ?string
    {
        $line = '';
        while (strlen($line) < $max) {
            $chunk = @fread($fp, $max - strlen($line));
            if ($chunk === false || $chunk === '') {
                $meta = stream_get_meta_data($fp);
                if (!empty($meta['timed_out']) || feof($fp)) {
                    break;
                }
                usleep(20000);
                continue;
            }
            $line .= $chunk;
            if (strpos($line, "\n") !== false) {
                break;
            }
        }
        return $line === '' ? null : $line;
    }

    /**
     * 读取一个完整的 MySQL 数据包（3 字节小端长度 + 1 字节序号 + payload）
     */
    private static function mysqlReadPacket($fp): ?string
    {
        $header = self::mysqlReadExact($fp, 4);
        if ($header === null || strlen($header) < 4) {
            return null;
        }
        $len = ord($header[0]) | (ord($header[1]) << 8) | (ord($header[2]) << 16);
        if ($len <= 0 || $len > 0xFFFFFF) {
            return null;
        }
        $payload = self::mysqlReadExact($fp, $len);
        if ($payload === null) {
            return null;
        }
        return $header . $payload;
    }

    /**
     * 精确读取指定字节数，超时或连接断开返回 null
     */
    private static function mysqlReadExact($fp, int $len): ?string
    {
        $data = '';
        while (strlen($data) < $len) {
            $chunk = @fread($fp, $len - strlen($data));
            if ($chunk === false || $chunk === '') {
                $meta = stream_get_meta_data($fp);
                if (!empty($meta['timed_out']) || feof($fp)) {
                    return null;
                }
                usleep(20000);
                continue;
            }
            $data .= $chunk;
        }
        return $data;
    }

    /**
     * 构造 MySQL 数据包：3 字节小端长度 + 1 字节序号 + payload
     */
    private static function mysqlBuildPacket(string $payload, int $seq): string
    {
        $len = strlen($payload);
        return chr($len & 0xFF) . chr(($len >> 8) & 0xFF) . chr(($len >> 16) & 0xFF) . chr($seq) . $payload;
    }

    /**
     * 规范化传入的字典，null 使用内置字典
     */
    private static function normalizeDict(?array $dict, array $default): array
    {
        if ($dict === null) {
            return $default;
        }
        $list = array_values(array_filter(array_map('strval', $dict), 'strlen'));
        return $list ? $list : $default;
    }

    /**
     * 记录日志（框架上下文外静默）
     */
    private static function log(string $message): void
    {
        if (function_exists('addlog')) {
            @call_user_func('addlog', [$message]);
        }
    }
}

<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2019 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// [ 应用入口文件 ]
namespace think;

header('Access-Control-Allow-Origin:*');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, x-file-name,token");

header('Content-Type: text/html; charset=utf-8');

// 静态资源直出（绕过 ThinkPHP 路由）
$requestUri = $_SERVER['REQUEST_URI'] ?? '';
$parsed = parse_url($requestUri);
$path = $parsed['path'] ?? '/';
$publicDir = __DIR__;
$staticDirs = ['/icon/', '/static/', '/web/', '/favicon.ico'];
foreach ($staticDirs as $dir) {
    if (strpos($path, $dir) === 0) {
        $file = $publicDir . $path;
        if (is_file($file)) {
            $ext = pathinfo($file, PATHINFO_EXTENSION);
            $mimeTypes = [
                'svg' => 'image/svg+xml',
                'png' => 'image/png',
                'jpg' => 'image/jpeg',
                'ico' => 'image/x-icon',
                'css' => 'text/css',
                'js'  => 'application/javascript',
                'html' => 'text/html',
            ];
            $mime = $mimeTypes[$ext] ?? 'application/octet-stream';
            header('Content-Type: ' . $mime);
            header('Content-Length: ' . filesize($file));
            readfile($file);
            exit;
        }
        // SPA fallback：/web 及 /web/ 下的前端路由刷新时返回 index.html
        if ($dir === '/web/' && ($path === '/web' || strpos($path, '/web/') === 0)) {
            $indexFile = $publicDir . '/web/index.html';
            if (is_file($indexFile)) {
                header('Content-Type: text/html; charset=utf-8');
                readfile($indexFile);
                exit;
            }
        }
    }
}

require __DIR__ . '/../vendor/autoload.php';

// 执行HTTP应用并响应
$http = (new App())->http;

$response = $http->run();

$response->send();

$http->end($response);

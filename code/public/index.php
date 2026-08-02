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

// SPA 前端路由 fallback：不带 /web 前缀访问前端路由（如 /webscan/nuclei、/asm/host）时也返回 SPA。
// 仍存活控制器的路径（webscan/asm/result/task/code/workorder 等）以 /index 结尾时走控制器 302；
// 已删除控制器的老路径（vulnerable/plugin/backup 等）一律 fallback 到 SPA。
$lastSeg = substr($path, (int)strrpos($path, '/') + 1);
if (strpos($path, '.') === false) {
    $spaPrefixes = ['/webscan', '/asm', '/result', '/task', '/code', '/workorder', '/rules'];
    // login/index、index/index 仍由存活控制器 302 重定向，其余 /index 结尾老 URL 一律 fallback
    if ($lastSeg === 'index' && (strpos($path, '/login') === 0 || strpos($path, '/index') === 0)) {
        $spaPrefixes = [];
    }
    $spaPrefixes = array_merge($spaPrefixes, [
        '/vulnerable', '/pocs_file', '/plugin', '/plugin_result', '/backup', '/config',
        '/node', '/proxy', '/process_safe', '/vul_target', '/github_notice', '/to_examine',
        '/log', '/user', '/zhiwen', '/sqlmap', '/app_nuclei', '/one_for_all', '/pocsuite',
        '/unauthorized', '/hydra', '/urls', '/dirmap', '/vulmap', '/xray', '/whatweb',
        '/app_crawlergo', '/github_keyword_monitor', '/fortify', '/semgrep', '/mobsfscan',
        '/murphysec', '/codeql', '/code_webshell', '/code_composer', '/code_java', '/code_python',
    ]);
    foreach ($spaPrefixes as $prefix) {
        if ($path === $prefix || strpos($path, $prefix . '/') === 0) {
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

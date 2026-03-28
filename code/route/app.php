<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\facade\Route;

Route::get('think', function () {
    return 'hello,ThinkPHP8!';
});

Route::get('hello/:name', 'index/hello');

// 回调接口（供插件调用）
Route::post('api/callback/status', 'callback/status');
Route::post('api/callback/result', 'callback/result');

// 任务接口
Route::post('api/task/create', 'task/create');
Route::get('api/task/status', 'task/status');
Route::get('api/task/results', 'task/results');

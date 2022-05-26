<?php
// +----------------------------------------------------------------------
// | 应用设置
// +----------------------------------------------------------------------

return [
    // 应用地址
    'app_host' => env('app.host', ''),
    // 应用的命名空间
    'app_namespace' => '',
    // 是否启用路由
    'with_route' => true,
    // 默认应用
    'default_app' => 'index',
    // 默认时区
    'default_timezone' => 'Asia/Shanghai',

    // 应用映射（自动多应用模式有效）
    'app_map' => [],
    // 域名绑定（自动多应用模式有效）
    'domain_bind' => [],
    // 禁止URL访问的应用列表（自动多应用模式有效）
    'deny_app_list' => [],

    // 异常页面的模板文件
    'exception_tmpl' => app()->getThinkPath() . 'tpl/think_exception.tpl',

    'dispatch_success_tmpl' => app()->getRootPath() . '/public/tpl/dispatch_jump.tpl',
    'dispatch_error_tmpl' => app()->getRootPath() . '/public/tpl/dispatch_jump.tpl',

    // 错误显示信息,非调试模式有效
    'error_message' => '页面错误！请稍后再试～',
    // 显示错误信息
    'show_error_msg' => true,
    'UC_AUTH_KEY' => 'xt1l3a21uo0tu2oxtds3wWte23dsxix2d3in7yuhui32yuapatmdsnnzdazh1612ongxxin2z',
    'ADMINISTRATOR' => [1],
    'not_del' => [],
    'NOT_AUTH_ACTION' => ['index/index','auth/user_info','auth/user_password'],
    'backup' => [
        'path' => '/data/tools/backup/',//数据库备份路径
        'part' => 20971520,//数据库备份卷大小
        'compress' => 0,//数据库备份文件是否启用压缩 0不压缩 1 压缩
        'level' => 9 //数据库备份文件压缩级别 1普通 4 一般  9最高
    ],
    'plugin_store'=>[
        'domain_name'=>'http://qingscan.xtblog.net/'
    ]
];

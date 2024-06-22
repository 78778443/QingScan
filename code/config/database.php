<?php
return array(
    'default' => 'mysql',
    'time_query_rule' =>
        array(),
    'auto_timestamp' => true,
    'datetime_format' => 'Y-m-d H:i:s',
    'datetime_field' => '',
    'connections' =>
        array(
            'mysql' =>
                array(
                    'type' => 'mysql',
                    // 服务器地址
                    'hostname' => env('database.hostname', '127.0.0.1'),
                    // 数据库名
                    'database' => env('database.database', ''),
                    // 用户名
                    'username' => env('database.username', 'root'),
                    // 密码
                    'password' => env('database.password', ''),
                    // 端口
                    'hostport' => env('database.hostport', '3306'),
                    'charset' => 'utf8mb4',
                    'prefix' => '',
                    'debug' => true,
                    'fields_strict' => false,
                    'deploy' => 0,
                    'rw_separate' => false,
                    'master_num' => 1,
                    'slave_no' => '',
                    'break_reconnect' => true,
                    // 监听SQL
                    'trigger_sql'     => env('app_debug', true),
                    'fields_cache' => false,
                ),
        ),
);
?>
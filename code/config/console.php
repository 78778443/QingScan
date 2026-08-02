<?php
// +----------------------------------------------------------------------
// | 控制台配置
// +----------------------------------------------------------------------
return [
    // 指令定义
    'commands' => [
        'scan' => 'app\command\Scan',
        'schedule' => 'app\command\Schedule',
        'install' => 'app\command\Install',
        'asm' => 'app\command\Asm',
        'db:sql' => 'app\command\DbSql',
    ],
];
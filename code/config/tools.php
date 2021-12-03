<?php

// 相关工具配置
return [
    'hydra' => [
        'password'=>\think\facade\App::getRuntimePath().'tools/hydra/password.txt',
        'install_path'=>'/data/tools/hydra/'
    ],
    'sqlmap' => [
        'install_path'=>''
    ],
    'oneForAll' => [
        'install_path'=>'/data/tools/oneforall/',
        'file_path' => \think\facade\App::getRuntimePath().'tools/oneforall/'
    ],
    'dirmap' => [
        'install_path'=>''
    ],
    'whatweb'=>[
        'install_path'=>'',
        'file_path' => '/data/tools/whatweb',
    ]
];
<?php

// 相关工具配置
return [
    'hydra' => [
        'install_path'=>'/data/tools/hydra/',
        'username'=>\think\facade\App::getRootPath().'tools/hydra/username.txt',
        'password'=>\think\facade\App::getRootPath().'tools/hydra/password.txt'
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
<?php

namespace app\model;

use think\facade\Db;

class UserLogModel extends BaseModel
{
    public static function addLog($username, $type, $content)
    {
        $data = [
            'username' => $username,
            'type' => $type,
            'content' => $content,
            'create_time' => date('Y-m-d H:i:s', time()),
            'ip' => request()->ip()
        ];
        Db::name('user_log')->insert($data);
    }
}
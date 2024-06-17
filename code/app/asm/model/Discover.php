<?php


namespace app\asm\model;


use app\model\BaseModel;
use think\facade\Db;

class Discover extends BaseModel
{
    public static function add()
    {

        $list = Db::table('task_scan')->where(['type' => 'asm_discov'])->select()->toArray();

        foreach ($list as $value) {

        }
    }


}

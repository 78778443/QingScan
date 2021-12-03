<?php

namespace app\model;


use think\facade\Db;

class AuthRuleModel extends BaseModel
{
    public static function isExist($href){
        $map[] = ['href','=',$href];
        $map[] = ['is_delete','=',0];
        return Db::name('auth_rule')->where($map)->count('auth_rule_id');
    }
}
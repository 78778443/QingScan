<?php


namespace app\controller;


use think\facade\Db;
use think\facade\View;

class Whatweb extends Common
{
    public function index()
    {
        $pageSize = 20;
        $where = [];
        if ($this->auth_group_id != 5 && !in_array($this->userId,config('app.ADMINISTRATOR'))) {
            $where[] = ['user_id','=',$this->userId];
        }
        $list = Db::table('app_whatweb')->where($where)->order("id", 'desc')->paginate($pageSize);
        $data['list'] = $list->items();
        foreach ($data['list'] as &$v) {
            $v['app_name'] = Db::name('app')->where('id', $v['app_id'])->value('name');
        }
        $data['page'] = $list->render();
        return View::fetch('index', $data);
    }
}
<?php


namespace app\controller;


use think\facade\Db;
use think\facade\View;

class CodePython extends Common
{
    public function index(){
        $pageSize = 20;
        $where = [];
        if ($this->auth_group_id != 5 && !in_array($this->userId, config('app.ADMINISTRATOR'))) {
            $where[] = ['user_id', '=', $this->userId];
        }
        $list = Db::table('code_python')->where($where)->order("id", 'desc')->paginate($pageSize);
        $data['list'] = $list->items();
        foreach ($data['list'] as &$v) {
            $v['code_name'] = Db::table('code')->where('id',$v['code_id'])->value('name');
        }
        $data['page'] = $list->render();
        $data['nameArr'] = Db::table('code_python')->group('name')->column('name');
        return View::fetch('index', $data);
    }
}
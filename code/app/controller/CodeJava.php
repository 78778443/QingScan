<?php


namespace app\controller;


use think\facade\Db;
use think\facade\View;

class CodeJava extends Common
{
    public function index(){
        $pageSize = 20;
        $where = [];
        $list = Db::table('code_java')->where($where)->order("id", 'desc')->paginate($pageSize);
        $data['list'] = $list->items();
        foreach ($data['list'] as &$v) {
            $v['code_name'] = Db::table('code')->where('id',$v['code_id'])->value('name');
        }
        $data['page'] = $list->render();
        //$data['nameArr'] = Db::table('code_python')->group('name')->column('name');
        return View::fetch('index', $data);
    }
}
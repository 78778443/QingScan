<?php


namespace app\controller;


use think\facade\Db;
use think\facade\View;
use think\Request;

class CodeComposer extends Common
{
    public function index(Request $request){
        $pageSize = 20;
        $where = [];
        $search = $request->param('search');
        if (!empty($search)) {
            $where[] = ['name|version|source|authors','like',"%{$search}%"];
        }
        if ($this->auth_group_id != 5 && !in_array($this->userId, config('app.ADMINISTRATOR'))) {
            $where[] = ['user_id', '=', $this->userId];
        }
        $list = Db::table('code_composer')->where($where)->order("id", 'desc')->paginate($pageSize);
        $data['list'] = $list->items();
        foreach ($data['list'] as &$v) {
            $v['code_name'] = Db::table('code')->where('id',$v['code_id'])->value('name');
        }
        $data['page'] = $list->render();
        return View::fetch('index', $data);
    }
}
<?php

namespace app\controller;

use think\facade\Db;
use think\facade\View;

class CodeWebshell extends Common
{
    public function index(){
        $pageSize = 20;
        $where = [];
        $search = getParam('search');
        if (!empty($search)) {
            $where[] = ['type|path','like',"%{$search}%"];
        }
        $code_id = getParam('code_id');
        if (!empty($code_id)) {
            $where[] = ['code_id','=',$code_id];
        }
        if ($this->auth_group_id != 5 && !in_array($this->userId, config('app.ADMINISTRATOR'))) {
            $where[] = ['user_id', '=', $this->userId];
        }
        $list = Db::table('code_webshell')->where($where)->order("id", 'desc')->paginate(['list_rows'=>$pageSize,'query'=>request()->param()]);
        $data['list'] = $list->items();
        foreach ($data['list'] as &$v) {
            $v['name'] = Db::name('code')->where('id',$v['code_id'])->value('name');
        }
        $data['page'] = $list->render();
        //查询项目数据
        $projectArr = Db::table('code')->where($where)->where('is_delete',0)->select()->toArray();
        $data['projectList'] = array_column($projectArr, 'name', 'id');
        return View::fetch('index', $data);
    }
}
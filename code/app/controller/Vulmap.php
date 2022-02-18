<?php

namespace app\controller;

use think\facade\Db;
use think\facade\View;
use think\Request;

class Vulmap extends Common
{
    public function index(Request $request)
    {
        $pageSize = 20;
        $where = [];
        if ($this->auth_group_id != 5 && !in_array($this->userId, config('app.ADMINISTRATOR'))) {
            $where[] = ['user_id', '=', $this->userId];
        }
        $app_id = $request->param('app_id');
        if (!empty($app_id)) {
            $where[] = ['app_id','=',$app_id];
        }
        $list = Db::table('app_vulmap')->where($where)->order("id", 'desc')->paginate([
            'list_rows'=> $pageSize,//每页数量
            'query' => $request->param(),
        ]);
        $data['list'] = $list->items();
        foreach ($data['list'] as &$v) {
            $v['app_name'] = Db::name('app')->where('id', $v['app_id'])->value('name');
        }
        $data['page'] = $list->render();
        //查询项目数据
        $projectArr = Db::table('app')->where($where)->where('is_delete',0)->select()->toArray();
        $data['projectList'] = array_column($projectArr, 'name', 'id');
        return View::fetch('index', $data);
    }
}
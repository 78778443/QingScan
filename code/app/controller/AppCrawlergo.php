<?php

namespace app\controller;

use think\facade\Db;
use think\facade\View;
use think\Request;

class AppCrawlergo extends Common
{
    public function index(Request $request)
    {
        $pageSize = 20;
        $where = [];
        $search = $request->param('search');
        if (!empty($search)) {
            $where[] = ['url', 'like', "%{$search}%"];
        }
        $app_id = $request->param('app_id');
        if (!empty($app_id)) {
            $where[] = ['app_id', '=', $app_id];
        }
        if ($this->auth_group_id != 5 && !in_array($this->userId, config('app.ADMINISTRATOR'))) {
            $where[] = ['user_id', '=', $this->userId];
        }
        $list = Db::table('app_crawlergo')->where($where)->order("id", 'desc')->paginate([
            'list_rows' => $pageSize,
            'query' => $request->param()
        ]);
        $data['list'] = $list->items();
        foreach ($data['list'] as &$v) {
            $v['app_name'] = Db::name('app')->where('id', $v['app_id'])->value('name');
        }
        $data['page'] = $list->render();
        //查询项目数据
        $data['projectList'] = $this->getMyAppList();
        return View::fetch('index', $data);
    }
}
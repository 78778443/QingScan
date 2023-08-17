<?php

namespace app\webscan\controller;

use app\controller\Common;
use think\facade\Db;
use think\facade\View;
use think\Request;

class GithubKeywordMonitorNotice extends Common
{
    public function index(Request $request)
    {
        $pageSize = 20;
        $where = [];
        $search = $request->param('search');
        if (!empty($search)) {
            $where[] = ['keyword','like',"%{$search}%"];
        }
        if ($this->auth_group_id != 5 && !in_array($this->userId,config('app.ADMINISTRATOR'))) {
            $where[] = ['user_id','=',$this->userId];
        }
        $list = Db::table('github_keyword_monitor_notice')->where($where)->order("id", 'desc')->paginate([
            'list_rows' => $pageSize,
            'query' => $request->param()
        ]);
        $data['list'] = $list->items();
        $data['page'] = $list->render();
        return View::fetch('index', $data);
    }

    // 批量删除
    public function batch_del(Request $request){
        return $this->batch_del_that($request,'github_keyword_monitor_notice');
    }
}
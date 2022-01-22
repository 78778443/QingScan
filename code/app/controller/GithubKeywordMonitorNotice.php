<?php

namespace app\controller;

use think\facade\Db;
use think\facade\View;
use think\Request;

class GithubKeywordMonitorNotice extends Common
{
    public function index(Request $request)
    {
        $pageSize = 20;
        $where = [];
        $keyword = $request->param('search');
        if (!empty($keyword)) {
            $where[] = ['keyword','like',"%{$keyword}%"];
        }
        $list = Db::table('github_keyword_monitor_notice')->where($where)->order("id", 'desc')->paginate($pageSize);
        $data['list'] = $list->items();
        $data['page'] = $list->render();
        return View::fetch('index', $data);
    }
}
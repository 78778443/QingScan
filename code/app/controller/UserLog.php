<?php

namespace app\controller;

use think\facade\Db;
use think\facade\View;

class UserLog extends Common
{
    public function index()
    {
        $pageSize = 20;
        $where = [];
        $search = getParam('search');
        if (!empty($search)) {
            $where[] = ['username','like',"%{$search}%"];
        }
        $list = Db::table('user_log')->where($where)->order("id", 'desc')->paginate(['list_rows' => $pageSize, 'query' => request()->param()]);
        $data['list'] = $list->items();
        $data['page'] = $list->render();
        return View::fetch('index', $data);
    }
}
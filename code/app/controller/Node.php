<?php

namespace app\controller;

use think\facade\Db;
use think\facade\View;
use think\Request;

class Node extends Common
{

    public function index(Request $request)
    {
        $pageSize = 20;
        $where = [];
        $list = Db::table('node')->where($where)->order("id", 'desc')->paginate([
            'list_rows' => $pageSize,
            'query' => $request->param()
        ]);
        $data['list'] = $list->items();
        $data['page'] = $list->render();
        return View::fetch('index', $data);
    }
}

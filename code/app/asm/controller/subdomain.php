<?php

namespace app\asm\controller;

use app\controller\Common;
use think\facade\Db;
use think\facade\View;
use think\Request;
use think\response\Redirect;


class subdomain extends Common
{

    public function index(Request $request)
    {
        $pageSize = 20;
        $where = [];
        if ($request->param('sub_domain')) $where[] = ['sub_domain', '=', $request->param('sub_domain')];

        $list = Db::table('asm_sub_domain')->where($where)->order("id", 'desc')->paginate([
            'list_rows' => $pageSize,//每页数量
            'query' => $request->param(),
        ]);
        $data['list'] = $list->items();
        $data['page'] = $list->render();
        return View::fetch('index', $data);
    }





}

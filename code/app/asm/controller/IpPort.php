<?php

namespace app\asm\controller;

use app\controller\Common;
use think\facade\Db;
use think\facade\View;
use think\Request;


class IpPort extends Common
{

    public function index(Request $request)
    {
        if (function_exists('startGetDomain')) startGetDomain();

        $pageSize = 20;
        $where = [];
        if ($request->param('domain')) $where[] = ['domain', '=', $request->param('domain')];
        if (!empty($request->param('app_id'))) $where[] = ['app_id', '=', $request->param('app_id')];

        $list = Db::table('asm_ip_port')->where($where)->order("id", 'desc')->paginate([
            'list_rows' => $pageSize,//每页数量
            'query' => $request->param(),
        ]);
        $data['list'] = $list->items();
        $data['page'] = $list->render();
        return View::fetch('index', $data);
    }

}

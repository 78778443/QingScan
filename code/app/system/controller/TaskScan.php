<?php

namespace app\system\controller;

use app\asm\model\DomainModel;
use app\controller\Common;
use think\facade\Db;
use think\facade\View;
use think\Request;


class TaskScan extends Common
{

    public function index(Request $request)
    {
        $pageSize = 20;
        $where = [];

        $list = Db::table('task_scan')->where($where)->order("id", 'desc')->paginate([
            'list_rows' => $pageSize,//每页数量
            'query' => $request->param(),
        ]);
        $data['list'] = $list->items();
        $data['page'] = $list->render();

        return View::fetch('index', $data);
    }


    public function _add(Request $request)
    {
        $lines = explode("\n", $request->param('url'));

        DomainModel::insertTarget($lines);

        return redirect('/asm/domain/index');
    }


}

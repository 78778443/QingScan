<?php

namespace app\system\controller;

use app\controller\Common;
use think\facade\Db;
use think\facade\View;
use think\Request;

class UserLog extends Common
{
    public function index(Request $request)
    {
        $pageSize = 20;
        $where = [];
        $search = $request->param('search');
        if (!empty($search)) {
            $where[] = ['username','like',"%{$search}%"];
        }
        $list = Db::table('user_log')->where($where)->order("id", 'desc')->paginate([
            'list_rows' => $pageSize,
            'query' => $request->param()
        ]);
        $data['list'] = $list->items();
        $data['page'] = $list->render();
        return View::fetch('index', $data);
    }

    public function clear_all(){
        Db::execute('truncate table `user_log`');
        $this->addUserLog('日志管理','清空数据表[user_log]成功');
        $this->success('日志表清空成功','index');
    }
}
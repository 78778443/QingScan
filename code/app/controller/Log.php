<?php


namespace app\controller;


use think\facade\Db;
use think\facade\View;
use think\Request;

class Log extends Common
{
    public function index(Request $request)
    {
        $pageSize = 20;
        $search = $request->param('search');
        $where = [];
        if ($search) {
            $where[] = ['content','like',"%{$search}%"];
        }
        $list = Db::table('log')->where($where)->order("id", 'desc')->paginate(['list_rows' => $pageSize, 'query' => request()->param()]);
        $data['list'] = $list->items();
        $data['page'] = $list->render();
        return View::fetch('index', $data);
    }

    public function clear_all(){
        Db::execute('truncate table `log`');
        $this->success('日志表清空成功');
    }
}
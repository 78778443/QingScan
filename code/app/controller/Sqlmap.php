<?php


namespace app\controller;


use think\facade\Db;
use think\facade\View;
use think\Request;

class Sqlmap extends Common
{
    public function index(Request $request){
        $pageSize = 20;
        $where = [];
        $app_id = $request->param('app_id');
        if (!empty($app_id)) {
            $where[] = ['app_id','=',$app_id];
        }
        if ($this->auth_group_id != 5 && !in_array($this->userId, config('app.ADMINISTRATOR'))) {
            $where[] = ['user_id', '=', $this->userId];
        }
        $list = Db::table('urls_sqlmap')->where($where)->order("id", 'desc')->paginate($pageSize);
        $data['list'] = $list->items();
        foreach ($data['list'] as &$val) {
            $val['uels'] = Db::name('urls')->where('id',$val['urls_id'])->value('url');
        }
        $data['page'] = $list->render();
        return View::fetch('index', $data);
    }
}
<?php


namespace app\controller;


use think\facade\Db;
use think\facade\View;
use think\Request;

class Zhiwen extends Common
{
    public function index(Request $request){
        $pageSize = 20;
        $where = [];
        /*if ($this->auth_group_id != 5 && !in_array($this->userId, config('app.ADMINISTRATOR'))) {
            $where[] = ['user_id', '=', $this->userId];
        }*/
        $search = $request->param('search');
        if (!empty($search)) {
            $where[] = ['filters|keyword|supplier|tags|title','like',"%{$search}%"];
        }
        $list = Db::table('zhiwen')->where($where)->paginate([
            'list_rows' => $pageSize,
            'query' => request()->param()
        ]);
        $data['list'] = $list->items();
        $data['page'] = $list->render();
        return View::fetch('index', $data);
    }

    // 批量删除
    public function batch_del(Request $request){
        return $this->batch_del_that($request,'zhiwen');
    }
}
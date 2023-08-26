<?php

namespace app\webscan\controller;

use app\controller\Common;
use think\facade\Db;
use think\facade\View;
use think\Request;

class Vulmap extends Common
{
    public function index(Request $request)
    {
        $pageSize = 20;
        $where = [];

        $search = $request->param('search');
        if (!empty($search)) {
            $where[] = ['author|host|port','like',"%{$search}%"];
        }
        $app_id = $request->param('app_id');
        if (!empty($app_id)) {
            $where[] = ['app_id','=',$app_id];
        }
        $list = Db::table('app_vulmap')->where($where)->order("id", 'desc')->paginate([
            'list_rows'=> $pageSize,//每页数量
            'query' => $request->param(),
        ]);
        $data['list'] = $list->items();
        foreach ($data['list'] as &$v) {
            $v['app_name'] = Db::name('app')->where('id', $v['app_id'])->value('name');
        }
        $data['page'] = $list->render();
        //查询项目数据
        $data['projectList'] = $this->getMyAppList();
        return View::fetch('index', $data);
    }

    public function del(Request $request)
    {
        $id = $request->param('id', '', 'intval');
        $map[] = ['id', '=', $id];
        

        if (Db::name('app_vulmap')->where($map)->delete()) {
            return redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->error('删除失败');
        }
    }


    // 批量删除
    public function batch_del(Request $request){
        return $this->batch_del_that($request,'app_vulmap');
    }
}
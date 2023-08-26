<?php

namespace app\webscan\controller;

use app\controller\Common;
use think\facade\Db;
use think\facade\View;
use think\Request;

class AppNuclei extends Common
{
    public function index(Request $request)
    {
        $pageSize = 20;
        $where = [];
        $search = $request->param('search');
        if (!empty($search)) {
            $where[] = ['name|host', 'like', "%{$search}%"];
        }
        $app_id = $request->param('app_id');
        if (!empty($app_id)) {
            $where[] = ['app_id', '=', $app_id];
        }

        $list = Db::table('app_nuclei')
            ->where($where)
            ->order("id", 'desc')
            ->paginate([
                'list_rows' => $pageSize,
                'query' => $request->param()
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

    // 批量删除
    public function batch_del(Request $request){
        return $this->batch_del_that($request,'app_nuclei');
    }
}
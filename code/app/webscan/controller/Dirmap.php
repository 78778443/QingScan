<?php


namespace app\webscan\controller;

use app\controller\Common;
use think\facade\Db;
use think\facade\View;
use think\Request;

class Dirmap extends Common
{
    public function index(Request $request)
    {
        $pageSize = 20;
        $where = [];

        $search = $request->param('search');
        if (!empty($search)) {
            $where[] = ['url|type', 'like', "%{$search}%"];
        }
        $app_id = $request->param('app_id');
        if (!empty($app_id)) {
            $where[] = ['app_id', '=', $app_id];
        }
        $list = Db::table('app_dirmap')->where($where)->order("id", 'desc')->paginate([
            'list_rows' => $pageSize,
            'query' => request()->param()
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
        $id = $request->param('id');
        $where[] = ['id', '=', $id];

        if (Db::name('app_dirmap')->where('id', $id)->delete()) {
            return redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->error('删除失败');
        }
    }


    // 批量删除
    public function batch_del(Request $request){
        return $this->batch_del_that($request,'app_dirmap');
    }
}
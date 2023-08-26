<?php


namespace app\webscan\controller;


use app\controller\Common;
use think\facade\Db;
use think\facade\View;
use think\Request;

class Hydra extends Common
{
    public function index(Request $request){
        $pageSize = 20;
        $where = [];
        $search = $request->param('search');
        if (!empty($search)) {
            $where[] = ['host|type','like',"%{$search}%"];
        }
        $app_id = $request->param('app_id');
        if (!empty($app_id)) {
            $where[] = ['app_id','=',$app_id];
        }

        $list = Db::table('host_hydra_scan_details')->where($where)->order("id", 'desc')->paginate([
            'list_rows'=>$pageSize,
            'query'=>$request->param()
        ]);
        $data['list'] = $list->items();
        foreach ($data['list'] as &$v) {
            $v['host'] = Db::name('host')->where('id',$v['host_id'])->value('hsot');
        }
        $data['page'] = $list->render();
        //查询项目数据
        $data['projectList'] = $this->getMyAppList();
        return View::fetch('index', $data);
    }
    public function del(Request $request)
    {
        $id = $request->param('id');
        $where[] = ['id','=',$id];

        if (Db::name('host_hydra_scan_details')->where('id',$id)->delete()) {
            return redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->error('删除失败');
        }
    }

    // 批量删除
    public function batch_del(Request $request){
        return $this->batch_del_that($request,'host_hydra_scan_details');
    }
}
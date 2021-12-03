<?php


namespace app\controller;


use think\facade\Db;
use think\facade\View;

class OneForAll extends Common
{
    public function index(){
        $pageSize = 20;
        $where = [];
        $search = getParam('search');
        if (!empty($search)) {
            $where[] = ['title','like',"%{$search}%"];
        }
        $app_id = getParam('app_id');
        if (!empty($app_id)) {
            $where[] = ['app_id','=',$app_id];
        }
        $list = Db::table('one_for_all')->where($where)->order("id", 'desc')->paginate(['list_rows'=>$pageSize,'query'=>request()->param()]);
        $data['list'] = $list->items();
        foreach ($data['list'] as &$v) {
            $v['app_name'] = Db::name('app')->where('id',$v['app_id'])->value('name');
        }
        $data['page'] = $list->render();
        //查询项目数据
        $projectArr = Db::table('app')->where('is_delete',0)->select()->toArray();
        $data['projectList'] = array_column($projectArr, 'name', 'id');
        return View::fetch('index', $data);
    }
}
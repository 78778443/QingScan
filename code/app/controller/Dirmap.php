<?php


namespace app\controller;


use think\facade\Db;
use think\facade\View;

class Dirmap extends Common
{
    public function index(){
        $pageSize = 20;
        $where = [];
        if ($this->auth_group_id != 5 && !in_array($this->userId,config('app.ADMINISTRATOR'))) {
            $where[] = ['user_id','=',$this->userId];
        }
        $search = getParam('search');
        if (!empty($search)) {
            $where[] = ['url','like',"%{$search}%"];
        }
        $app_id = getParam('app_id');
        if (!empty($app_id)) {
            $where[] = ['app_id','=',$app_id];
        }
        $list = Db::table('app_dirmap')->where($where)->order("id", 'desc')->paginate(['list_rows'=>$pageSize,'query'=>request()->param()]);
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
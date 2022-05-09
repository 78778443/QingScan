<?php

namespace app\controller;

use think\facade\Db;
use think\facade\View;
use think\Request;

class PluginResult extends Common
{

    public function index(Request $request)
    {
        $pageSize = 20;
        $where[] = ['is_delete','=',0];
        $search = $request->param('search');
        if ($search) {
            $where[] = ['name','like',"%{$search}%"];
        }
        $plugin_id = $request->param('plugin_id');
        if ($plugin_id) {
            $where[] = ['plugin_id','=',$plugin_id];
        }
        $app_id = $request->param('app_id');
        if ($app_id) {
            $where[] = ['app_id','=',$app_id];
        }
        $map[] = ['is_delete','=',0];
        if ($this->auth_group_id != 5 && !in_array($this->userId, config('app.ADMINISTRATOR'))) {
            $where[] = ['a.user_id', '=', $this->userId];
            $map[] = ['user_id', '=', $this->userId];
        }
        $list = Db::table('plugin_scan_log')->alias('a')
            ->leftJoin('plugin b','b.id=a.plugin_id')
            ->where($where)
            ->field('a.*,b.name,b.result_file')
            ->order("a.id", 'desc')
            ->paginate([
                'list_rows'=> $pageSize,//每页数量
                'query' => $request->param(),
            ]);
        $data['list'] = $list->items();
        foreach ($data['list'] as &$v) {
            $v['app_name'] = Db::name('app')->where('id',$v['app_id'])->value('name');
        }
        $data['page'] = $list->render();
        //查询项目数据
        $data['projectList'] = $this->getMyAppList();
        //插件列表数据
        $projectArr = Db::table('plugin')->where($map)->select()->toArray();
        $data['pluginList'] = array_column($projectArr, 'name', 'id');
        return View::fetch('index', $data);
    }

    public function details(Request $request){
        $id = $request->param('id');
        $info = Db::table('plugin_scan_log')->where(['id'=>$id])->find();
        $data['info'] = $info;
        return View::fetch('detail', $data);
    }

    public function del(Request $request){
        $id = $request->param('id');
        if (!$id) {
            $this->error('请先选择要删除的数据');
        }
        $map[] = ['id','=',$id];
        if ($this->auth_group_id != 5 && !in_array($this->userId, config('app.ADMINISTRATOR'))) {
            $map[] = ['user_id', '=', $this->userId];
        }
        if (Db::name('plugin_scan_log')->where($map)->delete()) {
            return redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->error('删除失败');
        }
    }

    // 批量删除
    public function batch_del(Request $request){
        return $this->batch_del_that($request,'plugin_scan_log');
    }
}
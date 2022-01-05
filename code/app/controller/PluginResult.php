<?php

namespace app\controller;

use think\facade\Db;
use think\facade\View;

class PluginResult extends Common
{

    public function index()
    {
        $pageSize = 20;
        $where[] = ['is_delete','=',0];
        $search = getParam('search');
        if ($search) {
            $where[] = ['name','like',"%{$search}%"];
        }
        $app_id = getParam('app_id');
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
            ->paginate($pageSize);
        $data['list'] = $list->items();
        foreach ($data['list'] as &$v) {
            $v['app_name'] = Db::name('app')->where('id',$v['app_id'])->value('name');
        }
        $data['page'] = $list->render();
        //查询项目数据
        $projectArr = Db::table('app')->where($map)->select()->toArray();
        $data['projectList'] = array_column($projectArr, 'name', 'id');
        //插件列表数据
        $projectArr = Db::table('plugin')->where($map)->select()->toArray();
        $data['pluginList'] = array_column($projectArr, 'name', 'id');
        return View::fetch('index', $data);
    }

    public function details(){
        $id = getParam('id');

        $info = Db::table('plugin_scan_log')->where(['id'=>$id])->find();

        $data['info'] = $info;
        return View::fetch('detail', $data);
    }
}
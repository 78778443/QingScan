<?php

namespace app\controller;

use think\facade\Db;
use think\facade\View;
use think\Request;

class PluginStore extends Common
{
    public $plugin_store_domain;

    public function initialize()
    {
        $this->plugin_store_domain = config('app.plugin_store.domain_name');

        parent::initialize();
    }

    public function index(){
        $result = curl_get($this->plugin_store_domain.'plugin_store/list');
        $list = json_decode($result,true)['data'];
        foreach ($list as &$v) {
            $v['is_install'] = 0;
            $v['status'] = '未安装';
            $where['name'] = $v['name'];
            $info = Db::name('plugin_store')->where($where)->find();
            if ($info) {
                $v['is_install'] = 1;
                if ($info['status']) {
                    $v['status'] = '开启';
                } else {
                    $v['status'] = '禁用';
                }
            }
        }
        $data['list'] = $list;
        return View::fetch('index',$data);
    }

    public function install(Request $request){
        $id = $request->param('id',0,'intval');
        echo $id;
    }
}
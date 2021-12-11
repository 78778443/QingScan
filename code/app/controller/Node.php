<?php

namespace app\controller;

use think\facade\Db;
use think\facade\View;

class Node extends Common
{

    public function index()
    {
        /*echo '<pre>';
        $cmd = "pocsuite -r ./cms/thinkphp/thinkphp_5_0_x_remote_code_execution.py -u http://127.0.0.1:8000 --verify";
        execLog($cmd, $output);
        //$result = implode("\n", $result);

        var_dump(json_encode($output));
        exit;*/
        /*echo '<pre>';
        // 设置代理
        $filename = '/data/tools/xray/config1.yaml';
        $arr = @yaml_parse_file($filename);
        var_dump($arr);

        exit;*/
        $pageSize = 20;
        $where = [];
        $list = Db::table('node')->where($where)->order("id", 'desc')->paginate($pageSize);
        $data['list'] = $list->items();
        $data['page'] = $list->render();
        return View::fetch('index', $data);
    }
}

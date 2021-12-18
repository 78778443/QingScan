<?php

namespace app\controller;

use app\BaseController;
use think\facade\Db;
use think\facade\View;

class VulTarget extends Common
{

    public function index()
    {
        $where = [];


        $pageSize = 25;
        $list = Db::table('vul_target')->alias('v')->leftJoin('pocs_file p','v.id = p.vul_id')->where($where)->order('v.id', 'desc')->paginate($pageSize);
        $data = [];
        $data['list'] = $list->toArray()['data'];
        $data['page'] = $list->render();

        return View::fetch('index', $data);
    }


}
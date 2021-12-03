<?php

namespace app\controller;

use app\BaseController;
use think\facade\Db;
use think\facade\View;

class Pocsuite extends Common
{

    /**
     * @return int
     * @Route("Index/app")
     */
    public function app()
    {
        $list = Db::table('pocsuite3')->select()->toArray();
        var_dump($list);
    }

}
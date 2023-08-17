<?php

namespace app\webscan\controller;

use app\controller\Common;
use think\facade\Db;

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
<?php


namespace app\controller;


use think\facade\Db;
use think\facade\View;

class Log extends Common
{
    public function index()
    {
        $list = Db::table('log')->order("id", 'desc')->paginate(20);
        $data['list'] = $list->items();
        $data['page'] = $list->render();
        return View::fetch('index', $data);
    }

}
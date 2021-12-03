<?php


namespace app\controller;


use think\facade\Db;
use think\facade\View;

class Sqlmap extends Common
{
    public function index(){
        $pageSize = 20;
        $where = [];
        $list = Db::table('urls_sqlmap')->where($where)->order("id", 'desc')->paginate($pageSize);
        $data['list'] = $list->items();
        foreach ($data['list'] as &$val) {
            $val['uels'] = Db::name('urls')->where('id',$val['urls_id'])->value('url');
        }
        $data['page'] = $list->render();
        return View::fetch('index', $data);
    }
}
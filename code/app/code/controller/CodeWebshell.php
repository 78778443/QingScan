<?php

namespace app\code\controller;

use app\controller\Common;
use think\facade\Db;
use think\facade\View;
use think\Request;

class CodeWebshell extends Common
{
    public function index(Request $request){
        $pageSize = 20;
        $where = [];
        $search = $request->param('search');
        if (!empty($search)) {
            $where[] = ['type|path','like',"%{$search}%"];
        }
        $code_id = $request->param('code_id');
        if (!empty($code_id)) {
            $where[] = ['code_id','=',$code_id];
        }
        $map = [];

        $list = Db::table('code_webshell')->where($where)->order("id", 'desc')->paginate([
            'list_rows'=> $pageSize,//每页数量
            'query' => $request->param(),
        ]);
        $data['list'] = $list->items();
        foreach ($data['list'] as &$v) {
            $v['name'] = Db::name('code')->where('id',$v['code_id'])->value('name');
        }
        $data['page'] = $list->render();
        //查询项目数据
        $data['projectList'] = $this->getMyCodeList();
        return View::fetch('index', $data);
    }


    public function del(Request $request)
    {
        $id = $request->param('id');
        $where[] = ['id','=',$id];

        if (Db::name('code_webshell')->where('id',$id)->delete()) {
            return redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->error('删除失败');
        }
    }

    // 批量删除
    public function batch_del(Request $request){
        return $this->batch_del_that($request,'code_webshell');
    }
}
<?php


namespace app\code\controller;


use app\controller\Common;
use think\facade\Db;
use think\facade\View;
use think\Request;

class CodePython extends Common
{
    public function index(Request $request){
        $pageSize = 20;
        $where = [];
        $search = $request->param('search');
        if (!empty($search)) {
            $where[] = ['name','like',"%{$search}%"];
        }

        $code_id = $request->param('code_id');
        if ($code_id) {
            $where[] = ['code_id','=',$code_id];
        }
        $list = Db::table('code_python')->where($where)->order("id", 'desc')->paginate([
            'list_rows'=> $pageSize,//每页数量
            'query' => $request->param(),
        ]);
        $data['list'] = $list->items();
        foreach ($data['list'] as &$v) {
            $v['code_name'] = Db::table('code')->where('id',$v['code_id'])->value('name');
        }
        $data['page'] = $list->render();
        $data['projectList'] = $this->getMyCodeList();
        return View::fetch('index', $data);
    }


    public function del(Request $request)
    {
        $id = $request->param('id');
        $where[] = ['id','=',$id];

        if (Db::name('code_python')->where('id',$id)->delete()) {
            return redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->error('删除失败');
        }
    }

    // 批量删除
    public function batch_del(Request $request){
        return $this->batch_del_that($request,'code_python');
    }
}
<?php


namespace app\controller;


use think\facade\Db;
use think\facade\View;
use think\Request;

class Sqlmap extends Common
{
    public function index(Request $request){
        $pageSize = 20;
        $where = [];
        $search = $request->param('search');
        if (!empty($search)) {
            $where[] = ['type|title|payload','like',"%{$search}%"];
        }
        if ($this->auth_group_id != 5 && !in_array($this->userId, config('app.ADMINISTRATOR'))) {
            $where[] = ['user_id', '=', $this->userId];
        }
        $app_id = $request->param('app_id');
        if (!empty($app_id)) {
            $where[] = ['app_id','=',$app_id];
        }
        $list = Db::table('urls_sqlmap')->where($where)->order("id", 'desc')->paginate([
            'list_rows'=> $pageSize,//每页数量
            'query' => $request->param(),
        ]);
        $data['list'] = $list->items();
        foreach ($data['list'] as &$val) {
            $val['url'] = Db::name('urls')->where('id',$val['urls_id'])->value('url');
        }
        $data['page'] = $list->render();

        $data['projectList'] = $this->getMyAppList();

        return View::fetch('index', $data);
    }


    public function del(Request $request)
    {
        $id = $request->param('id', '', 'intval');
        if (!$id) {
            $this->error('参数不能为空');
        }
        $map[] = ['id', '=', $id];
        if ($this->auth_group_id != 5 && !in_array($this->userId, config('app.ADMINISTRATOR'))) {
            $map[] = ['user_id', '=', $this->userId];
        }
        if (Db::name('urls_sqlmap')->where($map)->delete()) {
            return redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->error('删除失败');
        }
    }


    // 批量删除
    public function batch_del(Request $request){
        return $this->batch_del_that($request,'urls_sqlmap');
    }
}
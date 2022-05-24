<?php

namespace app\controller;

use app\Request;
use think\facade\Db;
use think\facade\View;

class Mobsfscan extends Common
{
    public function index(Request $request){
        $where = [];
        $map = [];
        $pageSize = 20;
        if ($this->auth_group_id != 5 && !in_array($this->userId, config('app.ADMINISTRATOR'))) {
            $where[] = ['user_id', '=', $this->userId];
            $map[] = ['user_id', '=', $this->userId];
        }
        $list = Db::table('mobsfscan')->where($where)->order('id', 'desc')->paginate(['list_rows' => $pageSize, 'query' => $request->param()]);
        $data['list'] = $list->items();
        foreach ($data['list'] as &$v) {
            $v['code_name'] = Db::name('code')->where('id',$v['code_id'])->value('name');
        }
        $data['page'] = $list->render();
        //查询项目列表
        $data['projectList'] = $this->getMyCodeList();
        $data['fileList'] = [];
        $data['check_status_list'] = ['未审计', '有效漏洞', '无效漏洞'];
        $data['CategoryList'] = Db::table('semgrep')->where($map)->group('check_id')->column('check_id');
        return View::fetch('index', $data);
    }

    public function batch_audit(Request $request){
        return $this->batch_audit_that($request,'mobsfscan');
    }

    public function del(Request $request)
    {
        $id = $request->param('id');
        $where[] = ['id','=',$id];
        if ($this->auth_group_id != 5 && !in_array($this->userId, config('app.ADMINISTRATOR'))) {
            $where[] = ['user_id', '=', $this->userId];
        }
        if (Db::name('mobsfscan')->where('id',$id)->delete()) {
            return redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->error('删除失败');
        }
    }

    // 批量删除
    public function batch_del(Request $request){
        return $this->batch_del_that($request,'mobsfscan');
    }
}
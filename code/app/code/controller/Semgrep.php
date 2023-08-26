<?php

namespace app\code\controller;

use app\controller\Common;
use think\facade\Db;
use think\facade\View;
use think\Request;

class Semgrep extends Common
{
    public function index(Request $request){
        $data = [];
        $search = $request->param('search', '');
        $pageSize = 25;
        $where[] = ['is_delete', '=', 0];
        $map[] = ['is_delete', '=', 0];
        $code_id = $request->param('code_id');
        $level = $request->param('level'); // 等级
        $Category = $request->param('Category');   // 分类
        $filename = $request->param('filename');   // 文件名
        $filetype = $request->param('filetype');   // 文件名
        $check_status = $request->param('check_status');   // 审核状态
        if (!empty($code_id)) {
            $where[] = ['code_id', '=', $code_id];
        }
        if (!empty($level)) {
            $where[] = ['extra_severity', '=', $level];
        }
        if (!empty($Category)) {
            $where[] = ['check_id', '=', "data.tools.semgrep.$Category"];
        }
        if (!empty($filename)) {
            $where[] = ['path', '=', "./data/codeCheck/$filename"];
        }
        if (!empty($filetype)) {
            $where[] = ['path', 'like', "%.$filetype"];
        }
        if ($check_status !== null && in_array($check_status, [0, 1, 2])) {
            $where[] = ['check_status', '=', $check_status];
        }
        if (!empty($search)) {
            $where[] = ['check_id', 'like', "%{$search}%"];
        }

        $list = Db::table('semgrep')->where($where)->order('id', 'desc')->paginate(['list_rows' => $pageSize, 'query' => $request->param()]);
        $data['list'] = $list->items();
        $data['page'] = $list->render();

        $projectArr = Db::table('code')->where($map)->select()->toArray();
        $projectArr = array_column($projectArr, null, 'id');
        $data['projectArr'] = $projectArr;
        $data['CategoryList'] = Db::table('semgrep')->where($map)->group('check_id')->column('check_id');

        $data['fileList'] = Db::table('semgrep')->where($map)->group('path')->column('path');
        $data['fileList'] = array_map('basename',$data['fileList']);
        $data['check_status_list'] = ['未审计', '有效漏洞', '无效漏洞'];
        //查询项目列表
        $data['projectList'] = $this->getMyCodeList();
        return View::fetch('index', $data);
    }


    public function details(Request $request)
    {
        $id = $request->param('id');
        if (!$id) {
            $this->error('参数不能为空');
        }
        $where[] = ['id', '=', $id];
        $map = [];

        $info = Db::table('semgrep')->where($where)->find();
        if (!$info) {
            $this->error('数据不存在');
        }
        $upper_id = Db::name('semgrep')->where('id', '<', $id)->where($map)->order('id', 'desc')->value('id');
        $info['upper_id'] = $upper_id ?: $id;
        $lower_id = Db::name('semgrep')->where('id', '>', $id)->where($map)->order('id', 'asc')->value('id');
        $info['lower_id'] = $lower_id ?: $id;
        $projectInfo = Db::name('code')->where($map)->where('id', $info['code_id'])->find();
        $data['project'] = $projectInfo;
        $info['project_name'] = $projectInfo['name'] ?? '';

        $data['info'] = $info;
        return View::fetch('details', $data);
    }

    public function batch_audit(Request $request){
        return $this->batch_audit_that($request,'semgrep');
    }

    public function del(Request $request)
    {
        $id = $request->param('id');
        $map[] = ['id', '=', $id];
        
        if (Db::name('semgrep')->where($map)->delete()) {
            return redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->error('删除失败');
        }
    }

    // 批量删除
    public function batch_del(Request $request)
    {
        return $this->batch_del_that($request,'semgrep');
    }
}
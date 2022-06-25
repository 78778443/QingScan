<?php

namespace app\controller;

use think\facade\Db;
use think\facade\View;
use think\Request;

class Kunlun extends Common
{
    public $db;

    public function initialize(){
        parent::initialize();
        $this->db = Db::connect('kunlun');
    }

    public function index(Request $request)
    {
        exit();
        $where = [];
        $pageSize = 25;
        $search = $request->param('search', '');
        $code_id = $request->param('code_id');
        $level = $request->param('level'); // 等级
        $Category = $request->param('Category');   // 分类
        $filename = $request->param('filename');   // 文件名
        $check_status = $request->param('check_status');   // 审核状态
        if (!empty($code_id)) {
            $where[] = ['code_id', '=', $code_id];
        }
        if (!empty($level)) {
            $where[] = ['is_active', '=', $level];
        }
        if (!empty($Category)) {
            $where[] = ['result_type', '=', $Category];
        }
        if (!empty($filename)) {
            $where[] = ['vulfile_path', '=', $filename];
        }
        if ($check_status !== null && in_array($check_status, [0, 1, 2])) {
            $where[] = ['check_status', '=', $check_status];
        }
        $map = [];
        if ($this->auth_group_id != 5 && !in_array($this->userId, config('app.ADMINISTRATOR'))) {
            $where[] = ['user_id', '=', $this->userId];
            $map[] = ['user_id', '=', $this->userId];
        }
        $semgrepApi = $this->db->table("index_scanresulttask");

        //->order('id', 'desc')
        $list = $semgrepApi->where($where)->paginate($pageSize);
        // 获取分页显示
        $data['list'] = $list->toArray()['data'];
        $data['page'] = $list->render();

        $projectArr = Db::connect('kunlun')->table("index_project")->where($map)->select()->toArray();
        $projectArr = array_column($projectArr, null, 'id');
        $data['projectArr'] = $projectArr;
        $data['CategoryList'] = $semgrepApi->where($where)->group('result_type')->column('result_type');
        $data['projectList'] = $this->getMyCodeList();
        $data['fileList'] = $semgrepApi->where($where)->group('vulfile_path')->column('vulfile_path');
        $data['check_status_list'] = ['未审计', '有效漏洞', '无效漏洞'];
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
        if ($this->auth_group_id != 5 && !in_array($this->userId, config('app.ADMINISTRATOR'))) {
            $where[] = ['user_id', '=', $this->userId];
            $map[] = ['user_id', '=', $this->userId];
        }
        $semgrepApi = $this->db->table("index_scanresulttask");
        $info = $semgrepApi->where($where)->find();
        if (!$info) {
            $this->error('数据不存在');
        }
        $upper_id = $this->db->table("index_scanresulttask")->where($map)->where('id', '<', $id)->order('id', 'desc')->value('id');
        $info['upper_id'] = $upper_id ?: $id;
        $lower_id = $this->db->table("index_scanresulttask")->where($map)->where('id', '>', $id)->order('id', 'asc')->value('id');
        $info['lower_id'] = $lower_id ?: $id;

        $data['info'] = $info;
        return View::fetch('kunlun_details', $data);
    }

    // 批量审核
    public function batch_audit(Request $request){
        $ids = $request->param('ids');
        $check_status = $request->param('check_status');
        $this->addUserLog('kunlun',"批量审核数据[$ids]");
        if (!$ids) {
            return $this->apiReturn(0,[],'请先选择要审核的数据');
        }
        $map[] = ['id','in',$ids];
        if ($this->auth_group_id != 5 && !in_array($this->userId, config('app.ADMINISTRATOR'))) {
            $map[] = ['user_id', '=', $this->userId];
        }
        if (Db::connect('kunlun')->table("index_scanresulttask")->where($map)->update(['check_status'=>$check_status,'update_time'=>date('Y-m-d H:i:s',time())])) {
            return $this->apiReturn(1,[],'审核成功');
        } else {
            return $this->apiReturn(0,[],'审核失败');
        }
    }

    // 批量删除
    public function batch_del(Request $request)
    {
        $ids = $request->param('ids');
        $this->addUserLog('kunlun',"批量删除数据[$ids]");
        if (!$ids) {
            return $this->apiReturn(0,[],'请先选择要删除的数据');
        }
        $map[] = ['id','in',$ids];
        if ($this->auth_group_id != 5 && !in_array($this->userId, config('app.ADMINISTRATOR'))) {
            $map[] = ['user_id', '=', $this->userId];
        }
        if (Db::connect('kunlun')->table("index_scanresulttask")->where($map)->delete()) {
            return $this->apiReturn(1,[],'删除成功');
        } else {
            return $this->apiReturn(0,[],'删除失败');
        }
    }
}
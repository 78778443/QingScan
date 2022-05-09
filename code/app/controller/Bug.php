<?php

namespace app\controller;

use app\BaseController;
use think\facade\Db;
use think\facade\View;
use think\Request;

class Bug extends Common
{

    /**
     * @return int
     * @Route("Index/app")
     */
    public function awvs(Request $request)
    {
        $pageSize = 25;
        $where[] = ['is_delete', '=', 0];
        $search = $request->param('search');
        if (!empty($search)) {
            $where[] = ['vt_name|affects_url', 'like', "%{$search}%"];
        }
        $app_id = $request->param('app_id');
        $level = $request->param('level'); // 等级
        $Category = $request->param('Category');   // 分类
        $filename = $request->param('filename');   // 文件名
        $check_status = $request->param('check_status');   // 审核状态
        if (!empty($app_id)) {
            $where[] = ['app_id', '=', $app_id];
        }
        if (!empty($level)) {
            $where[] = ['severity', '=', $level];
        }
        if (!empty($Category)) {
            $where[] = ['vt_name', '=', $Category];
        }
        if (!empty($filename)) {
            $where[] = ['affects_url', '=', $filename];
        }
        if ($check_status !== null && in_array($check_status, [0, 1, 2])) {
            $where[] = ['check_status', '=', $check_status];
        }
        if ($this->auth_group_id != 5 && !in_array($this->userId, config('app.ADMINISTRATOR'))) {
            $where[] = ['user_id', '=', $this->userId];
        }
        $list = Db::table('awvs_vuln')->where($where)->paginate([
            'list_rows' => $pageSize,//每页数量
            'query' => $request->param(),
        ]);
        $data['list'] = $list->items();
        $data['page'] = $list->render();
        $data['projectList'] = $this->getMyAppList();
        $data['CategoryList'] = Db::table('awvs_vuln')->where($where)->group('vt_name')->column('vt_name');
        //$data['projectList'] = Db::table('awvs_vuln')->where($where)->group('severity')->column('severity');
        $data['fileList'] = Db::table('awvs_vuln')->where($where)->group('affects_url')->column('affects_url');
        $data['check_status_list'] = ['未审计', '有效漏洞', '无效漏洞'];

        return View::fetch('awvs_list', $data);
    }

    public function awvs_del(Request $request)
    {
        $id = $request->param('id');
        if (Db::name('awvs_vuln')->where('id', $id)->delete()) {
            return redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->error('删除失败');
        }
    }


    // 批量删除
    public function awvs_batch_del(Request $request){
        return $this->batch_del_that($request,'awvs_vuln');
    }

    public function xray(Request $request)
    {
        $page = $request->param('page', 1);
        $pageSize = 25;
        $data['list'] = Db::table('xray')->limit($pageSize)->page($page)->select()->toArray();
        $data['page'] = Db::name('xray')->paginate($pageSize)->render();


        return View::fetch('xray_list', $data);
    }

    public function pocsuite(Request $request)
    {
        $page = $request->param('page', 1);
        $pageSize = 25;
        $data['list'] = Db::table('pocsuite3')->limit($pageSize)->page($page)->select()->toArray();
        $data['page'] = Db::name('pocsuite3')->paginate($pageSize)->render();
        return View::fetch('pocsuite_list', $data);
    }
}
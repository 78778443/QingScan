<?php

namespace app\controller;

use app\BaseController;
use think\facade\Db;
use think\facade\View;

class Bug extends Common
{

    /**
     * @return int
     * @Route("Index/app")
     */
    public function awvs()
    {
        $pageSize = 25;
        $where[] = ['is_delete','=',0];
        $search = getParam('search','');
        if (!empty($search)) {
            $where[] = ['vt_name|affects_url','like',"%{$search}%"];
        }
        $app_id = getParam('app_id');
        $level = getParam('level'); // 等级
        $Category = getParam('Category');   // 分类
        $filename = getParam('filename');   // 文件名
        $check_status = getParam('check_status');   // 审核状态
        if (!empty($app_id)) {
            $where[] = ['app_id','=',$app_id];
        }
        if (!empty($level)) {
            $where[] = ['severity','=',$level];
        }
        if (!empty($Category)) {
            $where[] = ['vt_name','=',$Category];
        }
        if (!empty($filename)) {
            $where[] = ['affects_url','=',$filename];
        }
        if ($check_status !== null && in_array($check_status,[0,1,2])) {
            $where[] = ['check_status','=',$check_status];
        }
        if (!empty($search)) {
            //$where[] = ['app_id','like',"%{$search}%"];
        }
        if ($this->auth_group_id != 5 && !in_array($this->userId, config('app.ADMINISTRATOR'))) {
            $where[] = ['user_id', '=', $this->userId];
        }
        $list = Db::table('awvs_vuln')->where($where)->paginate($pageSize);
        $data['list'] = $list->toArray()['data'];
        $data['page'] = $list->render();
        $projectArr = Db::table('app')->select()->toArray();
        $projectArr = array_column($projectArr, null, 'id');
        $data['projectArr'] = $projectArr;
        $data['CategoryList'] = Db::table('awvs_vuln')->where($where)->group('vt_name')->column('vt_name');
        //$data['projectList'] = Db::table('awvs_vuln')->where($where)->group('severity')->column('severity');
        $data['fileList'] = Db::table('awvs_vuln')->where($where)->group('affects_url')->column('affects_url');
        $data['check_status_list'] = ['未审计','有效漏洞','无效漏洞'];

        return View::fetch('awvs_list', $data);
    }

    public function awvs_del()
    {
        $id = getParam('id');
        if (Db::name('awvs_vuln')->where('id',$id)->delete()) {
            return redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->error('删除失败');
        }
    }

    public function xray()
    {
        $page = getParam('page', 1);
        $pageSize = 25;
        $data['list'] = Db::table('xray')->limit($pageSize)->page($page)->select()->toArray();
        $data['page'] = Db::name('xray')->paginate($pageSize)->render();


        return View::fetch('xray_list', $data);
    }

    public function pocsuite()
    {
        $page = getParam('page', 1);
        $pageSize = 25;
        $data['list'] = Db::table('pocsuite3')->limit($pageSize)->page($page)->select()->toArray();
        $data['page'] = Db::name('pocsuite3')->paginate($pageSize)->render();


        return View::fetch('pocsuite_list', $data);
    }




}
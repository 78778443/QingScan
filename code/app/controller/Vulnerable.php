<?php

namespace app\controller;

use app\BaseController;
use think\facade\Db;
use think\facade\View;
use think\Request;

class Vulnerable extends Common
{

    public function index()
    {
        $where[] = ['is_delete','=',0];
        $search = getParam('search','');
        if (!empty($search)) {
            $where[] = ['name|cve_num|cnvd_num','like',"%{$search}%"];
        }
        $vul_level = getParam('vul_level'); // 等级
        $product_field = getParam('product_field');   // 行业
        $product_type = getParam('product_type');   // 项目类型
        $product_cate = getParam('product_cate');   // 平台分类
        $check_status = getParam('check_status');   // 审核类型
        if (!empty($vul_level)) {
            $where[] = ['vul_level','=',$vul_level];
        }
        if (!empty($product_field)) {
            $where[] = ['product_field','=',$product_field];
        }
        if (!empty($product_field)) {
            $where[] = ['product_field','=',$product_field];
        }
        if (!empty($product_type)) {
            $where[] = ['product_type','=',$product_type];
        }
        if (!empty($product_cate)) {
            $where[] = ['product_cate','=',$product_cate];
        }
        if ($check_status !== null && in_array($check_status,[0,1,2])) {
            $where[] = ['check_status','=',$check_status];
        }
        if ($this->auth_group_id != 5 && !in_array($this->userId, config('app.ADMINISTRATOR'))) {
            $where[] = ['user_id', '=', $this->userId];
        }
        $pageSize = 25;
        $list = Db::table('vulnerable')->where($where)->order('id','desc')->paginate($pageSize);
        $data = [];
        $data['list'] = $list->toArray()['data'];
        $data['page'] = $list->render();
        $data['vul_level'] = Db::table('vulnerable')->where($where)->where('vul_level','<>','')->group('vul_level')->column('vul_level');
        $data['product_field'] = Db::table('vulnerable')->where($where)->where('product_field','<>','')->group('product_field')->column('product_field');
        $data['product_type'] = Db::table('vulnerable')->where($where)->where('product_type','<>','')->group('product_type')->column('product_type');
        $data['product_cate'] = Db::table('vulnerable')->where($where)->where('product_cate','<>','')->group('product_cate')->column('product_cate');
        $data['check_status_list'] = ['未审计','有效漏洞','无效漏洞'];


        return View::fetch('index', $data);
    }

    public function pocsuite()
    {
        $page = getParam('page', 1);
        $pageSize = 25;
        $data['list'] = Db::table('pocsuite3')->where('is_delete','=',0)->limit($pageSize)->page($page)->select()->toArray();
        $data['page'] = Db::name('pocsuite3')->where('is_delete','=',0)->paginate($pageSize)->render();


        return View::fetch('pocsuite_list', $data);
    }


    public function details(){
        $id = getParam('id');
        if (!$id) {
            $this->error('参数不能为空');
        }
        $where[] = ['id','=',$id];
        if ($this->auth_group_id != 5 && !in_array($this->userId, config('app.ADMINISTRATOR'))) {
            $where[] = ['user_id', '=', $this->userId];
        }
        $info = Db::table('vulnerable')->where($where)->find();
        if (!$info) {
            $this->error('数据不存在');
        }
        $upper_id = Db::name('vulnerable')->where('id','<',$id)->order('id','desc')->value('id');
        $info['upper_id'] = $upper_id?:$id;
        $lower_id = Db::name('vulnerable')->where('id','>',$id)->order('id','asc')->value('id');
        $info['lower_id'] = $lower_id?:$id;

        $data['info'] = $info;
        return View::fetch('details', $data);
    }

    public function add(Request $request){
        if ($request->isPost()) {
            $data['nature'] = $request->param('nature');
            $data['name'] = $request->param('name');
            $data['vul_num'] = $request->param('vul_num');
            $data['cve_num'] = $request->param('cve_num');
            $data['cnvd_num'] = $request->param('cnvd_num');
            $data['cnnvd_num'] = $request->param('cnnvd_num');
            $data['src_num'] = $request->param('src_num');
            $data['vul_level'] = $request->param('vul_level');
            $data['vul_type'] = $request->param('vul_type');
            $data['cwe'] = $request->param('cwe');
            $data['vul_cvss'] = $request->param('vul_cvss');
            $data['cvss_vector'] = $request->param('cvss_vector');
            $data['open_time'] = $request->param('open_time');
            $data['vul_repair_time'] = $request->param('vul_repair_time');
            $data['vul_source'] = $request->param('vul_source');
            $data['temp_plan'] = $request->param('temp_plan');
            $data['temp_plan_s3'] = $request->param('temp_plan_s3');
            $data['formal_plan'] = $request->param('formal_plan');
            $data['patch_s3'] = $request->param('patch_s3');
            $data['patch_url'] = $request->param('patch_url');
            $data['patch_use_func'] = $request->param('patch_use_func');
            $data['cpe'] = $request->param('cpe');
            $data['product_name'] = $request->param('product_name');
            $data['product_open'] = $request->param('product_open');
            $data['product_store'] = $request->param('product_store');
            $data['store_website'] = $request->param('store_website');
            $data['assem_name'] = $request->param('assem_name');
            $data['affect_ver'] = $request->param('affect_ver');
            $data['ver_open_date'] = $request->param('ver_open_date');
            $data['sub_update_url'] = $request->param('sub_update_url');
            $data['git_url'] = $request->param('git_url');
            $data['git_commit_id'] = $request->param('git_commit_id');
            $data['git_fixed_commit_id'] = $request->param('git_fixed_commit_id');
            $data['product_cate'] = $request->param('product_cate');
            $data['product_field'] = $request->param('product_field');
            $data['product_type'] = $request->param('product_type');
            $data['fofa_max'] = $request->param('fofa_max');
            $data['fofa_con'] = $request->param('fofa_con');
            $data['is_pass'] = $request->param('is_pass');
            $data['user_name'] = $request->param('user_name');
            $data['is_sub_attack'] = $request->param('is_sub_attack');
            $data['temp_plan_s3_hash'] = $request->param('temp_plan_s3_hash');
            $data['patch_s3_hash'] = $request->param('patch_s3_hash');
            $data['is_pass_attack'] = $request->param('is_pass_attack');
            $data['auditor'] = $request->param('auditor');
            $data['cause'] = $request->param('cause');
            $data['is_poc'] = $request->param('is_poc');
            $data['scan_time'] = $request->param('scan_time');
            $data['created_at'] = date('Y-m-d H:i:s',time());
            $data['user_id'] = $this->userId;
            $data['user_name'] = $this->userInfo['nickname'];
            if(!$data['scan_time']) {
                $data['scan_time'] = date('Y-m-d H:i:s',time());
            }
            if (Db::name('vulnerable')->insert($data)) {
                $this->success('添加成功','index');
            } else {
                $this->error('添加失败');
            }
        } else {
            $data = [];
            return View::fetch('add', $data);
        }
    }

    public function edit(Request $request){
        $id = $request->param('id');
        if (!$id) {
            $this->error('参数错误');
        }
        $where[] = ['id','=',$id];
        if ($this->auth_group_id != 5 && !in_array($this->userId, config('app.ADMINISTRATOR'))) {
            $where[] = ['user_id', '=', $this->userId];
        }
        if ($request->isPost()) {
            $data['nature'] = $request->param('nature');
            $data['name'] = $request->param('name');
            $data['vul_num'] = $request->param('vul_num');
            $data['cve_num'] = $request->param('cve_num');
            $data['cnvd_num'] = $request->param('cnvd_num');
            $data['cnnvd_num'] = $request->param('cnnvd_num');
            $data['src_num'] = $request->param('src_num');
            $data['vul_level'] = $request->param('vul_level');
            $data['vul_type'] = $request->param('vul_type');
            $data['cwe'] = $request->param('cwe');
            $data['vul_cvss'] = $request->param('vul_cvss');
            $data['cvss_vector'] = $request->param('cvss_vector');
            $data['open_time'] = $request->param('open_time');
            $data['vul_repair_time'] = $request->param('vul_repair_time');
            $data['vul_source'] = $request->param('vul_source');
            $data['temp_plan'] = $request->param('temp_plan');
            $data['temp_plan_s3'] = $request->param('temp_plan_s3');
            $data['formal_plan'] = $request->param('formal_plan');
            $data['patch_s3'] = $request->param('patch_s3');
            $data['patch_url'] = $request->param('patch_url');
            $data['patch_use_func'] = $request->param('patch_use_func');
            $data['cpe'] = $request->param('cpe');
            $data['product_name'] = $request->param('product_name');
            $data['product_open'] = $request->param('product_open');
            $data['product_store'] = $request->param('product_store');
            $data['store_website'] = $request->param('store_website');
            $data['assem_name'] = $request->param('assem_name');
            $data['affect_ver'] = $request->param('affect_ver');
            $data['ver_open_date'] = $request->param('ver_open_date');
            $data['sub_update_url'] = $request->param('sub_update_url');
            $data['git_url'] = $request->param('git_url');
            $data['git_commit_id'] = $request->param('git_commit_id');
            $data['git_fixed_commit_id'] = $request->param('git_fixed_commit_id');
            $data['product_cate'] = $request->param('product_cate');
            $data['product_field'] = $request->param('product_field');
            $data['product_type'] = $request->param('product_type');
            $data['fofa_max'] = $request->param('fofa_max');
            $data['fofa_con'] = $request->param('fofa_con');
            $data['is_pass'] = $request->param('is_pass');
            $data['is_sub_attack'] = $request->param('is_sub_attack');
            $data['temp_plan_s3_hash'] = $request->param('temp_plan_s3_hash');
            $data['patch_s3_hash'] = $request->param('patch_s3_hash');
            $data['is_pass_attack'] = $request->param('is_pass_attack');
            $data['auditor'] = $request->param('auditor');
            $data['cause'] = $request->param('cause');
            $data['is_poc'] = $request->param('is_poc');
            $data['scan_time'] = $request->param('scan_time');
            $data['updated_at'] = date('Y-m-d H:i:s',time());
            if(!$data['scan_time']) {
                $data['scan_time'] = date('Y-m-d H:i:s',time());
            }
            if (Db::name('vulnerable')->where($where)->update($data)) {
                $this->success('编辑成功','index');
            } else {
                $this->error('编辑失败');
            }
        } else {
            $data['info'] = Db::name('vulnerable')->where($where)->find();
            if (!$data['info']) {
                $this->error('数据不存在');
            }
            return View::fetch('edit', $data);
        }
    }

    public function vulnerable_del()
    {
        $id = getParam('id');
        $where[] = ['id','=',$id];
        if ($this->auth_group_id != 5 && !in_array($this->userId, config('app.ADMINISTRATOR'))) {
            $where[] = ['user_id', '=', $this->userId];
        }
        if (Db::name('vulnerable')->where($where)->delete()) {
            return redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->error('删除失败');
        }
    }

    public function pocsuite_del()
    {
        $id = getParam('id');
        $where[] = ['id','=',$id];
        if ($this->auth_group_id != 5 && !in_array($this->userId, config('app.ADMINISTRATOR'))) {
            $where[] = ['user_id', '=', $this->userId];
        }
        if (Db::name('pocsuite3')->where('id',$id)->delete()) {
            return redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->error('删除失败');
        }
    }
}
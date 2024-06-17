<?php

namespace app\controller;

use app\Request;
use think\facade\Db;
use think\facade\View;

class Vulnerable extends Common
{

    public function index(Request $request)
    {

        if (function_exists('autoVulnList')) autoVulnList();

        $where = [];
        $search = $request->param('search', '');
        if (!empty($search)) {
            $where[] = ['name|cve_num|cnvd_num', 'like', "%{$search}%"];
        }
        $vul_level = $request->param('vul_level'); // 等级
        $product_field = $request->param('product_field');   // 行业
        $product_type = $request->param('product_type');   // 项目类型
        $product_cate = $request->param('product_cate');   // 平台分类
        $check_status = $request->param('check_status');   // 审核类型
        if (!empty($vul_level)) {
            $where[] = ['vul_level', '=', $vul_level];
        }
        if (!empty($product_field)) {
            $where[] = ['product_field', '=', $product_field];
        }
        if (!empty($product_field)) {
            $where[] = ['product_field', '=', $product_field];
        }
        if (!empty($product_type)) {
            $where[] = ['product_type', '=', $product_type];
        }
        if (!empty($product_cate)) {
            $where[] = ['product_cate', '=', $product_cate];
        }
        if ($check_status !== null && in_array($check_status, [0, 1, 2])) {
            $where[] = ['check_status', '=', $check_status];
        }

        $pageSize = 25;
        $list = Db::table('vulnerable')->where($where)->order('id', 'desc')->paginate([
            'list_rows' => $pageSize,//每页数量
            'query' => $request->param(),
        ]);
        $data = [];
        $data['list'] = $list->items();
        $data['page'] = $list->render();


        return View::fetch('index', $data);
    }

    public function pocsuite(Request $request)
    {
        $pageSize = 25;
        $where = [];
        $search = $request->param('search', '');
        if (!empty($search)) {
            $where[] = ['name|name|cms', 'like', "%{$search}%"];
        }
        $list = Db::table('pocsuite3')->where('is_delete', '=', 0)->paginate([
            'list_rows' => $pageSize,//每页数量
            'query' => $request->param(),
        ]);
        $data['list'] = $list->items();
        $data['page'] = $list->render();
        return View::fetch('pocsuite_list', $data);
    }


    public function details(Request $request)
    {
        $id = $request->param('id');
        if (!$id) {
            $this->error('参数不能为空');
        }
        $where[] = ['id', '=', $id];

        $info = Db::table('vulnerable')->where($where)->find();
        if (!$info) {
            $this->error('数据不存在');
        }
        $upper_id = Db::name('vulnerable')->where('id', '<', $id)->order('id', 'desc')->value('id');
        $info['upper_id'] = $upper_id ?: $id;
        $lower_id = Db::name('vulnerable')->where('id', '>', $id)->order('id', 'asc')->value('id');
        $info['lower_id'] = $lower_id ?: $id;

        $data['info'] = $info;
        return View::fetch('details', $data);
    }

    public function add(Request $request)
    {
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
            $data['created_at'] = date('Y-m-d H:i:s', time());
            $data['user_id'] = $this->userId;
            $data['user_name'] = $this->userInfo['nickname'];
            if (!$data['scan_time']) {
                $data['scan_time'] = date('Y-m-d H:i:s', time());
            }
            if (Db::name('vulnerable')->insert($data)) {
                $this->success('添加成功', 'index');
            } else {
                $this->error('添加失败');
            }
        } else {
            $data = [];
            return View::fetch('add', $data);
        }
    }

    public function edit(Request $request)
    {
        $id = $request->param('id');
        $this->addUserLog('缺陷列表', "修改缺陷列表数据[{$id}]");
        if (!$id) {
            $this->error('参数错误');
        }
        $where[] = ['id', '=', $id];

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
            $data['updated_at'] = date('Y-m-d H:i:s', time());
            if (!$data['scan_time']) {
                $data['scan_time'] = date('Y-m-d H:i:s', time());
            }
            if (Db::name('vulnerable')->where($where)->update($data)) {
                $this->success('编辑成功', 'index');
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

    public function vulnerable_del(Request $request)
    {
        $id = $request->param('id');
        if (!$id) {
            $this->error('参数不存在');
        }
        $this->addUserLog('缺陷列表', "删除缺陷列表数据[{$id}]");
        $where[] = ['id', '=', $id];

        if (Db::name('vulnerable')->where($where)->delete()) {
            return redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->error('删除失败');
        }
    }

    // 批量删除
    public function vulnerable_batch_del(Request $request)
    {
        return $this->batch_del_that($request, 'vulnerable');
    }

    public function pocsuite_del(Request $request)
    {
        $id = $request->param('id');
        if (!$id) {
            $this->error('参数不存在');
        }
        $this->addUserLog('漏洞实例', "删除漏洞实例数据[{$id}]");
        $where[] = ['id', '=', $id];

        if (Db::name('pocsuite3')->where('id', $id)->delete()) {
            return redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->error('删除失败');
        }
    }

    // 批量删除
    public function pocsuite_batch_del(Request $request)
    {
        return $this->batch_del_that($request, 'pocsuite3');
    }

    public function add_pocsuite(Request $request)
    {
        if ($request->isPost()) {
            $data = $request->post();
            $data['user_id'] = $this->userId;
            if (Db::name('pocsuite3')->insert($data)) {
                return $this->success('数据添加成功');
            } else {
                return $this->error('数据添加失败');
            }
        } else {
            return View::fetch('add_pocsuite');
        }
    }

    // 批量导入
    public function batch_import(Request $request)
    {
        $file = $_FILES["file"]["tmp_name"];
        $result = $this->importExecl($file);
        if ($result['code'] == 0) {
            $this->error($result['msg']);
        }
        $list = $result['data'];
        unset($list[0]);
        $temp_data = [];
        foreach ($list as $k => $v) {
            $data['url'] = $v['A'];
            $data['name'] = $v['B'];
            $data['ssv_id'] = $v['C'];
            $data['cms'] = $v['D'];
            $data['version'] = $v['E'];
            $data['is_max'] = $v['F'];
            $data['tel'] = $v['G'];
            $data['regaddress'] = $v['H'];
            $data['ip'] = $v['I'];
            $data['CompanyName'] = $v['J'];
            $data['SiteLicense'] = $v['K'];
            $data['CompanyType'] = $v['L'];
            $data['regcapital'] = $v['M'];
            $data['user_id'] = $this->userId;

            $temp_data[] = $data;
        }
        if (Db::name('pocsuite3')->insertAll($temp_data)) {
            $this->success('漏洞实例批量导入成功');
        } else {
            $this->error('漏洞实例批量导入失败');
        }
    }

    public function downloaAppTemplate()
    {
        $file_dir = \think\facade\App::getRootPath() . 'public/static/';
        $file_name = '漏洞实例批量导入模版.xls';
        //以只读和二进制模式打开文件
        $file = fopen($file_dir . $file_name, "rb");

        //告诉浏览器这是一个文件流格式的文件
        Header("Content-type: application/octet-stream");
        //请求范围的度量单位
        Header("Accept-Ranges: bytes");
        //Content-Length是指定包含于请求或响应中数据的字节长度
        Header("Accept-Length: " . filesize($file_dir . $file_name));
        //用来告诉浏览器，文件是可以当做附件被下载，下载后的文件名称为$file_name该变量的值。
        Header("Content-Disposition: attachment; filename=" . $file_name);
        ob_end_clean();
        //读取文件内容并直接输出到浏览器
        echo fread($file, filesize($file_dir . $file_name));
        fclose($file);
        exit ();
    }
}
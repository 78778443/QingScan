<?php

namespace app\controller;

use app\BaseController;
use think\facade\Db;
use think\facade\View;

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
        $where['id'] = $id;
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

    public function add(){
        if (request()->isPost()) {
            $data = $_POST;
            $data['created_at'] = date('Y-m-d H:i:s',time());
            $data['updated_at'] = date('Y-m-d H:i:s',time());
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

    public function edit(){
        $id = getParam('id');
        if (!$id) {
            $this->error('参数错误');
        }
        if (request()->isPost()) {
            $data = $_POST;
            $data['updated_at'] = date('Y-m-d H:i:s',time());
            if(!$data['scan_time']) {
                $data['scan_time'] = date('Y-m-d H:i:s',time());
            }
            if (Db::name('vulnerable')->where('id',$id)->update($data)) {
                $this->success('编辑成功','index');
            } else {
                $this->error('编辑失败');
            }
        } else {
            $data['info'] = Db::name('vulnerable')->where('id',$id)->find();
            return View::fetch('edit', $data);
        }
    }

    public function vulnerable_del()
    {
        $id = getParam('id');
        if (Db::name('vulnerable')->where('id',$id)->update(['is_delete'=>1])) {
            return redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->error('删除失败');
        }
    }

    public function pocsuite_del()
    {
        $id = getParam('id');
        if (Db::name('pocsuite3')->where('id',$id)->update(['is_delete'=>1])) {
            return redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->error('删除失败');
        }
    }
}
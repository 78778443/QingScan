<?php

namespace app\controller;

use think\facade\Db;
use think\facade\View;

class Plugin extends Common
{

    public function index()
    {
        $pageSize = 20;
        $where[] = ['is_delete','=',0];
        $search = getParam('search');
        if ($search) {
            $where[] = ['name','like',"%{$search}%"];
        }
        if ($this->auth_group_id != 5 && !in_array($this->userId, config('app.ADMINISTRATOR'))) {
            $where[] = ['user_id', '=', $this->userId];
        }
        $list = Db::table('plugin')->where($where)->order("id", 'desc')->paginate($pageSize);
        $data['list'] = $list->items();
        $data['page'] = $list->render();
        return View::fetch('index', $data);
    }

    public function add()
    {
        if (request()->isPost()) {
            $data['name'] = getParam('name');
            if ($this->auth_group_id != 5 && !in_array($this->userId, config('app.ADMINISTRATOR'))) {
                $data['user_id'] = $this->userId;
            }
            $data['cmd'] = getParam('cmd');
            $data['result_file'] = getParam('result_file');
            $data['status'] = getParam('status');
            $data['result_type'] = getParam('result_type');
            $data['create_time'] = date('Y-m-d h:i:s', time());
            if (Db::name('plugin')->insert($data)) {
                $this->success('数据添加成功','index');
            } else {
                $this->error('数据添加失败，请稍候再试');
            }
        } else {
            return View::fetch('add');
        }
    }


    public function edit()
    {
        $id = getParam('id');
        $where[] = ['id','=',$id];
        $where[] = ['is_delete','=',0];
        if ($this->auth_group_id != 5 && !in_array($this->userId, config('app.ADMINISTRATOR'))) {
            $where[] = ['user_id', '=', $this->userId];
        }
        $info = Db::name('plugin')->where($where)->find();
        if (!$info) {
            $this->error('数据不存在');
        }
        if (request()->isPost()) {
            $data['name'] = getParam('name');
            $data['cmd'] = getParam('cmd');
            $data['result_type'] = getParam('result_type');
            $data['result_file'] = getParam('result_file');
            $data['status'] = getParam('status');
            if (Db::name('plugin')->where('id', $id)->update($data)) {
                $this->success('数据编辑成功');
            } else {
                $this->error('数据编辑失败，请稍候再试');
            }
        } else {
            $data['info'] = $info;
            return View::fetch('edit', $data);
        }
    }


    public function del()
    {
        $id = getParam('id');
        $map[] = ['id', '=', $id];
        if ($this->auth_group_id != 5 && !in_array($this->userId, config('app.ADMINISTRATOR'))) {
            $map[] = ['user_id', '=', $this->userId];
        }
        if (Db::name('plugin')->where($map)->update(['is_delete'=>1])) {
            $this->success('数据删除成功');
        } else {
            $this->error('数据删除失败');
        }
    }
}
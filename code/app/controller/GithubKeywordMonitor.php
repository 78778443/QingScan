<?php

namespace app\controller;

use think\facade\Db;
use think\facade\View;

class GithubKeywordMonitor extends Common
{
    public function index()
    {
        $pageSize = 20;
        $where = [];
        $list = Db::table('github_keyword_monitor')->where($where)->order("id", 'desc')->paginate($pageSize);
        $data['list'] = $list->items();
        $data['page'] = $list->render();
        return View::fetch('index', $data);
    }

    public function add()
    {
        if (request()->isPost()) {
            $data['title'] = getParam('title');
            $data['user_id'] = $this->userId;
            $data['create_time'] = date('Y-m-d h:i:s', time());
            if (Db::name('github_keyword_monitor')->insert($data)) {
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
        $info = Db::name('github_keyword_monitor')->where('id', $id)->where('user_id',$this->userId)->find();
        if (!$info) {
            $this->error('数据不存在');
        }
        if (request()->isPost()) {
            ini_set('max_execution_time', 0);
            $data['title'] = getParam('title');
            $data['update_time'] = date('Y-m-d h:i:s', time());
            if (Db::name('github_keyword_monitor')->where('id', $id)->update($data)) {
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
        if (Db::name('github_keyword_monitor')->where('id', $id)->where('user_id',$this->userId)->delete()) {
            $this->success('数据删除成功');
        } else {
            $this->error('数据删除失败');
        }
    }
}
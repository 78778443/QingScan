<?php

namespace app\webscan\controller;

use app\controller\Common;
use think\facade\Db;
use think\facade\View;
use think\Request;

class GithubKeywordMonitor extends Common
{
    public function index(Request $request)
    {
        $pageSize = 20;
        $where = [];
        $search = $request->param('search');
        if (!empty($search)) {
            $where[] = ['title', 'like', "%{$search}%"];
        }
        if ($this->auth_group_id != 5 && !in_array($this->userId,config('app.ADMINISTRATOR'))) {
            $where[] = ['user_id','=',$this->userId];
        }
        $list = Db::table('github_keyword_monitor')->where($where)->order("id", 'desc')->paginate([
            'list_rows' => $pageSize,
            'query' => $request->param()
        ]);
        $data['list'] = $list->items();
        $data['page'] = $list->render();
        return View::fetch('index', $data);
    }

    public function add(Request $request)
    {
        if ($request->isPost()) {
            $data['title'] = $request->param('title');
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


    public function edit(Request $request)
    {
        $id = $request->param('id',0,'intval');
        $info = Db::name('github_keyword_monitor')->where('id', $id)->where('user_id',$this->userId)->find();
        if (!$info) {
            $this->error('数据不存在');
        }
        if ($request->isPost()) {
           
            $data['title'] = $request->param('title');
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


    public function del(Request $request)
    {
        $id = $request->param('id',0,'intval');
        if (!$id) {
            $this->error('参数不能为空');
        }
        $this->addUserLog('github',"删除github关键词[{$id}]");
        $map[] = ['id','=',$id];
        if ($this->auth_group_id != 5 && !in_array($this->userId,config('app.ADMINISTRATOR'))) {
            $map[] = ['user_id','=',$this->userId];
        }
        if (Db::name('github_keyword_monitor')->where($map)->delete()) {
            $this->success('数据删除成功');
        } else {
            $this->error('数据删除失败');
        }
    }
}
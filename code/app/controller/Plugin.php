<?php

namespace app\controller;

use think\facade\Db;
use think\facade\View;
use think\Request;

class Plugin extends Common
{

    public function index(Request $request)
    {
        //echo '<pre>';
        /*$where[] = ['is_read','=',1];
        $where[] = ['plugin_name','=','TideFinger'];
        $where[] = ['scan_type','=',0];
        $where[] = ['log_type','=',1];
        $results = Db::table("plugin_scan_log")->where($where)->field('id,app_id,plugin_id,user_id,content,file_content')->select()->toArray();
        foreach ($results as $val) {
            echo $val['content'],'<br>';
            $regex = '/32m (.*?) \[0m/';
            preg_match($regex, $val['content'], $content);
            var_dump($content);
            exit;
            $arr = explode(' [0m [1;32m ',$val['content']);
            var_dump($arr);
        }
        exit;*/
        $pageSize = 20;
        $where[] = ['is_delete','=',0];
        $search = $request->param('search');
        if ($search) {
            $where[] = ['name','like',"%{$search}%"];
        }

        $list = Db::table('plugin')->where($where)->order("id", 'desc')->paginate([
            'list_rows'=> $pageSize,//每页数量
            'query' => $request->param(),
        ]);
        $data['list'] = $list->items();
        $data['page'] = $list->render();
        return redirect('/web/');
    }

    public function add(Request $request)
    {
        if ($request->isPost()) {
            $data['name'] = $request->param('name');
            if ($this->auth_group_id != 5 && !in_array($this->userId, config('app.ADMINISTRATOR'))) {
                $data['user_id'] = $this->userId;
            }
            $data['cmd'] = $request->param('cmd');
            $data['tool_path'] = $request->param('tool_path');
            $data['result_file'] = $request->param('result_file');
            $data['status'] = $request->param('status');
            $data['result_type'] = $request->param('result_type');
            $data['scan_type'] = $request->param('scan_type');
            $data['create_time'] = date('Y-m-d h:i:s', time());
            $data['type'] = $request->param('type');
            if (Db::name('plugin')->insert($data)) {
                $this->success('数据添加成功','index');
            } else {
                $this->error('数据添加失败，请稍候再试');
            }
        } else {
            return redirect('/web/');
        }
    }


    public function edit(Request $request)
    {
        $id = $request->param('id');
        $where[] = ['id','=',$id];
        $where[] = ['is_delete','=',0];

        $info = Db::name('plugin')->where($where)->find();
        if (!$info) {
            $this->error('数据不存在');
        }
        if ($request->isPost()) {
            $data['name'] = $request->param('name');
            $data['cmd'] = $request->param('cmd');
            $data['result_type'] = $request->param('result_type');
            $data['scan_type'] = $request->param('scan_type');
            $data['tool_path'] = $request->param('tool_path');
            $data['result_file'] = $request->param('result_file');
            $data['type'] = $request->param('type');
            $data['status'] = $request->param('status');
            if (Db::name('plugin')->where('id', $id)->update($data)) {
                $this->addUserLog('自定义插件',"编辑数据[{$id}] 成功");
                $this->success('数据编辑成功');
            } else {
                $this->addUserLog('自定义插件',"编辑数据[{$id}] 失败");
                $this->error('数据编辑失败，请稍候再试');
            }
        } else {
            $data['info'] = $info;
            return redirect('/web/');
        }
    }


    public function del(Request $request)
    {
        $id = $request->param('id');
        if (!$id) {
            $this->error('参数不能为空');
        }
        $this->addUserLog('自定义插件',"删除数据[{$id}]");
        $map[] = ['id', '=', $id];
        
        if (Db::name('plugin')->where($map)->delete()) {
            return redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->error('数据删除失败');
        }
    }
}
<?php


namespace app\controller;

use think\facade\Db;
use think\facade\View;
use think\Request;

class ProcessSafe extends Common
{
    public $typeArr = ['黑盒扫描','白盒审计','专项利用','其他','信息收集'];

    public function index(Request $request)
    {
        $pageSize = 20;
        $where = [];
        $search = $request->param('search');
        if ($search) {
            $where[] = ['key|value|note', 'like', "%{$search}%"];
        }
        $type = $request->param('type','');
        if ($type !== '') {
            $type = array_search($type,$this->typeArr);
            $where[] = ['type','=',$type];
        }
        $list = Db::table('process_safe')->where($where)->order("id", 'desc')->paginate([
            'list_rows'=> $pageSize,//每页数量
            'query' => $request->param(),
        ]);
        $data['list'] = $list->items();
        $data['page'] = $list->render();
        return View::fetch('index', $data);
    }


    // 添加管理员
    public function add(Request $request)
    {
        if ($request->isPost()) {
            $data['key'] = $request->param('key');
            $data['value'] = $request->param('value');
            $data['status'] = $request->param('status');
            $data['type'] = $request->param('type');
            $data['note'] = $request->param('note');
            //添加
            if (Db::name('process_safe')->insert($data)) {
                $this->success('添加成功', 'index');
            } else {
                $this->error('添加失败');
            }
        } else {
            ;
            return View::fetch('add');
        }
    }

    public function edit(Request $request)
    {
        $id = $request->param('id');
        if (request()->isPost()) {
            $data['key'] = $request->param('key');
            $data['value'] = $request->param('value');
            $data['status'] = $request->param('status');
            $data['type'] = $request->param('type');
            $data['note'] = $request->param('note');
            if (Db::name('process_safe')->where('id', $id)->update($data)) {
                return redirect(url('index'));
            } else {
                $this->error('信息修改失败');
            }
        } else {
            $map[] = ['id', '=', $id];
            $data['info'] = Db::name('process_safe')->where($map)->find();
            return View::fetch('edit', $data);
        }
    }

    public function del(Request $request)
    {
        $id = $request->param('id');
        if (Db::name('process_safe')->where('id', $id)->delete()) {
            $this->addUserLog('守护进程',"删除数据[$id]成功");
            return redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->addUserLog('守护进程',"删除数据[$id]失败");
            $this->error('删除失败');
        }
    }

    public function showProcess()
    {
        /*$list = Db::table('process_safe')->where('status',0)->select()->toArray();
        var_dump($list);exit;*/
        /*$cmd = "ps -ef | grep -v 'UID'";
        exec($cmd,$info);
        foreach ($info as $val) {
            $arr = array_values(array_filter(explode('  ', $val)));
            if (isset($arr[4]) && in_array_strpos($arr[4],['[php]','[sh]','[cat]','[chrome]'])) {
                $cmd = "kill -9 {$arr['1']}";
                exec($cmd);
            }
        }*/
        $cmd = "ps -ef | grep -v def  | grep -v 'ps -ef' | grep -v 'UID'";
        exec($cmd,$info);
        $data['info'] = $info;
        return View::fetch('show_process', $data);
    }

    public function kill(Request $request){
        $pid = $request->param('pid','intval');
        $cmd = "kill -9 {$pid}";
        exec($cmd);
        return redirect($_SERVER['HTTP_REFERER']);
    }

    public function update_status(Request $request){
        $ids = $request->param('ids');
        $map[] = ['id','in',$ids];
        if ($this->auth_group_id != 5 && !in_array($this->userId, config('app.ADMINISTRATOR'))) {
            $map[] = ['user_id', '=', $this->userId];
        }
        $type = $request->param('type',1);
        if ($type == 1) {
            $status = 1;
        } else {
            $status = 0;
        }
        if (Db::name('process_safe')->where($map)->update(['status'=>$status,'update_time'=>date('Y-m-d h:i:s',time())])) {
            $this->addUserLog('守护进程',"修改守护进程管理状态[{$ids}]成功");
            return $this->apiReturn(1,[],'操作成功');
        } else {
            $this->addUserLog('守护进程',"修改守护进程管理状态[{$ids}]失败");
            return $this->apiReturn(0,[],'操作失败');
        }
    }



    public function edit_tools(Request $request){
        $project_id = $request->param('project_id');
        $type = $request->param('type');
        if ($type == 1) {
            if (!Db::name('app')->where('id',$project_id)->count('id')) {
                $this->error('黑盒扫描项目不存在','index');
            }
        } else {
            if (!Db::name('code')->where('id',$project_id)->count('id')) {
                $this->error('白盒审计项目不存在','index');
            }
        }
        $tools = $request->param('tools');
        $project_tools_data = [];
        if ($tools) {
            foreach ($tools as $k=>$v) {
                $project_tools_data[] = [
                    'type'=>$type,
                    'project_id'=>$project_id,
                    'tools_name'=>$v,
                    'create_time'=>date('Y-m-d H:i:s',time())
                ];
            }
            Db::name('project_tools')->where('project_id',$project_id)->where('type',$type)->delete();
            Db::name('project_tools')->insertAll($project_tools_data);
        }
        if ($type == 1) {
            $this->success('工具修改成功','app/index');
        } else {
            $this->success('工具修改成功','code/index');
        }
    }

    public function tools(Request $request){
        $type = $request->param('type');
        $project_id = $request->param('project_id');
        $where[] = ['type','=',$type];
        $where[] = ['project_id','=',$project_id];
        $tools = Db::name('project_tools')->where($where)->column('tools_name');
        return $this->apiReturn(0,$tools,'ok');
    }
}
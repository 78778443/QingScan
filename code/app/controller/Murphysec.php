<?php

namespace app\controller;

use app\Request;
use think\facade\Db;
use think\facade\View;

class Murphysec extends Common
{
    public $show_level = [
        1=>'强烈建议修复',
        2=>'建议修复',
        3=>'可选修复'
    ];

    public function index(Request $request){
        /*echo '<pre>';
        $result_path = \think\facade\App::getRuntimePath().'tools/murphysec/nps_vnc.json';
        //$result_path = \think\facade\App::getRuntimePath().'tools/murphysec/2.json'; //java
        //$result_path = \think\facade\App::getRuntimePath().'tools/murphysec/3.json';
        //$result_path = \think\facade\App::getRuntimePath().'tools/murphysec/4.json';
        var_dump($result_path);
        $result = json_decode(json_decode(file_get_contents($result_path), true),true);
        var_dump($result);
        exit;*/
        /*if (!isset($result['comps'])) {
            echo '暂未发现漏洞';exit;
        }
        $list = $result['comps'];
        $data = [];
        foreach ($list as $k=>$v) {
            $data[] = [
                'user_id'=>1,
                'code_id'=>1,
                'comp_name'=>$v['comp_name'],
                'min_fixed_version'=>$v['min_fixed_version'],
                'version'=>$v['version'],
                'show_level'=>$v['show_level'],
                'language'=>$v['language'],
                'solutions'=>isset($v['solutions'])?json_encode($v['solutions']):'',
                'create_time'=>date('Y-m-d H:i:s',time()),
            ];
        }
        Db::name('murphysec')->insertAll($data);
        //var_dump($list);
        exit;*/
        $pageSize = 20;
        $where = [];
        $search = $request->param('search');
        if (!empty($search)) {
            $where[] = ['name|version|source|authors','like',"%{$search}%"];
        }
        $map = [];
        if ($this->auth_group_id != 5 && !in_array($this->userId, config('app.ADMINISTRATOR'))) {
            $where[] = ['user_id', '=', $this->userId];
            $map[] = ['user_id', '=', $this->userId];
        }
        $code_id = $request->param('code_id');
        if ($code_id) {
            $where[] = ['code_id','=',$code_id];
        }
        $list = Db::table('murphysec')->where($where)->order("id", 'desc')->paginate([
            'list_rows'=> $pageSize,//每页数量
            'query' => $request->param(),
        ]);
        $data['list'] = $list->items();
        foreach ($data['list'] as &$v) {
            $v['code_name'] = Db::table('code')->where('id',$v['code_id'])->value('name');
            if ($v['repair_status'] == 1) {
                $v['repair_status'] = '未修复';
            } else {
                $v['repair_status'] = '已修复';
            }
        }
        $data['page'] = $list->render();
        $data['show_level'] = $this->show_level;
        //查询项目数据
        $data['projectList'] = $this->getMyCodeList();
        return View::fetch('index', $data);
    }

    public function batch_repair(Request $request){
        $ids = $request->param('ids');
        $this->addUserLog('murphysec',"批量修复数据[$ids]");
        if (!$ids) {
            return $this->apiReturn(0,[],'请先选择要修复的数据');
        }
        $map[] = ['id','in',$ids];
        if ($this->auth_group_id != 5 && !in_array($this->userId, config('app.ADMINISTRATOR'))) {
            $map[] = ['user_id', '=', $this->userId];
        }
        if (Db::name('murphysec')->where($map)->update(['repair_status'=>2])) {
            return $this->apiReturn(1,[],'批量修改成功');
        } else {
            return $this->apiReturn(0,[],'批量修改失败');
        }
    }

    public function del(Request $request)
    {
        $id = $request->param('id');
        $where[] = ['id','=',$id];
        if ($this->auth_group_id != 5 && !in_array($this->userId, config('app.ADMINISTRATOR'))) {
            $where[] = ['user_id', '=', $this->userId];
        }
        if (Db::name('murphysec')->where($where)->delete()) {
            Db::name('murphysec_vuln')->where('murphysec_id',$id)->delete();
            return redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->error('删除失败');
        }
    }

    // 批量删除
    public function batch_del(Request $request){
        return $this->batch_del_that($request,'murphysec');
    }
}
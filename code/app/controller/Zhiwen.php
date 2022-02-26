<?php


namespace app\controller;


use think\facade\Db;
use think\facade\View;
use think\Request;

class Zhiwen extends Common
{
    public function index(Request $request){
        $pageSize = 20;
        $where = [];
        /*if ($this->auth_group_id != 5 && !in_array($this->userId, config('app.ADMINISTRATOR'))) {
            $where[] = ['user_id', '=', $this->userId];
        }*/
        $search = $request->param('search');
        if (!empty($search)) {
            $where[] = ['filters|keyword|supplier|tags|title','like',"%{$search}%"];
        }
        $list = Db::table('zhiwen')->where($where)->paginate([
            'list_rows' => $pageSize,
            'query' => request()->param()
        ]);
        $data['list'] = $list->items();
        $data['page'] = $list->render();
        return View::fetch('index', $data);
    }

    // 批量删除
    public function batch_del(Request $request){
        $ids = $request->param('ids');
        if (!$ids) {
            return $this->apiReturn(0,[],'请先选择要删除的数据');
        }
        $map[] = ['id','in',$ids];
        if ($this->auth_group_id != 5 && !in_array($this->userId, config('app.ADMINISTRATOR'))) {
            $map[] = ['user_id', '=', $this->userId];
        }
        if (Db::name('zhiwen')->where($map)->delete()) {
            return $this->apiReturn(1,[],'批量删除成功');
        } else {
            return $this->apiReturn(0,[],'批量删除失败');
        }
    }
}
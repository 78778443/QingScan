<?php


namespace app\controller;


use think\facade\Db;
use think\facade\View;
use think\Request;

class GithubNotice extends Common
{
    public function index(Request $request)
    {
        $pageSize = 20;
        $where = [];
        $search = $request->param('search');
        if (!empty($search)) {
            $where[] = ['title', 'like', "%{$search}%"];
        }
        $list = Db::table('github_notice')->where($where)->order("id", 'desc')->paginate([
            'list_rows' => $pageSize,
            'query' => $request->param()
        ]);
        $data['list'] = $list->items();
        $data['page'] = $list->render();
        return View::fetch('index', $data);
    }

    public function del(Request $request)
    {
        $id = $request->param('id');
        $where[] = ['id', '=', $id];
        if ($this->auth_group_id != 5 && !in_array($this->userId, config('app.ADMINISTRATOR'))) {
            $where[] = ['user_id', '=', $this->userId];
        }
        if (Db::name('github_notice')->where('id', $id)->delete()) {
            return redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->error('删除失败');
        }
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
        if (Db::name('github_notice')->where($map)->delete()) {
            return $this->apiReturn(1,[],'批量删除成功');
        } else {
            return $this->apiReturn(0,[],'批量删除失败');
        }
    }
}
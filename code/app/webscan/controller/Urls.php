<?php

namespace app\webscan\controller;

use app\controller\Common;
use app\model\UrlsModel;
use think\facade\Db;
use think\facade\View;
use think\Request;

class Urls extends Common
{

    public function index(Request $request)
    {
        $pageSize = 15;
        $search = $request->param('search');
        $where = [];
        if (!empty($search)) {
            $where[] = ['url', 'like', "%{$search}%"];
        }

        $list = Db::table('urls')->where($where)->order("id", 'desc')->paginate([
            'list_rows' => $pageSize,
            'query' => $request->param(),
        ]);
        $data['list'] = $list->items();
        $data['page'] = $list->render();
        $data['projectList'] = $this->getMyAppList();

        return redirect('/web/asm/url');
    }

    public function _add(Request $request)
    {
        $data = $request->post();
        if ($this->auth_group_id != 5 && !in_array($this->userId, config('app.ADMINISTRATOR'))) {
            $data['user_id'] = $this->userId;
        }
        UrlsModel::addData($data);
        $this->success('添加成功');
    }

    public function del(Request $request)
    {
        $id = $request->param('id', '', 'intval');
        if (!$id) {
            $this->error('参数不能为空');
        }
        $map[] = ['id', '=', $id];

        if (Db::name('urls')->where($map)->update(['is_delete' => 1])) {
            return redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->error('删除失败');
        }
    }

    public function batch_del(Request $request)
    {
        $ids = $request->param('ids');
        if (!$ids) {
            return $this->apiReturn(0, [], '请先选择要删除的数据');
        }
        $map[] = ['id', 'in', $ids];

        if (Db::name('urls')->where($map)->update(['is_delete' => 1])) {
            return $this->apiReturn(1, [], '批量删除成功');
        } else {
            return $this->apiReturn(0, [], '批量删除失败');
        }
    }
}

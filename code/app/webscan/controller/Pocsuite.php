<?php

namespace app\webscan\controller;

use app\controller\Common;
use think\facade\Db;
use think\facade\View;
use think\Request;

class Pocsuite extends Common
{
    public function index(Request $request)
    {
        $pageSize = 20;
        $where = [];
        $search = $request->param('search');
        if (!empty($search)) {
            $where[] = ['name|url', 'like', "%{$search}%"];
        }
        $app_id = $request->param('app_id');
        if (!empty($app_id)) {
            $where[] = ['app_id', '=', $app_id];
        }

        $list = Db::table('pocsuite3')->where($where)->order("id", 'desc')->paginate([
            'list_rows' => $pageSize,
            'query' => $request->param()
        ]);
        $data['list'] = $list->items();
        foreach ($data['list'] as &$v) {
            $v['app_name'] = isset($v['app_id']) ? Db::name('app')->where('id', $v['app_id'])->value('name') : '';
        }
        $data['page'] = $list->render();
        //查询项目数据
        $data['projectList'] = $this->getMyAppList();
        return redirect('/web/result/plugin');
    }

    /**
     * @return int
     * @Route("Index/app")
     */
    public function app()
    {
        $list = Db::table('pocsuite3')->select()->toArray();
        var_dump($list);
    }

    // 删除单条记录
    public function del(Request $request)
    {
        $id = $request->param('id');
        if (Db::name('pocsuite3')->where('id', $id)->delete()) {
            return redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->error('删除失败');
        }
    }

    // 批量删除
    public function batch_del(Request $request)
    {
        return $this->batch_del_that($request, 'pocsuite3');
    }
}
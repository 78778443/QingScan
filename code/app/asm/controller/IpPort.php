<?php

namespace app\asm\controller;

use app\controller\Common;
use think\facade\Db;
use think\facade\View;
use think\Request;


class IpPort extends Common
{

    public function index(Request $request)
    {
        if (function_exists('startGetDomain')) startGetDomain();

        $pageSize = 20;
        $where = [];
        if ($request->param('domain')) $where[] = ['domain', '=', $request->param('domain')];
        if (!empty($request->param('app_id'))) $where[] = ['app_id', '=', $request->param('app_id')];

        $list = Db::table('asm_ip_port')->where($where)->order("id", 'desc')->paginate([
            'list_rows' => $pageSize,//每页数量
            'query' => $request->param(),
        ]);
        $data['list'] = $list->items();
        $data['page'] = $list->render();
        $data['paginator'] = $list;
        return redirect('/web/');
    }

    /**
     * 获取IP端口详情（API接口）
     */
    public function detail(Request $request)
    {
        $id = $request->param('id', 0, 'intval');
        if (empty($id)) {
            return json(['code' => 0, 'msg' => '参数错误']);
        }

        $info = Db::table('asm_ip_port')->find($id);
        if (empty($info)) {
            return json(['code' => 0, 'msg' => '数据不存在']);
        }

        return json(['code' => 1, 'data' => $info]);
    }

}

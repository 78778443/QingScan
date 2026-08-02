<?php

namespace app\asm\controller;

use app\controller\Common;
use app\model\UrlsModel;
use app\Request;
use think\facade\Db;
use think\facade\View;


class Host extends Common
{

    public function index(Request $request)
    {
        $pageSize = 20;
        $where[] = ['is_delete','=',0];
        $domain = $request->param('domain');
        if ($domain) {
            $where[] = ['domain','=',$domain];
        }

        $app_id = $request->param('app_id');
        if (!empty($app_id)) {
            $where[] = ['app_id','=',$app_id];
        }
        $list = Db::table('asm_host')->where($where)->order("id", 'desc')->paginate([
            'list_rows'=> $pageSize,//每页数量
            'query' => $request->param(),
        ]);
        $data['list'] = $list->items();
        $data['page'] = $list->render();
        $data['paginator'] = $list;
        $data['projectList'] = $this->getMyAppList();
        return redirect('/web/asm/host');
    }

    public function add()
    {
        $this->show('host/add');
    }

    public function _add()
    {
        if ($this->auth_group_id != 5 && !in_array($this->userId,config('app.ADMINISTRATOR'))) {
            $_POST['user_id'] = $this->userId;
        }
        UrlsModel::addData($_POST);

        $this->Location("index.php?s=host/index");
    }

    public function add_api_url()
    {
        $this->show('host/add_api_url');
    }

    public function _add_api_url()
    {
        if ($this->auth_group_id != 5 && !in_array($this->userId,config('app.ADMINISTRATOR'))) {
            $_POST['user_id'] = $this->userId;
        }
        UrlsModel::addData($_POST);
    }

    /**
     * 获取主机详情（API接口）
     */
    public function detail(Request $request)
    {
        $id = $request->param('id', 0, 'intval');
        if (empty($id)) {
            return json(['code' => 0, 'msg' => '参数错误']);
        }

        $info = Db::table('asm_host')->find($id);
        if (empty($info)) {
            return json(['code' => 0, 'msg' => '数据不存在']);
        }

        return json(['code' => 1, 'data' => $info]);
    }

    /**
     * 删除主机
     */
    public function delete(Request $request)
    {
        $id = $request->param('id', 0, 'intval');
        if (empty($id)) {
            return json(['code' => 0, 'msg' => '参数错误']);
        }

        $result = Db::table('asm_host')->where('id', $id)->delete();
        if ($result) {
            return json(['code' => 1, 'msg' => '删除成功']);
        }
        return json(['code' => 0, 'msg' => '删除失败']);
    }
}

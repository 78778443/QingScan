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
        $data['projectList'] = $this->getMyAppList();
        return View::fetch('index', $data);
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
}

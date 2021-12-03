<?php

namespace app\controller;

use app\BaseController;
use app\model\AppModel;
use app\model\HostModel;
use app\model\UrlsModel;
use think\facade\Db;
use think\facade\View;


class Host extends Common
{

    public function index()
    {
        $pageSize = 20;
        $where[] = ['is_delete','=',0];
        $domain = getParam('domain');
        if ($domain) {
            $where[] = ['domain','=',$domain];
        }
        $list = Db::table('host')->where($where)->order("id", 'desc')->paginate([
            'list_rows'=> $pageSize,//每页数量
            'query' => request()->param(),
        ]);
        $data['list'] = $list->toArray()['data'];
        foreach ($data['list'] as &$v) {
        }
        $data['page'] = $list->render();

        $data['appArr'] = AppModel::getAppName();

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

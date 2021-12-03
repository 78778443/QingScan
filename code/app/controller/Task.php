<?php

namespace app\controller;

use app\BaseController;
use app\model\AppModel;
use app\model\UrlsModel;
use app\model\TaskModel;
use app\model\XrayModel;

class Task extends Common
{

    public $statusArr = ["队列中", "已完成", "已失败"];

    public function scan_list()
    {
        $page = getParam('page', 1);
        $data = UrlsModel::getListByWherePage([], $page);
        $data['statusArr'] = $this->statusArr;
        $data['appArr'] = AppModel::getAppName();

        $this->show('task/scan_list', $data);
    }


    public function bug_list()
    {
        $page = getParam('page', 1);
        $taskId = $_GET['task_id'] ?? 0;
        $where = [];

        if ($taskId) {
            $where['app_id'] = $taskId;
        }
        $data = XrayModel::getListByWherePage($where, $page);

        $data['statusArr'] = $this->statusArr;
        $data['appArr'] = AppModel::getAppName();
        $this->show('task/bug_list', $data);
    }

    public function add_task()
    {
        $this->show('task/add');
    }

    public function _add_task()
    {

        addlog(['接受到添加扫描任务请求', $_POST]);
        $isCrawl = isset($_GET['is_crawl']) ? intval($_GET['is_crawl']) : 0;

        TaskModel::addTask($_POST);

        $this->Location("/index.php?s=task/scan_list");

    }


    public static function checkCodeUpadte()
    {
        //获取上次更新时间
        $updateFile = "/tmp/qingscan.txt";
        $oldTime = file_get_contents($updateFile);

        //如果上次更新时间已经有100秒没有更新，这次就需要更新了
        if ($oldTime + 100 < time()) {
            $cmd = "git pull";
            system($cmd);
            file_put_contents($updateFile, time());
        }
    }

    public function _start_scan()
    {

        addlog(['开始进行扫描', $_POST]);

        //接收参数
        $urlId = getParam('url_id');

        //查询URL地址
        $urlInfo = UrlsModel::getInfo($urlId);

        //插入到xray队列
        XrayModel::sendTask($urlId, $urlInfo['url']);
        ajaxReturn("扫描任务发送成功");
    }

    public function add_api_url()
    {
        $this->show('task/add_api_url');
    }

    public function _add_api_url()
    {
        $data = $_POST;
        $data['method'] = 'POST';
        $data['type'] = 1;
        if ($this->auth_group_id != 5 && !in_array($this->userId,config('app.ADMINISTRATOR'))) {
            $data['user_id'] = $this->userId;
        }
        UrlsModel::addData($data);
    }

}

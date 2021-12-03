<?php

namespace app\controller;

use app\BaseController;
use app\model\AppModel;
use app\model\xrayModel;
use app\model\TaskModel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use think\facade\Db;
use think\facade\View;

class Xray extends Common
{
    public function index()
    {
        $pageSize = 10;
        $where[] = ['is_delete','=',0];
        $search = getParam('search');
        $pid = getParam('project_id');
        $level = getParam('level'); // 等级
        $Category = getParam('Category');   // 分类
        $filename = getParam('filename');   // 文件名
        $check_status = getParam('check_status');   // 审核状态
        if (!empty($pid)) {
            $where[] = ['app_id','=',$pid];
        }
        if (!empty($level) && $level != -1) {
            $where[] = ['hazard_level','=',$level];
        }
        if (!empty($Category)) {
            //$where[] = ['plugin','like',"%{$Category}%"];
            $where[] = ['plugin','=',json_encode($Category)];
        }
        if (!empty($filename)) {
            $where[] = ['url_source','=',$filename];
        }
        if ($check_status !== null && in_array($check_status,[0,1,2])) {
            $where[] = ['check_status','=',$check_status];
        }
        if (!empty($search)) {
            $where[] = ['app_id','like',"%{$search}%"];
        }
        $map = [];
        if ($this->auth_group_id != 5 && !in_array($this->userId,config('app.ADMINISTRATOR'))) {
            $where[] = ['user_id','=',$this->userId];
            $map[] = ['user_id','=',$this->userId];
        }
        $list = Db::table('xray')->where($where)->order("id", 'desc')->paginate([
            'list_rows'=> $pageSize,//每页数量
            'query' => request()->param(),
        ]);
        $data['list'] = $list->toArray()['data'];
        foreach ($data['list'] as &$v) {
            $v['plugin'] = json_decode($v['plugin'],true);
        }
        $data['page'] = $list->render();

        $data['appArr'] = AppModel::getAppName();

        $projectArr = Db::table('app')->where($map)->select()->toArray();
        $projectArr = array_column($projectArr, null, 'id');
        $data['projectArr'] = $projectArr;
        $data['CategoryList'] = Db::table('xray')->where($where)->group('plugin')->column('plugin');
        foreach ($data['CategoryList'] as &$v) {
            $v = json_decode($v,true);
        }
        $data['projectList'] = Db::table('xray')->where($where)->where('app_id','<>',0)->group('app_id')->column('app_id');
        $data['fileList'] = Db::table('xray')->where($where)->group('url_source')->column('url_source');
        $data['check_status_list'] = ['未审计','有效漏洞','无效漏洞'];

        return View::fetch('index', $data);
    }


    public function details(){
        $id = getParam('id');
        if (!$id) {
            $this->error('参数错误');
        }
        $where[] = ['id','=',$id];
        if ($this->auth_group_id != 5 && !in_array($this->userId,config('app.ADMINISTRATOR'))) {
            $where[] = ['user_id','=',$this->userId];
        }
        $info = Db::table('xray')->where($where)->find();
        if (!$info) {
            $this->error('数据不存在');
        }
        $info['detail'] = json_decode($info['detail'], true);

        $upper_id = Db::name('xray')->where('id','<',$id)->order('id','desc')->value('id');
        $info['upper_id'] = $upper_id?:$id;
        $lower_id = Db::name('xray')->where('id','>',$id)->order('id','asc')->value('id');
        $info['lower_id'] = $lower_id?:$id;

        $data['info'] = $info;
        /*var_dump($info['detail']);
        echo '<br/>';
        echo '<br/>';
        echo '<br/>';
        var_dump($info['detail']['snapshot'][0][1]);exit;*/
        return View::fetch('details', $data);
    }


    public function getResult()
    {
        $rabbitConf = getRabbitMq();
        $connection = new AMQPStreamConnection($rabbitConf['host'], $rabbitConf['port'], $rabbitConf['user'], $rabbitConf['password'], $rabbitConf['vhost']);
        $channel = $connection->channel();

        $queueName = "xray_result";
        $channel->queue_declare($queueName, false, false, false, false);

        echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";
        addlog("开始处理Xray扫描结果");
        $callback = function ($msg) {
            $this->addXrayResult($msg);
            //ACK确认
            $msg->ack();
            //                $msg->nack();
        };

        $channel->basic_consume($queueName, '', false, false, false, false, $callback);

        while (count($channel->callbacks)) {
            $channel->wait();
        }
    }

    public function addXrayResult(object $msg)
    {
        $execRet = json_decode($msg->body, true);
        addlog(['Xray任务扫描完成', " {$execRet['id']}"]);
        $scanStatus = 1;

        $data = json_decode(base64_decode($execRet['tool_result']), true);
        if (is_array($data) == false) {
            addlogRaw(["漏洞扫描结果非数组:", base64_decode($execRet['cmd_result']), base64_decode($execRet['tool_result'])]);
            $scanStatus = 2;
            //更新漏扫描扫描状态
            xrayModel::updateScanStatus($execRet['id'], $scanStatus, $execRet['cmd_result']);
            return false;
        }

        xrayModel::updateScanStatus($execRet['id'], $scanStatus, $execRet['cmd_result']);
        $urlInfo = xrayModel::getInfo($execRet['id']);
        foreach ($data as $value) {
            $newData = [
                'create_time' => substr($value['create_time'], 0, 10),
                'detail' => json_encode($value['detail']),
                'plugin' => json_encode($value['plugin']),
                'target' => json_encode($value['target']),
                'url' => $value['detail']['addr'],
                'url_id' => $execRet['id'],
                'app_id' => $urlInfo['app_id'],
                'poc' => $value['detail']['payload']
            ];
            echo "添加漏洞结果:" . json_encode($newData) . PHP_EOL;
            XrayModel::addXray($newData);
        }
    }


    public function test()
    {
        $str = "tmp/www.ampak.com.tw/product.php/bugRet.json";
        $data = file_get_contents($str);
        $data = json_decode($data, true);
        foreach ($data as $value) {
            $newData = [
                'create_time' => substr($value['create_time'], 0, 10),
                'detail' => json_encode($value['detail']),
                'plugin' => json_encode($value['plugin']),
                'target' => json_encode($value['target']),
                'url' => $value['target']['url'],
                'poc' => $value['detail']['payload']
            ];

            TaskModel::addTask($newData);
        }


    }

    public function del()
    {
        $id = getParam('id');
        $map[] = ['id','=',$id];
        if ($this->auth_group_id != 5 && !in_array($this->userId,config('app.ADMINISTRATOR'))) {
            $map[] = ['user_id','=',$this->userId];
        }
        if (Db::name('xray')->where('id',$id)->update(['is_delete'=>1])) {
            return redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->error('删除失败');
        }
    }

}

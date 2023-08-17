<?php

namespace app\webscan\controller;

use app\controller\Common;
use app\model\TaskModel;
use app\webscan\model\xrayModel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use think\facade\Db;
use think\facade\View;
use think\Request;

class Xray extends Common
{
    public function index(Request $request)
    {
        $dengjiArr = ['Low', 'Medium', 'High', 'Critical'];

        $pageSize = 15;
        $where[] = ['is_delete','=',0];
        $search = $request->param('search');
        $app_id = $request->param('app_id');
        $level = $request->param('level'); // 等级
        $Category = $request->param('Category');   // 分类
        $filename = $request->param('filename');   // 文件名
        $check_status = $request->param('check_status');   // 审核状态
        if (!empty($app_id)) {
            $where[] = ['app_id','=',$app_id];
        }
        if (!empty($level) && $level != -1) {
            $level = array_keys($dengjiArr,$level)[0];
            $where[] = ['hazard_level','=',$level];
        }
        if (!empty($Category)) {
            $where[] = ['plugin','=',json_encode($Category)];
        }
        if (!empty($filename)) {
            $where[] = ['url_source','=',$filename];
        }
        if ($check_status !== null && in_array($check_status,[0,1,2])) {
            $where[] = ['check_status','=',$check_status];
        }
        $map = [];
        if ($this->auth_group_id != 5 && !in_array($this->userId,config('app.ADMINISTRATOR'))) {
            $where[] = ['user_id','=',$this->userId];
            $map[] = ['user_id','=',$this->userId];
        }
        $list = Db::table('xray')->where($where)->order("id", 'desc')->paginate([
            'list_rows'=> $pageSize,//每页数量
            'query' => $request->param(),
        ]);
        $data['list'] = $list->toArray()['data'];
        foreach ($data['list'] as &$v) {
            $v['plugin'] = json_decode($v['plugin'],true);
            $v['app_name'] = Db::name('app')->where('id',$v['app_id'])->value('name');
        }
        $data['page'] = $list->render();

        $data['CategoryList'] = Db::table('xray')->where($where)->group('plugin')->column('plugin');
        foreach ($data['CategoryList'] as &$v) {
            $v = json_decode($v,true);
        }
        $data['projectList'] = $this->getMyAppList();
        $data['check_status_list'] = ['未审计','有效漏洞','无效漏洞'];
        return View::fetch('index', $data);
    }


    public function details(Request $request){
        $id = $request->param('id');
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
        //var_dump($info['detail']);exit;

        $info['detail'] = json_decode($info['detail'], true);

        $upper_id = Db::name('xray')->where('id','<',$id)->order('id','desc')->value('id');
        $info['upper_id'] = $upper_id?:$id;
        $lower_id = Db::name('xray')->where('id','>',$id)->order('id','asc')->value('id');
        $info['lower_id'] = $lower_id?:$id;

        $data['info'] = $info;
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

    public function del(Request $request)
    {
        $id = $request->param('id');
        if (!$id) {
            $this->error('参数不能为空');
        }
        $map[] = ['id','=',$id];
        if ($this->auth_group_id != 5 && !in_array($this->userId,config('app.ADMINISTRATOR'))) {
            $map[] = ['user_id','=',$this->userId];
        }
        if (Db::name('xray')->where('id',$id)->delete()) {
            return redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->error('删除失败');
        }
    }

    // 批量删除
    public function batch_del(Request $request){
        return $this->batch_del_that($request,'xray');
    }
}

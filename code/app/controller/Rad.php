<?php

namespace app\controller;

use app\model\AppModel;
use app\model\UrlsModel;
use app\model\TaskModel;
use app\model\XrayModel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class Rad extends Common
{


    public function getResult()
    {
        $rabbitConf = getRabbitMq();
        $connection = new AMQPStreamConnection($rabbitConf['host'], $rabbitConf['port'], $rabbitConf['user'], $rabbitConf['password'], $rabbitConf['vhost']);
        $channel = $connection->channel();

        $queueName = "rad_result";
        $channel->queue_declare($queueName, false, false, false, false);

        echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";
        $callback = function ($msg) {
            $execRet = json_decode($msg->body, true);

            addlog(['收到爬虫结果', $execRet]);

            $value = json_decode(base64_decode($execRet['tool_result']), true);

            $newData = [
                'app_id' => $execRet['id'],
                'method' => $value['Method'],
                'url' => $value['URL'],
                'status' => 1,
                'crawl_status' => 1,
                'scan_status' => 0,
                'header' => json_encode($value['Header']),
            ];
            if ($this->auth_group_id != 5 && !in_array($this->userId,config('app.ADMINISTRATOR'))) {
                $newData['user_id'] = $this->userId;
            }
            UrlsModel::addData($newData);
            UrlsModel::updateCrawlStatus($execRet['id'], 1);

            //ACK确认
            $msg->ack(true);
            //                $msg->nack();
        };

        $channel->basic_consume($queueName, '', false, false, false, false, $callback);

        while (count($channel->callbacks)) {
            $channel->wait();
        }
    }




}

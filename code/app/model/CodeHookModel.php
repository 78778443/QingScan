<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/8/15
 * Time: 上午10:54
 */


namespace app\model;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use QingPHP\Lib\MysqlLib;
use think\facade\Db;

class CodeHookModel extends BaseModel
{

    public static $tableName = 'code_hook';


    /**
     * @param  int    $id
     * @param  string $url
     * @param  string $callUrl
     * @throws Exception
     */
    public static function sendTask(int $id, string $url)
    {
        $rabbitConf = getRabbitMq();
        $connection = new AMQPStreamConnection($rabbitConf['host'], $rabbitConf['port'], $rabbitConf['user'], $rabbitConf['password'], $rabbitConf['vhost']);
        $channel = $connection->channel();

        $queueName = "rad";
        $channel->queue_declare($queueName, false, false, false, false);

        //发送任务到节点
        $data = [
            'id' => $id,
            'url' => $url
        ];

        $sendData = json_encode($data);

        $msg = new AMQPMessage($sendData);
        $result = $channel->basic_publish($msg, '', $queueName);

        addlog(['发送爬虫任务', $id, $url, $data]);


        $channel->close();
        $connection->close();

    }

    /**
     * @param  $where
     * @param  int   $limit
     * @param  array $otherParam
     * @return mixed
     */
    private static function getList($where, int $limit = 15, int $page = 1, array $otherParam = [])
    {
        $result = Db::table(self::$tableName)->where($where)->whereRaw("author !='' ")->order('id', 'desc')->page($page)->select()->toArray();

        return $result;
    }

    /**
     * 获取单条记录
     *
     * @param  int $id
     * @return array
     */
    public static function getInfo(int $id)
    {
        $where = ['id' => $id];

        $list = self::getList($where);

        return $list[0] ?? [];
    }

    /**
     * @param array $data
     */
    public static function addData(array $data)
    {
        return self::add($data);
    }

    private static function add($data)
    {
        addlog(["保存URL地址", $data]);
        Db::table(self::$tableName)->insert($data);
    }
}

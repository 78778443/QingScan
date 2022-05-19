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
use think\facade\Db;

class XrayModel extends BaseModel
{

    public static $tableName = "xray";


    public static function temp()
    {
        $data = file_get_contents("/mnt/d/MyCode/work/auto_scan_bug/tools/xray/xray-crawler-testphp.json");
        $data = json_decode($data, true);
        foreach ($data[0] as &$value){
            $value = json_encode($value);
        }
        Db::table('xray')->insert($data[0]);
    }

    /**
     * @param  $where
     * @param  int   $limit
     * @param  array $otherParam
     * @return mixed
     */
    private static function getList($where, int $limit = 15, int $page = 1, array $otherParam = [])
    {
        $result = Db::table(self::$tableName)->where($where)->select()->toArray();
        foreach ($result as &$value) {
            $value['detail'] = json_decode($value['detail'], true);
            $value['plugin'] = json_decode($value['plugin'], true);
            $value['target'] = json_decode($value['target'], true);
        }
        return $result;
    }

    public static function getListByWhere($where, $limit = 20)
    {
        $list = self::getList($where, $limit);
        return $list;
    }

    public static function getCount($where, array $otherParam = [])
    {
        $group = $otherParam['group'] ?? '';
        $db = Db::table(self::$tableName);
        if ($group) {
            $db->group($group);
        }
        $result = $db->where($where)->count();
        return $result[0]['num'] ?? 0;
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
    public static function addXray(array $data)
    {
        self::add($data);
    }

    private static function add($data)
    {
        Db::table('xray')->extra('IGNORE')->insert($data);
    }

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

        $queueName = "xray";
        $channel->queue_declare($queueName, false, false, false, false);

        //发送任务到节点
        $data = [
            'id' => $id,
            'url' => $url
        ];

        $sendData = json_encode($data);

        $msg = new AMQPMessage($sendData);
        $data = $channel->basic_publish($msg, '', $queueName);

        addlog(['发送扫描任务', $sendData]);

        $channel->close();
        $connection->close();

    }
}

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
use wamkj\thinkphp\Backup;

class ConfigModel extends BaseModel
{

    public static function field()
    {
        return db()->getFields(self::$tableName);
    }

    public static $tableName = "config";


    /**
     * @param int $id
     * @param string $url
     * @param string $callUrl
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
     * @param string $limit
     * @param array $otherParam
     * @return mixed
     */
    private static function getList($where, $limit = '', array $otherParam = [])
    {
        $field = $otherParam['field'] ?? '*';
        $group = $otherParam['group'] ?? '';
        $order = $otherParam['order'] ?? 'id desc';

        $db = Db::table(self::$tableName);

        if (!empty($limit)) {
            $db->limit($limit);
        }

        if ($group) {
            $db->group($group);
        }

        $result = $db->where($where)->order($order)->select($field);


        return $result;
    }

    public static function getListByWhere($where)
    {

        $list = self::getList($where, 20);

        return $list;
    }

    /**
     * 获取单条记录
     *
     * @param int $id
     * @return array
     */
    public static function getInfo(int $id)
    {
        $where = ['id' => $id];

        $list = self::getList($where);

        return $list[0] ?? [];
    }

    /**
     * 内部方法，更新数据
     *
     * @param array $where
     * @param array $data
     * @return mixed
     */
    private static function updateByWhere(array $where, array $data)
    {
        $crawlerApi = new MysqlLib();

        //更新条件
        $crawlerApi = $crawlerApi->table(self::$tableName)->where($where);

        //执行更新并返回数据
        $crawlerApi->update($data);
    }

    /**
     * 更新生成任务状态
     *
     * @param string $crawlerNum
     * @param int $status
     */
    public static function updateStatus(int $id, int $status)
    {
        $where = ['id' => $id];
        $data = ['status' => $status];
        self::updateByWhere($where, $data);
    }


    public static function updateConfig(int $id, array $data)
    {
        $where = ['id' => $id];
        $data = ['data' => json_encode($data)];
        self::updateByWhere($where, $data);
    }


    /**
     * @param array $data
     */
    public static function addData(array $data)
    {

        $data = getArrayField($data, self::field());

        return self::add($data);
    }

    private static function add($data)
    {

        Db::table(self::$tableName)->insert($data);
    }

    public static function getNameArr()
    {
        $radArr = Db::table(self::$tableName)->where(['type' => 1])->select()->toArray();
        $data['radArr'] = array_column($radArr, 'name', 'id');
        $xrayArr = Db::table(self::$tableName)->where(['type' => 2])->select()->toArray();
        $data['xrayArr'] = array_column($xrayArr, 'name', 'id');
        return $data;
    }

    // 备份数据库
    public static function backup()
    {
            $backup = config('app.backup');
            if (!file_exists($backup['path'])) {
                mkdir($backup['path'], 0777, true);
                addlog("文件不存在:{$backup['path']}");
            }
            $filename = $backup['path'] . 'runtime.lock';
            $status = true;
            if (file_exists($filename)) {
                $content = file_get_contents($filename);
                if ($content + 3600 * 24 - 100 > time()) {
                    $status = false;
                    sleep(60);
                } else {
                    file_put_contents($filename, time());
                }
            } else {
                file_put_contents($filename, time());
            }
            if ($status) {

                $db = new Backup($backup);
                $time = time();
                $file = ['name' => date('Ymd-His', $time), 'part' => 1];
                foreach ($db->dataList() as $k => $v) {
                    $db->setFile($file)->backup($v['name'], 0);
                }
            }
        addlog("数据库备份完成");

    }

    public static function value($key){
        return Db::name('system_config')->where('key',$key)->value('value');
    }

    public static function set_value($key,$val){
        return Db::name('system_config')->where('key',$key)->update(['value'=>$val]);
    }
}

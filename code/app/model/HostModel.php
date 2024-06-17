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

class HostModel extends BaseModel
{

    public static $tableName = 'asm_host';


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
     * 获取APP的URL地址
     *
     * @param int $appId
     * @return mixed
     */
    public static function getCrawlerList(int $appId)
    {

        //查询具体数据,并刷新缓存
        $result = self::getList(['app_id' => $appId]);


        return $result;

    }

    /**
     * @param  $where
     * @param int $limit
     * @param array $otherParam
     * @return mixed
     */
    private static function getList($where, int $limit = 15, int $page = 1, array $otherParam = [])
    {

        $result = Db::table(self::$tableName)->where($where)->page($page)->order('id', 'desc')->select()->toArray();

        return $result;
    }

    private static function getCount($where, array $otherParam = [])
    {
        $db = new MysqlLib(getMysql());
        $group = $otherParam['group'] ?? '';

        $db = $db->table(self::$tableName);

        if ($group) {
            $db->group($group);
        }

        $result = $db->where($where)->count();


        return $result[0]['num'] ?? 0;
    }


    public static function getListByWherePage($where, $page, $pageSize = 15)
    {

        $list = self::getList($where, $pageSize, $page);

        $count = self::getCount($where);

        return ['list' => $list, 'count' => $count, 'pageSize' => $pageSize];
    }

    public static function getListByWhere($where)
    {

        $list = self::getList($where);

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


    public static function updateScanStatus(int $id, int $status, string $cmdResult)
    {
        $where = ['id' => $id];
        $data = ['scan_status' => $status, 'cmd_result' => $cmdResult];
        self::updateByWhere($where, $data);
    }

    public static function updateCrawlStatus(int $id, int $status)
    {
        $where = ['id' => $id];
        $data = ['crawl_status' => $status];
        self::updateByWhere($where, $data);
    }

    /**
     * @param array $data
     */
    public static function addData(array $data, $metod = 'get')
    {
        $data['method'] = $metod;


        return self::add($data);
    }

    private static function add($data)
    {

        addlog(["保存URL地址", $data]);

        Db::table(self::$tableName)->extra("IGNORE")->insert($data);
    }

    public static function autoAddHost()
    {
        $appList = Db::table('app')->where(['status' => 1])->limit(100)->order('id', 'desc')->select()->toArray();
        foreach ($appList as $app) {

            PluginModel::addScanLog($app['id'], __METHOD__, 0);
            $domain = parse_url($app['url'])['host'];
            $host = gethostbyname($domain);
            if (!filter_var($host, FILTER_VALIDATE_IP)) {
                addlog(["此域名{$domain}不能解析成IP"]);
                PluginModel::addScanLog($app['id'], __METHOD__, 1, 0, 1, ['content' => "此域名{$domain}不能解析成IP"]);
                continue;
            }

            $time = getCurrentMilis();
            $url = "https://site.ip138.com/domain/read.do?domain=www.eoffcn.com&time={$time}";
            $result = curl_get($url);
            $list = json_decode($result, true);
            if (isset($list['status']) && $list['status']) {
                foreach ($list['data'] as $v) {
                    $data = [
                        'app_id' => $app['id'],
                        'domain' => $domain,
                        'host' => $v['ip'],
                        'user_id' => $app['user_id']
                    ];
                    Db::table(self::$tableName)->extra("IGNORE")->insert($data);
                }
            } else {
                $data = [
                    'app_id' => $app['id'],
                    'domain' => $domain,
                    'host' => $host,
                    'user_id' => $app['user_id']
                ];
                Db::table(self::$tableName)->extra("IGNORE")->insert($data);
            }
            PluginModel::addScanLog($app['id'], __METHOD__, 1, 2);
        }
        addlog("自动添加主机记录完成");
    }
}

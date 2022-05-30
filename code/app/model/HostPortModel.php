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

class HostPortModel extends BaseModel
{

    public static $tableName = 'host_port';


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


    public static function getCrawlerInfo($crawlerId)
    {

        //查询具体数据,并刷新缓存
        $result = self::getList(['id' => $crawlerId]);


        return $result[0] ?? false;

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

        $order = ['id' => 'desc'];
        $result = Db::table(self::$tableName)->where($where)->page($page)->order($order)->select()->toArray();

        return $result;
    }

    private static function getCount($where, array $otherParam = [])
    {
        return 0;
        $db = new MysqlLib(getMysql());
        $group = $otherParam['group'] ?? '';

        $db = $db->table(self::$tableName);

        if ($group) {
            $db->group($group);
        }

        $result = $db->where($where)->count();


        return $result[0]['num'] ?? 0;
    }

    private static function getServiceList($where)
    {

        $list = Db::table(self::$tableName)
            ->field('service as name,count(service) as num')
            ->where($where)
            ->group('service')
            ->having("num > 50")
            ->select()
            ->toArray();
        array_multisort(array_column($list, 'num'), SORT_DESC, $list);

        $list = array_slice($list, 0, 10);
        return $list;
    }

    private static function getPort($where)
    {
        $list = Db::table(self::$tableName)
            ->field('port as name,count(port) as num')
            ->where($where)
            ->group('port')
            ->having("num > 50")
            ->select()
            ->toArray();
        array_multisort(array_column($list, 'num'), SORT_DESC, $list);

        $list = array_slice($list, 0, 10);
        return $list;
    }

    public static function getClassify($where)
    {

        $data = [
            ['服务', HostPortModel::getServiceList($where), "service"],
            ['端口排名', HostPortModel::getPort($where), "port"],
        ];


        return $data;
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

    public static function getHttpList()
    {
        $list = Db::table(self::$tableName)
            ->whereIn('service', "http,https")
            ->limit(100)
            ->order('id', 'desc')
            ->select()
            ->toArray();

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
    public static function updateByWhere(array $where, array $data)
    {
        $result = Db::name(self::$tableName)
            ->where($where)
            ->update($data);
        return $result;
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

        Db::table(self::$tableName)->insert($data);
    }


    public static function NmapPortScan()
    {
        while (true) {
            processSleep(1);
            $taskList = Db::table('host_port')->where(['service' => null])->orderRand()->limit(10)->select()->toArray();
            foreach ($taskList as $value) {
                if (!self::checkToolAuth(1,$value['app_id'],'nmap')) {
                    continue;
                }
                PluginModel::addScanLog($value['id'], __METHOD__, 1,1);
                $result = [];
                $cmd = "nmap -sS -Pn -T4  -p {$value['port']} {$value['host']} | grep open | grep -v Discovered |grep -v grep";
                echo $cmd . PHP_EOL;
                execLog($cmd, $result);

                foreach ($result as $item) {
                    $item = str_replace("  ", " ", $item);
                    $aaa = explode(" ", $item);
                    if (count($aaa) != 3) {
                        continue;
                    }

                    $data = ['service' => $aaa[2]];

                    $where = ['host' => $value['host'], 'port' => $value['port']];
                    Db::table('host_port')->where($where)->update($data);
                }
                PluginModel::addScanLog($value['id'], __METHOD__, 1,1);
            }
            sleep(10);
        }
    }

    // 端口扫描
    public static function scanHostPort()
    {
        $portStr = "21,22,23,25,53,80,81,110,111,123,135,137,139,161,389,443,445,465,500,515,520,523,548,623,636,873,902,1080,1099,1433,1521,1604,1645,1701,1883,1900,2049,2181,2375,2379,2425,3128,3306,3389,4730,5060,5222,5351,5353,5432,5555,5601,5672,5683,5900,5938,5984,6000,6379,7001,7077,8000,8001,8080,8081,8443,8545,8686,8888,9000,9001,9042,9092,9100,9200,9418,9999,11211,27017,37777,50000,50070,61616";
        while (true) {
            processSleep(1);
            $endTime = date('Y-m-d', time() - 86400 * 15);
            $hostLit = Db::table('host')->whereTime('port_scan_time', '<=', $endTime)->limit(5)->orderRand()->select()->toArray();
            foreach ($hostLit as $val) {
                if (!self::checkToolAuth(1,$val['app_id'],'masscan')) {
                    continue;
                }
                PluginModel::addScanLog($val['id'], __METHOD__, 1,0);
                self::scanTime('host', $val['id'], 'port_scan_time');
                $host = gethostbyname($val['host']);
                $cmd = "masscan --ports {$portStr} {$host}  --max-rate 2000 |grep Discovered";
                execLog($cmd, $result);
                foreach ($result as $value) {
                    $aaa = explode(" ", $value);
                    $typeArr = explode("/", $aaa[3]);
                    $data = ['host' => $aaa[5], 'type' => $typeArr[1], 'port' => $typeArr[0],'user_id'=>$val['user_id'],'app_id'=>$val['app_id']];
                    addlog(["发现主机开放端口", $data]);
                    Db::table('host_port')->extra("IGNORE")->insert($data);
                }
                PluginModel::addScanLog($val['id'], __METHOD__, 1,1);
            }
            sleep(10);
        }
    }

    // 更新ip区域以及运营商信息
    public static function upadteRegion()
    {
        while (true) {
            processSleep(1);
            $obj = Db::table('host')->whereTime('ip_scan_time', '<=', date('Y-m-d H:i:s', time() - (86400 * 15)));
            $list = $obj->where('is_delete', 0)->limit(10)->orderRand()->select()->toArray();
            foreach ($list as $v) {
                PluginModel::addScanLog($v['id'], __METHOD__, 1,0);
                $result = get_ip_lookup($v['host']);
                if (!isset($result['data'])) {
                    PluginModel::addScanLog($v['id'], __METHOD__, 1,2);
                    addlog(["未获取此IP{$v['host']}信息"]);
                    self::updateScanTime($v['id']);
                    continue;
                }

                $result = $result['data'];
                if ($result) {
                    $ids[] = $v['id'];

                    $v['country'] = isset($result['country']) ? $result['country'] : '';
                    $v['region'] = $result['region'];
                    $v['city'] = $result['city'];
                    $v['area'] = $result['area'];
                    $v['isp'] = $result['isp'];
                    $v['ip_scan_time'] = date('Y-m-d H:i:s', time());
                    Db::table('host')->save($v);
                }
                PluginModel::addScanLog($v['id'], __METHOD__, 1,1);
            }

            addlog(["更新IP信息完成，休息30秒..."]);
            sleep(30);
        }
    }

    public static function updateScanTime($id)
    {
        $data = ['ip_scan_time' => date('Y-m-d H:i:s', time())];
        Db::name('host')->where('id', $id)->update($data);
    }
}

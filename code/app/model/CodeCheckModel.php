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

class CodeCheckModel extends BaseModel
{

    public static $tableName = 'code_check';


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

        //$where['author'] = [''];
        $result = Db::table(self::$tableName)->where($where)->whereRaw("author !='' ")->order('id', 'desc')->page($page)->select()->toArray();

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
    public static function addData(array $data)
    {
        return self::add($data);
    }

    private static function add($data)
    {

        addlog(["保存URL地址", $data]);


        Db::table(self::$tableName)->insert($data);
    }

    public static function scan()
    {
        $codePath = "/data/codeCheck";
        $fortifyRetDir = "/data/fortify_result";

        if (!file_exists($codePath)) {
            mkdir($codePath, 0777, true);
        }
        if (!file_exists($fortifyRetDir)) {
            mkdir($fortifyRetDir, 0777, true);
        }
        while (true) {
            $endTime = date('Y-m-d', time() - 86400 * 15);
            $list = Db::table('code')->where(['is_delete' => 0])->whereTime('scan_time', '<=', $endTime)->orderRand()->limit(1)->select()->toArray();
            $count = count($list);

            foreach ($list as $value) {
                $prName = cleanString($value['name']);
                $codeUrl = $value['ssh_url'];
                addlog("开始执行扫描代码任务:{$prName}..." . PHP_EOL);
                //1. 拉取代码
                downCode($codePath, $prName, $codeUrl);

                //2. 扫描代码
                FortifyModel::startScan("{$codePath}/{$prName}", "{$fortifyRetDir}/{$prName}");

                $xmlFile = "{$fortifyRetDir}/{$prName}.xml";
                if (file_exists($xmlFile) === false) {
                    addlog(["fortify的XML文件不存在:{$xmlFile}", $value]);
                    continue;
                }
                $bugList = FortifyModel::getFortifData("{$fortifyRetDir}/{$prName}.xml");

                //4. 存储结果
                FortifyModel::addDataAll($value['id'], $bugList, $value['user_id']);

                //5. 更新
                if (file_exists("{$fortifyRetDir}/{$prName}.xml")) {
                    $value['scan_time'] = date('Y-m-d H:i:s');
                    Db::table('code')->update($value);
                }
            }

            addlog("fortify 完成本次扫描任务，休息10秒");
            sleep(10);
        }
    }

    public static function kunLunScan()
    {
        $codePath = "/data/codeCheck";
        //判断目录是否存在
        if (!file_exists($codePath)) {
            mkdir($codePath, 0777, true);
        }

        while (true) {
            $endTime = date('Y-m-d', time() - 86400 * 15);
            $list = Db::table('code')->whereTime('kunlun_scan_time', '<=', $endTime)->orderRand()->limit(1)->select()->toArray();

            foreach ($list as $value) {
                $prName = cleanString($value['name']);

                $codeUrl = $value['ssh_url'];
                //1. 拉取代码
                downCode($codePath, $prName, $codeUrl);

                //扫描代码
                $result = KunlunModel::startScan("{$codePath}/{$prName}");
                if ($result) {
                    $value['kunlun_scan_time'] = date('Y-m-d H:i:s');
                    Db::table('code')->update($value);
                }
            }

            print_r("跑完一圈,休息10秒..." . PHP_EOL);
            sleep(10);
        }
    }

    public static function semgrep()
    {
        $codePath = "/data/codeCheck";
        $fortifyRetDir = "/data/semgrep_result";

        while (true) {
            $endTime = date('Y-m-d', time() - 86400 * 15);
            $list = Db::table('code')->whereTime('semgrep_scan_time', '<=', $endTime)->orderRand()->limit(1)->select()->toArray();
            $count = count($list);
            print("开始执行semgrep扫描代码任务,{$count} 个项目等待扫描..." . PHP_EOL);
            foreach ($list as $value) {
                $prName = cleanString($value['name']);
                $codeUrl = $value['ssh_url'];
                //1. 拉取代码
                downCode($codePath, $prName, $codeUrl);

                //2. 扫描代码
                $outJson = "{$fortifyRetDir}/{$prName}.json";
                SemgrepModel::startScan("{$codePath}/{$prName}", $outJson);

                //4. 存储结果
                SemgrepModel::addDataAll($value['id'], $outJson, $value['user_id']);

                //5. 更新
                if (file_exists($outJson)) {
                    $value['semgrep_scan_time'] = date('Y-m-d H:i:s');
                    Db::table('code')->update($value);
                }
            }

            sleep(10);
        }
    }
}

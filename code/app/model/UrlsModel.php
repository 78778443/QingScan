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
use think\facade\App;
use think\facade\Db;

class UrlsModel extends BaseModel
{

    public static $tableName = 'urls';


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

        $result = Db::table(self::$tableName)->where($where)->select()->toArray();

        return $result;
    }

    private static function getCount($where, array $otherParam = [])
    {
        $group = $otherParam['group'] ?? '';


        $api = Db::table(self::$tableName)->where($where);

        if ($group) {
            $api = $api->group($group);
        }

        $count = $api->group($group)->count();

        return $count;

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

        Db::table('crawler')->where($where)->update($data);
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

        Db::table(self::$tableName)->extra('IGNORE')->insert($data, 'IGNORE');
    }

    public static function sqlmapScan()
    {
        ini_set('max_execution_time', 0);
        while (true) {
            $list = Db::name('urls')->whereTime('sqlmap_scan_time', '<=', date('Y-m-d H:i:s', time() - (86400 * 15)))->where('is_delete', 0)->field('id,url,app_id')->limit(1)->orderRand()->select()->toArray();
            $file_path = '/data/tools/sqlmap/';
            foreach ($list as $k => $v) {
//            $v['url'] = "http://172.22.43.246:8888/home/index.php?m=tiezi&a=index&bk=5";
                $arr = parse_url($v['url']);

                $blackExt = ['.js', '.css', '.json', '.png', '.jpg', '.jpeg', '.gif', '.mp3', '.mp4'];
                //没有可以注入的参数
                if (!isset($arr['query']) or in_array_strpos($arr['path'], $blackExt) or (strpos($arr['query'], '=') === false)) {
                    addlog(["URL地址不存在可以注入的参数", $v['url']]);
                    self::scanTime('urls',$v['id'],'sqlmap_scan_time');
                    continue;
                }

                $cmd = "cd {$file_path}  && python3 ./sqlmap.py -u '{$v['url']}' --batch  --random-agent --output-dir={$file_path}";
                systemLog($cmd);
                $host = $arr['host'];
                $outdir = $file_path . "{$host}/";
                $outfilename = "{$outdir}/log";

                //sqlmap输出异常
                if (!is_dir($outdir) or !file_exists($outfilename) or !filesize($outfilename)) {
                    addlog(["sqlmap输出异常", $v['url']]);
                    self::scanTime('urls',$v['id'],'sqlmap_scan_time');
                    continue;
                }
                $ddd = file_get_contents($outfilename);
                $arr = explode("\n", $ddd);

                $data = [];
                foreach ($arr as $tmp) {
                    $tempv2 = explode(":", $tmp);
                    if (count($tempv2) == 2) {
                        $data[trim($tempv2[0])][] = trim($tempv2[1]);
                    }
                }

                $bbb = [
                    'system' => $data['web server operating system'][0],
                    'application' => $data['web application technology'][0],
                    'dbms' => $data['back-end DBMS'][0],
                    'urls_id' => $v['id'],
                ];
                foreach ($data['Payload'] as $key => $value) {
                    $bbb['payload'] = $value;
                    $bbb['title'] = $data['Title'][$key];
                    $bbb['type'] = $data['Type'][$key];
                    Db::name('urls_sqlmap')->insert($bbb);
                }

                Db::name('urls')->where('id', $v['id'])->update(['sqlmap_scan_time' => date('Y-m-d H:i:s', time())]);
                systemLog("rm -rf $outdir");
            }
            sleep(10);
        }
    }

    public static function reptile()
    {
        ini_set('max_execution_time', 0);
        while (true) {
            $list = Db::name('urls')->where('is_delete', 0)->field('id,url')->limit(5)->orderRand()->select()->toArray();
            foreach ($list as $k => $v) {
                $arr = parse_url($v['url']);
                if (in_array_strpos($arr['path'], ['.js', '.css', '.json', '.png', '.jpg', '.jpeg', '.gif', '.mp3', '.mp4'])) {
                    addlog("此URL地址不是普通HTML文本:{$v['url']}");
                    continue;
                }
                $result = curl_get_url_head($v['url']);
                if ($result['code'] != '200') {
                    addlog("此URL地址状态码不是200:{$v['url']}");
                    continue;
                }
                $content = curl_get($v['url']);
                $data = [];
                $preg_phone = '/^1[345789]\d{9}$/ims';
                preg_match_all($preg_phone, $content, $phone);
                if (preg_match_all($preg_phone, $content, $phone)) {
                    $data['phone'] = json_encode($phone[0]);
                }

                $id_card_reg = '/^[1-9]\d{5}(18|19|([23]\d))\d{2}((0[1-9])|(10|11|12))(([0-2][1-9])|10|20|30|31)\d{3}[0-9Xx]$/';
                if (preg_match_all($id_card_reg, $content, $id_card)) {
                    $data['id_card'] = json_encode($id_card[0]);
                }

                $email_reg = '/^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/';
                if (preg_match_all($email_reg, $content, $email)) {
                    $data['email'] = json_encode($email[0]);
                }

                $icp_reg = '/^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/';
                if (preg_match_all($preg_phone, $icp_reg, $icp)) {
                    $data['icp'] = json_encode($icp[0]);
                }

                if ($data) {
                    Db::name('urls')->where('id', $v['id'])->update($data);
                }
            }
            sleep(10);
        }
    }
}

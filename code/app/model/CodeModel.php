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


class CodeModel extends BaseModel
{

    public static $tableName = 'code';


    /**
     * @return string
     */
    public static function getFortifData($xmlPath)
    {

        $str = file_get_contents($xmlPath);

        $obj = simplexml_load_string($str, "SimpleXMLElement", LIBXML_NOCDATA);
        $test = json_decode(json_encode($obj), true);


        $list = $test['ReportSection'][2]['SubSection']['IssueListing']['Chart']['GroupingSection'];
        foreach ($list as $key => $value) {
            foreach ($value['Issue'] as $k => $val) {
                if (strpos($val['Source']['FileName'], '.php') === false) {
                    unset($value['Issue'][$k]);
                }
            }

            if (empty($value['Issue'])) {
                unset($list[$key]);
            }
        }

        $data = [];
        foreach ($list as $value) {
            foreach ($value['Issue'] as $val) {
                unset($val['@attributes']);

                foreach ($val as &$v) {
                    $v = is_string($v) ? $v : json_encode($v);
                }

                $data[] = $val;
            }
        }

        return $data;
    }

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
    private static function getList($where, int $limit = 15, int $page = 1, array $order = ['id' => 'desc'], array $otherParam = [])
    {

        //$where['author'] = [''];
        $result = Db::table(self::$tableName)->where($where)->order($order)->page($page)->select()->toArray();

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

    public static function getListByWhere($where, int $limit = 15, int $page = 1, array $order = ['id' => 'desc'])
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


    public static function updateScanLast(int $id)
    {
        $where = ['id' => $id];
        $data = ['scan_last' => date('Y-m-d H:i:s'), 'scan_status' => 1];
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
    public static function addDataAll(array $data)
    {

        foreach ($data as $value) {

            try {
                self::add($value);

            } catch (Exception $e) {
                var_dump($value);
            }

        }
    }

    public static function startScan($codePath, $outPath)
    {

        $buildId = md5($codePath);

        if (file_exists($outPath)) {
            chmod($outPath, 0777);
        }

        $base = "cd /data/tools/fortify_linux/bin && ";
        $cmd = $base . "./sourceanalyzer -b {$buildId} -clean";

        systemLog($cmd);
        $cmd = $base . "./sourceanalyzer -b {$buildId} -Xmx8192M -Xms2048M -Xss48M     -source 1.8 -machine-output   {$codePath}";
        systemLog($cmd);
        $cmd = $base . "./sourceanalyzer -b {$buildId} -scan -format fpr -f {$outPath}.fpr -machine-output ";
        systemLog($cmd);
        $cmd = $base . "./ReportGenerator  -format xml -f {$outPath}.xml -source {$outPath}.fpr -template DeveloperWorkbook.xml";
        systemLog($cmd);
    }


    public static function addData(array $data)
    {
        return self::add($data);
    }

    private static function add($data)
    {
        return Db::table(self::$tableName)->insertGetId($data);
    }

    public static function getScanNum(array $codeIds)
    {

        $codeIdStr = implode(',', $codeIds);

        $tempArr = Db::table('fortify')->whereIn('code_id', $codeIdStr)
            ->field('code_id,count(code_id) as num')
            ->group('code_id')->select()->toArray();
        $data['fortifyNum'] = array_column($tempArr, 'num', 'code_id');

        /*$tempArr = Db::connect('kunlun')->table("index_scanresulttask")->whereIn('code_id', $codeIdStr)
            ->field('code_id,count(code_id) as num')
            ->group('code_id')->select()->toArray();
        $data['kunlunNum'] = array_column($tempArr, 'num', 'code_id');*/
        $data['kunlunNum'] = 0;

        $tempArr = Db::table('semgrep')->whereIn('code_id', $codeIdStr)
            ->field('code_id,count(code_id) as num')
            ->group('code_id')->select()->toArray();
        $data['semgrepNum'] = array_column($tempArr, 'num', 'code_id');

        $tempArr = Db::table('mobsfscan')->whereIn('code_id', $codeIdStr)
            ->field('code_id,count(code_id) as num')
            ->group('code_id')->select()->toArray();
        $data['mobsfscanNum'] = array_column($tempArr, 'num', 'code_id');

        $tempArr = Db::table('murphysec')->whereIn('code_id', $codeIdStr)
            ->field('code_id,count(code_id) as num')
            ->group('code_id')->select()->toArray();
        $data['murphysecNum'] = array_column($tempArr, 'num', 'code_id');

        $tempArr = Db::table('code_webshell')->whereIn('code_id', $codeIdStr)
            ->field('code_id,count(code_id) as num')
            ->group('code_id')->select()->toArray();
        $data['hemaNum'] = array_column($tempArr, 'num', 'code_id');

        $tempArr = Db::table('code_composer')->whereIn('code_id', $codeIdStr)
            ->field('code_id,count(code_id) as num')
            ->group('code_id')->select()->toArray();
        $data['phpNum'] = array_column($tempArr, 'num', 'code_id');

        $tempArr = Db::table('code_python')->whereIn('code_id', $codeIdStr)
            ->field('code_id,count(code_id) as num')
            ->group('code_id')->select()->toArray();
        $data['pythonNum'] = array_column($tempArr, 'num', 'code_id');

        $tempArr = Db::table('code_java')->whereIn('code_id', $codeIdStr)
            ->field('code_id,count(code_id) as num')
            ->group('code_id')->select()->toArray();
        $data['javaNum'] = array_column($tempArr, 'num', 'code_id');

        return $data;
    }

    public static function code_php()
    {
        ini_set('max_execution_time', 0);
        $codePath = "/data/codeCheck";
        while (true) {
            processSleep(1);
            $where[] = ['project_type','in',[1,6]];
            $list = self::getCodeStayScanList('composer_scan_time',$where);
            foreach ($list as $k => $v) {
                PluginModel::addScanLog($v['id'], __METHOD__, 2);
                self::scanTime('code', $v['id'], 'composer_scan_time');

                $value = $v;
                $prName = cleanString($value['name']);
                $codeUrl = $value['ssh_url'];
                $filepath = "/data/codeCheck/{$prName}";
                if (!file_exists($filepath)) {
                    downCode($codePath, $prName, $codeUrl, $value['is_private'], $value['username'], $value['password'], $value['private_key']);
                }
                $fileArr = getFilePath($filepath, 'composer.lock');
                if (!$fileArr) {
                    PluginModel::addScanLog($v['id'], __METHOD__, 2, 2);
                    addlog("扫描composer依赖失败,composer.lock 依赖文件不存在:{$filepath}");
                    continue;
                }

                foreach ($fileArr as $value) {
                    $json = file_get_contents($value['file']);
                    if (empty($json)) {
                        PluginModel::addScanLog($v['id'], __METHOD__, 2, 2);
                        addlog("项目文件内容为空:{$value['file']}");
                        continue;
                    }
                    $json = str_replace('"require-dev"', '"require_dev"', $json);
                    $json = str_replace('"notification-url"', '"notification_url"', $json);
                    $arr = json_decode($json, true);
                    $packages = $arr['packages'];
                    Db::name('code_composer')->where(['code_id' => $v['id']])->delete();
                    foreach ($packages as $val) {
                        foreach ($val as &$temp) {
                            $temp = is_string($temp) ? $temp : var_export($temp, true);
                        }

                        $val['user_id'] = $v['user_id'];
                        $val['code_id'] = $v['id'];
                        $val['create_time'] = date('Y-m-d H:i:s', time());

                        Db::name('code_composer')->insert($val);
                        addlog("composer依赖扫描数据写入成功,内容为:" . json_encode($val));
                    }
                }
                PluginModel::addScanLog($v['id'], __METHOD__, 2, 1);
            }
            sleep(30);
        }
    }

    public static function giteeProject()
    {
        while (true) {
            processSleep(1);
            ini_set('max_execution_time', 0);

            systemLog('cd /data/tools/reptile && python3 ./giteeProject.py');

            sleep(3600);
        }
    }
}
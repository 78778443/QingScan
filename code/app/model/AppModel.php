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
use Requests;
use think\facade\App;
use think\facade\Db;

class AppModel extends BaseModel
{
    public static $tableName = "app";


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

    public static function fofaSubdomain()
    {
        $user = ConfigModel::value('fofa_user');
        $token = ConfigModel::value('fofa_token');
        while (true) {
            processSleep(1);
            $appList = self::getAppStayScanList('subdomain_time',10);
            foreach ($appList as $appInfo) {
                PluginModel::addScanLog($appInfo['id'], __METHOD__, 0);
                $str = urlencode(base64_encode('domain="' . parse_url($appInfo['url'])['host'] . '"'));
                $list = Requests::get("https://fofa.so/api/v1/search/all?email={$user}&key={$token}&qbase64=" . $str);

                $list = json_decode($list->body, true)['results'];

                foreach ($list as $value) {
                    $url = $value[0] . ":" . $value[2] . "/";
                    if (strpos($url, 'https://') === false) {
                        $url = "http://" . $url;
                    }
                    $subDomain = ['name' => $appInfo['name'], 'url' => $url];
                    Db::table(self::$tableName)->extra("IGNORE")->insert($subDomain);
                }
                $appInfo['subdomain_time'] = date('Y-m-d H:i:s');
                Db::table(self::$tableName)->save($appInfo);
                PluginModel::addScanLog($appInfo['id'], __METHOD__, 0,1);
            }

            print_r("休息10秒..." . PHP_EOL);
            sleep(10);
        }
    }


    public static function getCrawlerInfo($crawlerId)
    {

        //查询具体数据,并刷新缓存
        $result = self::getList(['id' => $crawlerId]);


        return $result[0] ?? false;

    }

    /**
     * @param  $where
     * @param string $limit
     * @param array $otherParam
     * @return mixed
     */
    private static function getList($where, $limit = '', int $page = 1, array $otherParam = [])
    {
        $db = new MysqlLib(getMysql());

        $field = $otherParam['field'] ?? '*';
        $group = $otherParam['group'] ?? '';
        $order = $otherParam['order'] ?? 'id desc';

        $db = $db->table(self::$tableName);

        if (!empty($limit)) {
            $page = max($page, 1);
            $limit = ($page - 1) * $limit . ",$limit";
            $db->limit($limit);
        }

        if ($group) {
            $db->group($group);
        }
        if (!empty($order)) {
            $db->order($order);
        }

        if (isset($where['status']) == false) {
            $where['status'] = [0, 1, 2];
        }


        $result = Db::table(self::$tableName)->where($where)->select()->toArray();

        return $result;
    }

    public static function getListByGroup($groupId, $limit = 10)
    {
        $where = ['group_id' => $groupId];

        return self::getList($where, $limit);
    }

    public static function getListByWherePage($where, $page, $pageSize = 15)
    {

        $list = self::getList($where, $pageSize, $page);


        $count = self::getCount($where);

        return ['list' => $list, 'count' => $count, 'pageSize' => $pageSize];
    }

    public static function getCount($where, array $otherParam = [])
    {
        $db = new MysqlLib(getMysql());
        $group = $otherParam['group'] ?? '';

        $db = $db->table(self::$tableName);

        if ($group) {
            $db->group($group);
        }
        if (isset($where['status']) == false) {
            $where['status'] = ['>', -1];
        }
        $result = $db->where($where)->count();


        return $result[0]['num'] ?? 0;
    }

    public static function getListByWhere($where, $limit = 10)
    {

        $list = self::getList($where, $limit);

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
        addlog(['更新数据', $where, $data]);
        $crawlerApi = new MysqlLib();

        //        //更新条件
        //        $crawlerApi = $crawlerApi->table(self::$tableName)->where($where);
        //
        //        //执行更新并返回数据
        //        $crawlerApi->update($data);

        Db::table(self::$tableName)->where($where)->update($data);
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


    public static function updateScanStatus(int $id, int $status)
    {
        $where = ['id' => $id];
        $data = ['scan_status' => $status];
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
        /*$datetime = date('Y-m-d H:i:s', time() + 86400 * 365);
        if ($data['is_xray'] == 0) {
            $data['xray_scan_time'] = $datetime;
        }
        if ($data['is_awvs'] == 0) {
            $data['awvs_scan_time'] = $datetime;
        }
        if ($data['is_whatweb'] == 0) {
            $data['whatweb_scan_time'] = $datetime;
        }
        if ($data['is_one_for_all'] == 0) {
            $data['subdomain_scan_time'] = $datetime;
        }*/
//        if ($data['is_hydra'] == 0) {
//            if (!Db::name('host')->where('app_id',$data[''])) {
//
//            }
//        }
        /*if ($data['is_dirmap'] == 0) {
            $data['dirmap_scan_time'] = $datetime;
        }*/
        //return Db::table(self::$tableName)->insert($data);
        return Db::table(self::$tableName)->insertGetId($data);
    }


    // web指纹识别
    public static function whatweb()
    {
        ini_set('max_execution_time', 0);
        $file_path = '/data/tools/whatweb';
        while (true) {
            processSleep(1);
            $list = self::getAppStayScanList('whatweb_scan_time',10);
            @mkdir($file_path,0777, true);
            foreach ($list as $k => $v) {
                if (!self::checkToolAuth(1,$v['id'],'whatweb')) {
                    continue;
                }

                PluginModel::addScanLog($v['id'], __METHOD__, 0);
                self::scanTime('app',$val['id'],'whatweb_scan_time');

                $filename = "{$file_path}/whatweb.json";
                $cmd = "whatweb {$v['url']} --log-json $filename";
                systemLog($cmd);
                if (file_exists($filename) == false) {
                    PluginModel::addScanLog($v['id'], __METHOD__, 0,2);
                    addlog(["whatweb扫描结果文件不存在:{$filename}"]);
                    continue;
                }

                $contents = file_get_contents($filename);
                $arr = json_decode($contents,true);
                if ($contents && is_array($arr)) {
                    $target = [];
                    $http_status = [];
                    $request_config = [];
                    $plugins = [];
                    foreach ($arr as $val) {
                        $target[] = isset($val['target'])?$val['target']:[];
                        $http_status[] = isset($val['http_status'])?$val['http_status']:[];
                        $request_config[] = isset($val['request_config'])?$val['request_config']:[];
                        $plugins[] = isset($val['plugins'])?$val['plugins']:[];
                    }
                    $data = [
                        'app_id'=>$v['id'],
                        'user_id'=>$v['user_id'],
                        'target'=>json_encode($target,JSON_UNESCAPED_UNICODE),
                        'http_status'=>json_encode($http_status,JSON_UNESCAPED_UNICODE),
                        'request_config'=>json_encode($request_config,JSON_UNESCAPED_UNICODE),
                        'plugins'=>json_encode($plugins,JSON_UNESCAPED_UNICODE),
                        'create_time'=>date('Y-m-d H:i:s',time()),
                    ];
                    if ($data) {
                        Db::name('app_whatweb')->insert($data);
                    }
                } else {
                    PluginModel::addScanLog($v['id'], __METHOD__, 0,2);
                    addlog(["whatweb扫描结果文件内容格式错误:{$filename}"]);
                }
                @unlink($filename);
                PluginModel::addScanLog($v['id'], __METHOD__,0, 1);
            }
            sleep(10);
        }
    }

    public static function whatwebPocTest(){
        ini_set('max_execution_time', 0);
        while (true) {
            processSleep(1);
            $list = Db::name('app_whatweb')->whereTime('poc_scan_time', '<=', date('Y-m-d H:i:s', time() - (86400 * 15)))->limit(1)->orderRand()->select()->toArray();
            foreach ($list as $val) {
                self::scanTime('app_whatweb',$val['id'],'poc_scan_time');
                $whatwebArr = whatwebArr($val['plugins']);
                foreach ($whatwebArr as $k=>$v) {
                    $where[] = ['product_name','=',$k];
                    $where[] = ['affect_ver','like',"%{$v}%"];
                    $where[] = ['is_delete','=',0];
                    $where[] = ['is_poc','=',1];
                    $cve_num = Db::name('vulnerable')->where($where)->value('cve_num');
                    if ($cve_num) { // 存在poc
                        $pocfile = Db::name('pocs_file')->where('cve_num',$cve_num)->value('poc_file');
                        $url = Db::name('app')->where('id',$val['app_id'])->value('url');
                        if (strpos($pocfile,'/data/') === false) {
                            $pocfile = '/data/'.$pocfile;
                        }
                        $cmd = "pocsuite -r {$pocfile} -u {$url} --verify";
                        execLog($cmd, $output);
                        addlog(["poc验证结束", $val['id'], $url, $cmd, json_encode($output)]);

                        $data = [
                            'whatweb_id'=>$val['id'],
                            'url'=>$val['url'],
                            'app_id'=>$val['app_id'],
                            'user_id'=>$val['user_id'],
                            'key'=>$k,
                            'value'=>$v,
                            'result'=>json_encode($output),
                            'output'=>date('Y-m-d H:i:s',time())
                        ];
                        Db::name('app_whatweb_poc')->insert($data);
                    }
                }
            }
            sleep(10);
        }
    }

    //获取项目列表
    public static function getAppName()
    {
        $appList = Db::table(self::$tableName)->field('id,name')->select()->toArray();
        $appList = array_column($appList, 'name', 'id');
        return $appList;
    }


    public static function updateScanTime($id,$filed)
    {
        $data = [$filed => date('Y-m-d H:i:s', time())];
        Db::name('app')->where('id', $id)->update($data);
    }
}

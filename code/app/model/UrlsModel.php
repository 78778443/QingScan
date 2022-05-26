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
        Db::table(self::$tableName)->extra('IGNORE')->insert($data, 'IGNORE');
    }

    public static function sqlmapScan()
    {
        ini_set('max_execution_time', 0);
        $tools = '/data/tools/sqlmap/';
        $file_path = $tools.'result/';
        while (true) {
            processSleep(1);
            $endTime = date('Y-m-d', time() - 86400 * 15);
            $where[] = ['is_delete','=',0];
            $list = Db::name('urls')->whereTime('sqlmap_scan_time', '<=', $endTime)->where($where)->field('id,url,app_id,user_id')->limit(5)->orderRand()->select()->toArray();
            foreach ($list as $k => $v) {
                if (!self::checkToolAuth(1,$v['app_id'],'sqlmap')) {
                    continue;
                }
                PluginModel::addScanLog($v['id'], __METHOD__, 3);
                self::scanTime('urls',$v['id'],'sqlmap_scan_time');

                $arr = parse_url($v['url']);
                $blackExt = ['.js', '.css', '.png', '.jpg', '.jpeg', '.gif', '.mp3', '.mp4','.ico','.bmp','.wmv','.avi','.psd'];
                //没有可以注入的参数
                if (!isset($arr['query']) or in_array_strpos(strtolower($arr['path']), $blackExt) or (strpos($arr['query'], '=') === false)) {
                    PluginModel::addScanLog($v['id'], __METHOD__, 3,2);
                    addlog(["URL地址不存在可以注入的参数", $v['url']]);
                    continue;
                }
                $cmd = "cd {$tools}  && python3 ./sqlmap.py -u '{$v['url']}' --batch  --random-agent --output-dir={$file_path}";
                systemLog($cmd);
                $host = $arr['host'];
                $outdir = $file_path . "{$host}/";
                $outfilename = "{$outdir}/log";

                //sqlmap输出异常
                if (!is_dir($outdir) or !file_exists($outfilename) or !filesize($outfilename)) {
                    PluginModel::addScanLog($v['id'], __METHOD__, 3,1);
                    addlog(["sqlmap没有找到注入点", $v['url']]);
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
                    'system' => isset($data['web server operating system'])?$data['web server operating system'][0]:'',
                    'application' => isset($data['web application technology'])?$data['web application technology'][0]:'',
                    'dbms' => isset($data['back-end DBMS'])?$data['back-end DBMS'][0]:'',
                    'urls_id' => $v['id'],
                    'app_id' => $v['app_id'],
                    'user_id' => $v['user_id'],
                ];
                foreach ($data['Payload'] as $key => $value) {
                    $bbb['payload'] = $value;
                    $bbb['title'] = $data['Title'][$key];
                    $bbb['type'] = $data['Type'][$key];
                    Db::name('urls_sqlmap')->insert($bbb);
                }
                addlog(["sqlmap扫描成功数据已写入：", $v['url']]);
                systemLog("rm -rf $outdir");
                PluginModel::addScanLog($v['id'], __METHOD__, 3,1);
            }
            //exit;
            sleep(5);
        }
    }

    public static function reptile()
    {
        ini_set('max_execution_time', 0);
        while (true) {
            processSleep(1);
            $list = Db::name('urls')->where('is_delete', 0)->field('id,url')->limit(5)->orderRand()->select()->toArray();
            foreach ($list as $v) {
                PluginModel::addScanLog($v['id'], __METHOD__, 0);
                $arr = parse_url($v['url']);
                if (in_array_strpos($arr['path'], ['.js', '.css', '.json', '.png', '.jpg', '.jpeg', '.gif', '.mp3', '.mp4'])) {
                    PluginModel::addScanLog($v['id'], __METHOD__, 3,2);
                    addlog("此URL地址不是普通HTML文本:{$v['url']}");
                    continue;
                }
                $result = curl_get_url_head($v['url']);
                if ($result['code'] != '200') {
                    PluginModel::addScanLog($v['id'], __METHOD__, 3,2);
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
                PluginModel::addScanLog($v['id'], __METHOD__, 3,1);
            }
            sleep(10);
        }
    }
}

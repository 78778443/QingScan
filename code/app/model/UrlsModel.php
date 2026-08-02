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

    public static function sqlInjectScan()
    {
        $where = ['tool' => 'scan_url_sql_inject', 'status' => 0];
        $list = Db::table('task_scan')->where($where)->limit(10)->select()->toArray();
        foreach ($list as $task) {
            Db::table('task_scan')->where(['id' => $task['id']])->update(['status' => 1]);
            $v = json_decode($task['ext_info'], true);

            if (!self::checkToolAuth(1, $v['app_id'], 'sql_inject')) {
                continue;
            }
            PluginModel::addScanLog($v['id'], __METHOD__, 3);


            $arr = parse_url($v['url']);
            $blackExt = ['.js', '.css', '.png', '.jpg', '.jpeg', '.gif', '.mp3', '.mp4', '.ico', '.bmp', '.wmv', '.avi', '.psd'];
            //没有可以注入的参数
            if (!isset($arr['query']) or in_array_strpos(strtolower($arr['path']), $blackExt) or (strpos($arr['query'], '=') === false)) {
                PluginModel::addScanLog($v['id'], __METHOD__, 3, 2);
                addlog(["URL地址不存在可以注入的参数", $v['url']]);
                continue;
            }

            // 使用内置纯 PHP SQL 注入检测引擎
            $result = \app\scan\SqlInjectScan::scan($v['url']);

            //未发现注入点
            if (empty($result)) {
                PluginModel::addScanLog($v['id'], __METHOD__, 3, 1);
                addlog(["SQL注入检测未发现注入点", $v['url']]);
                continue;
            }

            foreach ($result as $item) {
                $bbb = [
                    'system' => '',
                    'application' => '',
                    'dbms' => '',
                    'urls_id' => $v['id'],
                    'app_id' => $v['app_id'],
                    'user_id' => $v['user_id'],
                    'title' => $item['result'],
                    'type' => $item['result'],
                    'payload' => $item['payload'],
                    'create_time' => date('Y-m-d H:i:s', time()),
                ];
                Db::name('urls_sqlmap')->insert($bbb);
            }
            addlog(["SQL注入扫描成功数据已写入：", $v['url']]);
            PluginModel::addScanLog($v['id'], __METHOD__, 3, 1);
        }

    }

    public static function reptile()
    {

        $list = Db::name('urls')->where('is_delete', 0)->field('id,url')->limit(5)->orderRand()->select()->toArray();
        foreach ($list as $v) {
            PluginModel::addScanLog($v['id'], __METHOD__, 0);
            $arr = parse_url($v['url']);
            if (in_array_strpos($arr['path'], ['.js', '.css', '.json', '.png', '.jpg', '.jpeg', '.gif', '.mp3', '.mp4'])) {
                PluginModel::addScanLog($v['id'], __METHOD__, 3, 2);
                addlog("此URL地址不是普通HTML文本:{$v['url']}");
                continue;
            }
            $result = curl_get_url_head($v['url']);
            if ($result['code'] != '200') {
                PluginModel::addScanLog($v['id'], __METHOD__, 3, 2);
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
            PluginModel::addScanLog($v['id'], __METHOD__, 3, 1);
        }

    }
}

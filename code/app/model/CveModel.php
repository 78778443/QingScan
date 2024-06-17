<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/8/15
 * Time: 上午10:54
 */


namespace app\model;


use think\facade\Db;
use WpOrg\Requests\Requests;

class CveModel extends BaseModel
{


    public static function cveScan()
    {
        $endTime = date('Y-m-d', time() - 86400 * 15);
        $where = [];
        $hostLit = Db::table('vulnerable')->where($where)->orderRand()->limit(5)->select()->toArray();
        foreach ($hostLit as $val) {
            self::cve($val);
            Db::table('asm_host')->where(['id' => $val['id']])->save(['scan_time' => date('Y-m-d H:i:s')]);
        }
        print_r("本轮CVE扫描完成，休息10秒...");

    }


    public static function cve($cveInfo)
    {

        $pocInfo = Db::table('pocs_file')->where(['cve_num' => $cveInfo['cve_num']])->find();
        $user = ConfigModel::value('fofa_user');
        $token = ConfigModel::value('fofa_token');

        //将POC写入临时文件
        $tempPoc = "/tmp/{$cveInfo['cve_num']}";
        file_get_contents($tempPoc, $pocInfo['content']);

        //判断POC对应的工具类型
        if ($pocInfo['tool'] == 'pocsuite3') {
            $cmd = "pocsuite -r {$tempPoc} --dork-fofa  '{$cveInfo['fofa_con']}'   --fofa-user {$user}  --fofa-token  {$token}";
        } elseif ($pocInfo['tool'] == 'xray') {
            $cmd = "./xray_linux_amd64";
        }

        systemLog($cmd);
    }

    public static function execute_poc($cveNum)
    {
    }

    public static function fofaSearch()
    {
        $user = ConfigModel::value('fofa_user');
        $token = ConfigModel::value('fofa_token');
        if (empty($user) || empty($token)) {
            addlog(["fofa收集缺陷目标失败,没有获取到user或token", $user, $token]);
            return false;
        }


        $where = ['tool' => 'asm_domain_fofa', 'status' => 0];
        $list = Db::table('task_scan')->where($where)->limit(10)->select()->toArray();
        foreach ($list as $task) {
            Db::table('task_scan')->where(['id' => $task['id']])->update(['status' => 1]);
            $val = json_decode($task['ext_info'], true);

            $keywords = "domain=\"{$val['domain']}\"";
            $str = urlencode(base64_encode($keywords));
            $url = "https://fofa.info/api/v1/search/all?email={$user}&key={$token}&qbase64=" . $str;

            $list = Requests::get($url);
            $list = json_decode($list->body, true)['results'] ?? [];

            foreach ($list as $temp) {
                $info = ['addr' => $temp[0], 'ip' => $temp[1], 'port' => $temp[2], 'query' => $keywords, 'vul_id' => $val['id']];
                Db::table('vul_target')->extra("IGNORE")->insert($info);
            }
            Db::table('vulnerable')->where(['id' => $val['id']])->save(['target_scan_time' => date('Y-m-d H:i:s')]);

        }


    }

    public static function searchCveFile()
    {

        $path = "/mnt/c/mycode/work/qing-scan/tools/pocs";


        $list = getDirFileName($path);
        foreach ($list as $fileName) {
            if (strstr($fileName, ".py") == false) {
                continue;
            }
            $content = file_get_contents($fileName);
            $regex = "/(cve|CVE)-\d\d\d\d-\d\d\d\d?\d?/";
            $num_matches = preg_match_all($regex, $content, $result);

            if ($num_matches == 0) {
                continue;
            }

            $cveArr = array_unique(array_map('strtoupper', $result[0]));
            $poc_file = str_replace(dirname(dirname(dirname(__DIR__))) . "/", "", $fileName);
            foreach ($cveArr as $cveNum) {
                $data = ['cve_num' => $cveNum, 'poc_file' => $poc_file];
                Db::table('vulnerable')->where(['cve_num' => $cveNum])->save(['is_poc' => 1]);
                Db::table('pocs_file')->extra('IGNORE')->insert($data);
            }
        }
    }
}

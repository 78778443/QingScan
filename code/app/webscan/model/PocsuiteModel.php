<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/8/15
 * Time: 上午10:54
 */


namespace app\webscan\model;

use app\model\BaseModel;
use think\facade\Db;

class PocsuiteModel extends BaseModel
{

    public static function updateIp()
    {
        $list = Db::table('pocsuite3')->select()->toArray();

        foreach ($list as $value) {
            $info = pathinfo($value['url']);
            $value['ip'] = gethostbyname(trim($info['basename']));
            Db::table('pocsuite3')->save($value);
        }
    }

    public static function updateIcp()
    {
        $list = Db::table('pocsuite3')->select()->toArray();

        foreach ($list as $value) {
            $info = pathinfo($value['url']);
            $domain = trim($info['basename']);

            //如果是IP，就不调用API接口了
            if (filter_var($domain, FILTER_VALIDATE_IP)) {
                continue;
            }
            $url = "https://apidatav2.chinaz.com/single/icp?key=939bdebee3e74820ac6abfd8414523c9&domain={$domain}";
            $icpInfo = json_decode(file_get_contents($url), true);
            if ($icpInfo['StateCode'] == 1) {
                $value['CompanyName'] = $icpInfo['Result']['CompanyName'];
                $value['SiteLicense'] = $icpInfo['Result']['SiteLicense'];
                $value['CompanyType'] = $icpInfo['Result']['CompanyType'];
                Db::table('pocsuite3')->save($value);
            }
        }

    }

    public static function getCompanyInfo($name)
    {
        $headers = array();
        array_push($headers, "X-Bce-Signature:AppCode/" . "2e8ddd3610be457f92e92c845dac8f82");
        array_push($headers, "Content-Type" . ":" . "application/json;charset=UTF-8");

        $url = "http://gwgp-dc8od3uiqjb.n.bdcloudapi.com" . "/enterprise2/query" . "?company={$name}&creditno=null&regno=null&";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, true);

        list($header, $body) = explode("\r\n\r\n", curl_exec($curl), 2);
        return json_decode($body, true);
    }

    public static function updateCompony()
    {
        $list = Db::table('pocsuite3')->whereNotNull('CompanyName')->whereNull("regcapital")->limit(2)->select()->toArray();
        foreach ($list as $value) {
            $info = self::getCompanyInfo($value['CompanyName']);
            if (intval($info['status']) != 0) {
                addlog( "{$value['CompanyName']} 没有获取到公司信息" );
                continue;
            }
            $value['regcapital'] = $info['result']['regcapital'] ?? "";
            $value['regaddress'] = $info['result']['regaddress'] ?? "";

            if (empty($value['regcapital'])) {
                echo "{$value['CompanyName']} 没有获取到公司注册资金信息" . PHP_EOL;
                continue;
            }
            Db::table('pocsuite3')->save($value);
        }
    }
}

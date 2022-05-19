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

class CeHuiModel extends BaseModel
{

    public static $tableName = 'cehui';

    public static function main()
    {
        //获取当前IP
        $ip = file_get_contents("http://ipinfo.me");
        var_dump($ip);

        //获取当前网段

        //扫描当前存活主机
        $portStr = "21,22,23,25,53,80,81,110,111,123,135,137,139,161,389,443,445,465,500,515,520,523,548,623,636,873,902,1080,1099,1433,1521,1604,1645,1701,1883,1900,2049,2181,2375,2379,2425,3128,3306,3389,4730,5060,5222,5351,5353,5432,5555,5601,5672,5683,5900,5938,5984,6000,6379,7001,7077,8080,8081,8443,8545,8686,9000,9001,9042,9092,9100,9200,9418,9999,11211,27017,37777,50000,50070,61616";

        $cmd = "masscan --ports {$portStr} {$ip}  --max-rate 50000  --wait  5 |grep Discovered";

        //扫描存活主机开放的端口

        //组件识别


        //HTML指纹识别


        //TCP端口服务识别
    }


}

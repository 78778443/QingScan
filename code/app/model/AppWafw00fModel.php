<?php

namespace app\model;

use think\facade\App;
use think\facade\Db;

class AppWafw00fModel extends BaseModel
{
    public static function wafw00fScan(){
        ini_set('max_execution_time', 0);
        $tools = '/data/tools/wafw00f/wafw00f';
        while (true) {
            $list = Db::name('app')->whereTime('wafw00f_scan_time', '<=', date('Y-m-d H:i:s', time() - (86400 * 15)))->where('is_delete', 0)->orderRand()->limit(1)->field('id,url,user_id')->select();
            foreach ($list as $v) {
                self::scanTime('app',$v['id'],'wafw00f_scan_time');
                $cmd = "cd {$tools} && python3 main.py {$v['url']} -o result.json";
                systemLog($cmd);
                $result = json_decode(file_get_contents($tools.'/result.json'),true);
                if (!$result) {
                    addlog(["文件内容不存在:{$tools}/result.json"]);
                    continue;
                }
                $data = [
                    'app_id'=>$v['id'],
                    'user_id'=>$v['user_id'],
                    'url'=>$v['url'],
                    'detected'=>$result[0]['detected'],
                    'firewall'=>$result[0]['firewall'],
                    'manufacturer'=>$result[0]['manufacturer'],
                    'create_time'=>date('Y-m-d H:i:s',time()),
                ];
                Db::name('app_wafw00f')->insert($data);
            }
            sleep(20);
        }
    }
}
<?php

namespace app\model;

use think\facade\App;
use think\facade\Db;

class UnauthorizedModel extends BaseModel
{
    public static function unauthorizeScan(){
        ini_set('max_execution_time', 0);
        while (true) {
            processSleep(1);
            $app_list = Db::name('host')->whereTime('unauthorize_scan_time', '<=', date('Y-m-d H:i:s', time() - (86400 * 15)))->limit(10)->orderRand()->field('id,host,app_id')->select()->toArray();
            $file_path = '/data/tools/Scanunauthorized_2.0/';
            $filename = "{$file_path}result.txt";
            $host = "{$file_path}host.txt";
            file_put_contents($host,'');
            $file = fopen($host,'w');
            foreach ($app_list as $v) {
                fwrite($file,$v['host']);
            }
            fclose($file);
            foreach ($app_list as $k => $v) {
                if (!self::checkToolAuth(1,$v['app_id'],'unauthorize')) {
                    continue;
                }

                PluginModel::addScanLog($v['id'], __METHOD__, 1);
                self::scanTime('host',$v['id'],'unauthorize_scan_time');

                $cmd = "cd {$file_path}  && python3 ./Scanunauthorized_2.0.py";
                systemLog($cmd);
                if (file_exists($filename) && file_get_contents($filename)) {
                    $dataAll = [];
                    $file = fopen($filename,"r");
                    while(!feof($file)) {
                        $content= fgets($file);
                        if ($content) {
                            $arr = explode('-l-',$content);
                            $dataAll[] = [
                                'host_id'=>$v['id'],
                                'ip'=>$arr[0],
                                'port'=>$arr[1],
                                'text'=>$arr[2],
                                'user_id'=>$v['user_id'],
                                'create_time'=>date('Y-m-d H:i:s',time()),
                            ];
                        }
                    }
                    fclose($file);
                    Db::name('host_unauthorized')->insertAll($dataAll);
                }
                PluginModel::addScanLog($v['id'], __METHOD__, 1,1);
            }
            sleep(10);
        }
    }
}
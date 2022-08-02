<?php

namespace app\model;

use think\facade\App;
use think\facade\Db;

class UnauthorizedScanModel extends BaseModel
{
    public static function unauthorizeScan(){
        ini_set('max_execution_time', 0);
        while (true) {
            processSleep(1);
            $app_list = Db::name('host')->whereTime('unauthorize_scan_time', '<=', date('Y-m-d H:i:s', time() - (86400 * 15)))->limit(1)->orderRand()->field('id,host,app_id')->select()->toArray();
            $file_path = '/data/tools/Scanunauthorized_2.0/';
            $filename = "{$file_path}hydra.txt";
            foreach ($app_list as $k => $v) {
                if (!self::checkToolAuth(1,$v['app_id'],'unauthorizeScan')) {
                    continue;
                }

                PluginModel::addScanLog($v['id'], __METHOD__, 0,1);
                self::scanTime('host',$v['id'],'unauthorize_scan_time');

                $cmd = "cd {$file_path}  && python3 ./Scanunauthorized_2.0.py -i {$v['host']}";
                systemLog($cmd);
                if (file_exists($filename)) {
                    $result = json_decode(file_get_contents($filename),true);
                    if ($result['results']) {
                        $dataAll = [];
                        foreach ($result['results'] as $kk=>$vv) {
                            $data['username'] = $vv['login'];
                            $data['password'] = $vv['password'];
                            $data['host_id'] = $v['id'];
                            $data['app_id'] = $v['app_id'];
                            $data['user_id'] = $v['user_id'];
                            $data['create_time'] = date('Y-m-d H:i:s',time());
                            $dataAll[] = $data;
                        }
                        Db::name('host_hydra_scan_details')->insertAll($dataAll);
                    } else {
                        PluginModel::addScanLog($v['id'], __METHOD__, 0,1);
                        addlog(["hydra文件内容格式错误：{$filename}"]);
                    }
                    @unlink($filename);
                } else {
                    PluginModel::addScanLog($v['id'], __METHOD__, 0,1);
                    addlog(["hydra文件内容获取失败：{$filename}"]);
                }
                PluginModel::addScanLog($v['id'], __METHOD__, 1,1);
            }
            sleep(10);
        }
    }
}
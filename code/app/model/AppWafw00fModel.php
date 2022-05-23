<?php

namespace app\model;

use think\facade\App;
use think\facade\Db;

class AppWafw00fModel extends BaseModel
{
    public static function wafw00fScan()
    {
        ini_set('max_execution_time', 0);
        $tools = '/data/tools/wafw00f/wafw00f';
        $result_path = $tools . '/result.json';
        while (true) {
            processSleep(1);
            $list = self::getAppStayScanList('wafw00f_scan_time');
            foreach ($list as $v) {
                if (!self::checkToolAuth(1,$v['id'],'wafw00f')) {
                    continue;
                }

                PluginModel::addScanLog($v['id'], __METHOD__, 0);
                self::scanTime('app', $v['id'], 'wafw00f_scan_time');

                $cmd = "cd {$tools} && python3 main.py {$v['url']} -o result.json";
                systemLog($cmd);
                if (!file_exists($result_path)) {
                    PluginModel::addScanLog($v['id'], __METHOD__,0, 2);
                    addlog(["wafw00f扫描结果文件不存在:{$result_path}", $v]);
                    continue;
                }
                $result = json_decode(file_get_contents($result_path), true);
                if (!$result) {
                    PluginModel::addScanLog($v['id'], __METHOD__,0, 2);
                    addlog(["wafw00f扫描结果文件内容不存在:{$result_path}"]);
                    continue;
                }
                $data = [
                    'app_id' => $v['id'],
                    'user_id' => $v['user_id'],
                    'url' => $v['url'],
                    'detected' => $result[0]['detected'],
                    'firewall' => $result[0]['firewall'],
                    'manufacturer' => $result[0]['manufacturer'],
                    'create_time' => date('Y-m-d H:i:s', time()),
                ];
                if (Db::name('app_wafw00f')->insert($data)) {
                    addlog(["wafw00f扫描结果数据写入成功：".json_encode($data)]);
                } else {
                    addlog(["wafw00f扫描结果数据写入失败：".json_encode($data)]);
                }
                @unlink($result_path);
                PluginModel::addScanLog($v['id'], __METHOD__, 0,1);
            }
            sleep(20);
        }
    }
}
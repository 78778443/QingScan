<?php

namespace app\webscan\model;

use app\model\BaseModel;
use app\model\PluginModel;
use app\scan\WafScan;
use think\facade\Db;

class WafCheckModel extends BaseModel
{
    public static function wafCheckScan()
    {
        $list = self::getAppStayScanList('wafw00f_scan_time');
        foreach ($list as $v) {
            if (!self::checkToolAuth(1,$v['id'],'waf')) {
                continue;
            }

            PluginModel::addScanLog($v['id'], __METHOD__, 0);

            // 自研纯 PHP WAF 识别引擎（替代 python3 main.py + result.json）
            $result = WafScan::scan($v['url']);
            $data = [
                'app_id' => $v['id'],
                'user_id' => $v['user_id'],
                'url' => $v['url'],
                'detected' => $result['detected'],
                'firewall' => $result['firewall'],
                'manufacturer' => $result['manufacturer'],
                'create_time' => date('Y-m-d H:i:s', time()),
            ];
            if (Db::name('scan_waf')->insert($data)) {
                addlog(["WAF识别扫描结果数据写入成功：".json_encode($data)]);
            } else {
                addlog(["WAF识别扫描结果数据写入失败：".json_encode($data)]);
            }
            PluginModel::addScanLog($v['id'], __METHOD__, 0,1);
        }

    }
}
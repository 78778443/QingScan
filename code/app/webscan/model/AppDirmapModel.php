<?php


namespace app\webscan\model;


use app\model\BaseModel;
use app\model\PluginModel;
use think\facade\Db;

class AppDirmapModel extends BaseModel
{
    public static function dirmapScan()
    {
        $where = ['tool' => 'scan_scan_dir', 'status' => 0];
        $list = Db::table('task_scan')->where($where)->limit(10)->select()->toArray();
        foreach ($list as $task) {
            Db::table('task_scan')->where(['id' => $task['id']])->update(['status' => 1]);
            $v = json_decode($task['ext_info'], true);

            if (!self::checkToolAuth(1, $v['id'], 'dir_scan')) {
                continue;
            }

            PluginModel::addScanLog($v['id'], __METHOD__, 0);

            // 调用内置 PHP 目录扫描引擎，替换外部 python3 dirmap.py 调用
            $scanResult = \app\scan\DirScan::scan($v['url']);
            if (empty($scanResult)) {
                PluginModel::addScanLog($v['id'], __METHOD__, 0, 2);
                addlog(["dirmap 扫描目标结果为空", $v['url']]);
                continue;
            }

            $data = [];
            foreach ($scanResult as $item) {
                $data[] = [
                    'app_id' => $v['id'],
                    'user_id' => $v['user_id'],
                    'url' => $item['url'],
                    'code' => $item['code'],
                    'type' => $item['type'],
                    'size' => $item['size'],
                    'create_time' => date('Y-m-d H:i:s'),
                ];
            }
            if ($data) {
                Db::name('scan_dir')->insertAll($data);
            }
            PluginModel::addScanLog($v['id'], __METHOD__, 0, 1);
        }

    }
}
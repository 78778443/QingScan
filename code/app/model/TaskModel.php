<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/8/15
 * Time: 上午10:54
 */


namespace app\model;

use think\facade\Cache;
use think\facade\Db;

class TaskModel extends BaseModel
{

    public static $tableName = "task";


    public static function startTask()
    {
        $where = ['status' => 0];
        $appList = Db::table('task_scan')->where($where)->orderRand()->select()->toArray();
        foreach ($appList as $value) {
            $cmd = "php think scan {$value['tool']} -vvv";
            echo $cmd . PHP_EOL;
            systemLog($cmd);
        }
    }

    public static function autoAddTask()
    {
        //列出每个表对应的扫描工具
        $data = [
            'app' => ['scan_app_web_info', 'scan_app_dir_scan', 'scan_app_gen_vuln', 'scan_app_vul_verify',
                'scan_app_asset_finger', 'scan_app_web_vuln', 'scan_app_crawler', 'scan_app_finger',
                'scan_app_waf', 'scan_url_sql_inject'],
            'code' => ['code_audit'],
            'asm_domain' => ['asm_domain_subdomain'],
            'asm_ip' => ['asm_ip_info', 'asm_ip_port_scan', 'scan_ip_weak_pass'],
            'asm_urls' => ['scan_url_sql_inject'],
        ];

        foreach ($data as $tableName => $toolLists) {
            $targets = Db::table($tableName)->orderRand()->limit(1000)->select()->toArray();
            foreach ($targets as $item) {
                foreach ($toolLists as $tool) {
                    $unKey = "{$tableName}|{$tool}|{$item['id']}";
                    if (Cache::has($unKey)) continue;

                    $version = self::generateTaskByHours($item['create_time'], 15 * 24);
                    $data = ['tool' => $tool, 'target_table' => $tableName, 'target' => $item['id'],
                        'task_version' => $version, 'ext_info' => json_encode($item)];
                    $count = Db::table('task_scan')->extra('IGNORE')->strict(false)->insertGetId($data);
                    if ($count) echo "{$unKey}" . $count . PHP_EOL;
                    Cache::set($unKey, $count, 86400 * 15);
                }
            }
        }
    }

    private static function generateTaskByHours($createTime, $hours)
    {
        $currentTime = time(); // 获取当前时间的时间戳
        $createTimestamp = strtotime($createTime); // 将创建时间转换为时间戳

        // 计算从创建时间到当前时间的小时差
        $hoursSinceCreation = ceil(($currentTime - $createTimestamp) / (60 * 60));

        // 计算最近一个任务的时间戳
        $recentTaskTime = $createTimestamp + (floor($hoursSinceCreation / $hours) * $hours * 60 * 60);

        // 输出最近一个任务
        return date('YmdHi', $recentTaskTime);
    }


    /**
     * @param array $data
     */
    public static function addTask(array $data)
    {
        self::add($data);
    }

    private static function add($data)
    {
        Db::table(self::$tableName)->save($data);
    }
}

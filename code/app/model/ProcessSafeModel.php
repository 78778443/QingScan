<?php
/**
 * Created by PhpStorm.
 * User: xiaotian
 * Date: 2022/05/26
 * Time: 下午15:24
 */

namespace app\model;


use think\facade\Db;
use think\facade\Env;

class ProcessSafeModel extends BaseModel
{
    public static $tableName = "process_safe";


    public static function safe()
    {
        // 死循环不断监听任务是不是挂了
        $timeSleep = 5;
        while (true) {
            $key = Env::get('TASK_SCAN_BLACKLIST_KEY');
            $keyArr = explode(',', $key);
            $keyList = Db::table(self::$tableName)->where(['status' => 1])->select()->toArray();

            $endTime = date('Y-m-d H:i:s', time() - 30);
            $keyList2 = Db::table(self::$tableName)->whereTime('update_time', '>=', $endTime)->select()->toArray();
            $keyList = array_merge($keyList, $keyList2);

            $keyList = array_column($keyList, null, 'key');

            // 遍历需要监控的关键词和对应的脚本
            foreach ($keyList as $key => $info) {
                $value = $info['value'];
                // 执行命令查看任务是否已经执行
                $cmd = "ps -ef | grep '{$key}' | grep -v ' grep'";
                $result = [];
                exec($cmd, $result);
                // 如果返回值长度是0说明任务没有执行
                if (($info['status'] == 1) && count($result) == 0) {
                    // 执行命令
                    systemLog($value);
                    addlog("{$key} 进程已结束，正在重启此进程...");
                } elseif ((($info['status'] == 0) or in_array($key, $keyArr)) && (count($result) > 0)) {
                    //$cmd = "kill -9 $(ps -ef |  grep '{$key}'  | grep -v grep | awk '{print \$2}')";
                    $cmd = "kill -s 9 `ps -aux | grep '{$key}' | awk '{print $2}'`";
                    systemLog($cmd);
                    addlog("已强制终止任务1:{$value}");
                }
            }
            // 删除未启动的工具进程
            $list = Db::table(self::$tableName)->where('status',0)->select()->toArray();
            foreach ($list as $v) {
                // 执行命令查看任务是否已经执行
                $cmd = "ps -ef | grep '{$v['key']}' | grep -v ' grep'";
                $result = [];
                exec($cmd, $result);
                if (count($result) > 0) {
                    //$cmd = "kill -9 $(ps -ef |  grep '{$v['key']}'  | grep -v grep | awk '{print \$2}')";
                    $cmd = "kill -s 9 `ps -aux | grep '{$v['key']}' | awk '{print $2}'`";
                    systemLog($cmd);
                    addlog("已强制终止任务2:{$v['value']}");
                }
            }
            sleep($timeSleep);
        }
    }
}
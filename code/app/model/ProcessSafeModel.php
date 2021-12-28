<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/8/15
 * Time: 上午10:54
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
        $i = true;
        while (true) {
            $key = Env::get('TASK_SCAN_BLACKLIST_KEY');
            $keyArr = explode(',', $key);
            $keyList = Db::table(self::$tableName)->where(['status' => 1])->select()->toArray();

            $endTime = date('Y-m-d H:i:s', time() - 30);
            $keyList2 = Db::table(self::$tableName)->whereTime('update_time', '>=', $endTime)->select()->toArray();
            $keyList = array_merge($keyList, $keyList2);

            $keyList = array_column($keyList, null, 'key');
            $keyList = ($key == false) ? [] : $keyList;

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
                    print_r("{$key} 进程已结束，正在重启此进程...");
                    print_r($value);

                } elseif ((($info['status'] == 0) or in_array($key, $keyArr)) && (count($result) > 0)) {
                    $cmd = "kill -9 $(ps -ef |  grep '{$key}'  | grep -v grep | awk '{print \$2}')";
                    systemLog($cmd);
                    addlog("已强制终止任务:{$value}");
                }
            }
            // 每次循环完毕将休眠2个小时
            sleep($timeSleep);
        }
    }
}
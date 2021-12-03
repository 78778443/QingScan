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
        $key = Env::get('TASK_SCAN_BLACKLIST_KEY');
        $keyArr = explode(',',$key);

        // 死循环不断监听任务是不是挂了
        $timeSleep = 5;
        $i = true;
        while (true) {
            $where = ['status' => 1];
            $keyList = Db::table(self::$tableName)->where($where)->select()->toArray();
            $keyList = array_column($keyList, 'value', 'key');
            $keyList = (Env::get('TASK_SCAN') == false) ? [] : $keyList;
            // 遍历需要监控的关键词和对应的脚本
            foreach ($keyList as $key => $value) {
                if (in_array($key,$keyArr)) {
                    continue;
                }
                // 执行命令查看任务是否已经执行
                $cmd = "ps -ef | grep '{$key}' | grep -v ' grep'";
                $result = [];
                exec($cmd, $result);
                // 如果返回值长度是0说明任务没有执行
                if (count($result) == 0) {
                    // 执行命令
                    exec($value);
                    print_r("{$key} 进场已结束，正在重启此进程...");
                    print_r($value);
                }
            }
            // 每次循环完毕将休眠2个小时
            sleep($timeSleep);
        }
    }
}
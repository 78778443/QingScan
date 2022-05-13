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
                    $cmd = "kill -9 $(ps -ef |  grep '{$v['key']}'  | grep -v grep | awk '{print \$2}')";
                    systemLog($cmd);
                    addlog("已强制终止任务2:{$info['value']}");
                    echo 1;
                }
            }
            // 处理垃圾进程问题


            /*$pid = pcntl_fork();//开启子进程
            if ($pid > 0) {
                echo "我是主进程,我的ppid是" . posix_getppid() . ",我的pid是" . getmypid() . "\n";
                cli_set_process_title('php think scan safe');//修改PHP进程的名字
                pcntl_wait($status);
                echo "我是在后面哦\n";
                sleep(60);
            } else if (0 == $pid) {
                echo "我是子进程,我的ppid是" . posix_getppid() . ",我的pid是" . getmypid() . "\n";
                cli_set_process_title('php son process');//修改PHP进程的名字
                $cpid = pcntl_fork();
                if ($cpid == -1) {
                    die("fork error");
                } else if ($cpid) {
                    //这里是子进程，直接退出
                    exit;
                } else {
                    //这里是孙进程，处理业务逻辑
                    cli_set_process_title('php grandson process');//修改PHP进程的名字
                    for ($i = 0; $i < 5; ++$i) {
                        echo "我是孙子进程,我的ppid是" . posix_getppid() . ",我的pid是" . getmypid() . "\n";
                        sleep(1);
                    }
                }
            } else {//-1 不会创建子进程
                echo "创建进程失败了" . PHP_EOL;
            }*/


            $key1 = '\[php\] <defunct>';
            $cmd = "ps -ef | grep '\[php\] <defunct>' | grep -v ' grep'";
            $result1 = [];
            //exec($cmd, $result1);
            /*$key2 = 'php <defunct>';
            $cmd = "ps -ef | grep '{$key2}' | grep -v ' grep'";
            $result2 = [];
            exec($cmd, $result2);*/
            /*if (count($result1)) {
                $cmd = "kill -9 $(ps -ef |  grep defunct  | more grep -v grep | awk '{print \$2}')";
            ps -ef |  grep '\[php\] <defunct>' | grep -v grep | awk '{print $2}' | xargs kill -9
            ps aux|grep '\[php\] <defunct>'|awk '{print $2}'|xargs kill -9
                systemLog($cmd);
            } elseif(count($result2)){
                $cmd = "kill -9 $(ps -ef |  grep '{$key2}'  | grep -v grep | awk '{print \$2}')";
                systemLog($cmd);
            }*/

            // 每次循环完毕将休眠2个小时
            sleep($timeSleep);
        }
    }
}
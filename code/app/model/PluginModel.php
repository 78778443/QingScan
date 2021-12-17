<?php

namespace app\model;

use think\facade\Db;
use think\facade\Env;

class PluginModel extends BaseModel
{
    public static $tableName = "plugin";

    public static function execPlugin()
    {
        ini_set('max_execution_time', 0);
        while (true) {
            $list = Db::name('app')->where('is_delete', 0)->limit(1)->orderRand()->select()->toArray();
            foreach ($list as $v) {
                $plugin_list = Db::name('plugin')->where('status', 1)->where('is_delete', 0)->select()->toArray();
                foreach ($plugin_list as $val) {
                    $filename = $val['result_file'];
                    @unlink($filename);
                    systemLog($val['cmd']);
                    if (!file_exists($filename)) {
                        addlog(["自定义插件扫描失败，id:{$v['id']}"]);
                        continue;
                    }
                    if ($val['result_type'] == 'json') {
                        $content = file_get_contents($filename);
                    } elseif ($val['result_type'] == 'csv') {
                        $result = readCsv($filename);
                        unset($result[0]);
                        $content = json_encode($result);
                    } else {    // txt格式
                        $content = file_get_contents($filename);
                    }
                    $data = [
                        'app_id' => $v['id'],
                        'plugin_id' => $val['id'],
                        'user_id' => $val['user_id'],
                        'content' => $content,
                        'create_time' => date('Y-m-d H:i:s', time()),
                    ];
                    Db::name('plugin_result')->insert($data);
                    sleep(120);
                }
            }
            sleep(120);
        }
    }

    public static function safe()
    {
        // 死循环不断监听任务是不是挂了
        $timeSleep = 15;
        $i = true;
        while (true) {
            $key = Env::get('TASK_SCAN_BLACKLIST_KEY');
            $keyArr = explode(',', $key);
            $keyList = Db::table(self::$tableName)->where(['status' => 1])->select()->toArray();

            $endTime = date('Y-m-d H:i:s', time() - 30);
            $keyList2 = Db::table(self::$tableName)->whereTime('update_time', '>=', $endTime)->select()->toArray();
            $keyList = array_merge($keyList, $keyList2);

            $keyList = array_column($keyList, null, 'name');
//            $keyList = (Env::get('TASK_SCAN') == false) ? [] : $keyList;
            // 遍历需要监控的关键词和对应的脚本
            foreach ($keyList as $key => $info) {
                $value = $info['cmd'];
                // 执行命令查看任务是否已经执行
                $cmd = "ps -ef | grep 'scan custom {$key}' | grep -v ' grep'";
                $result = [];
                exec($cmd, $result);
                // 如果返回值长度是0说明任务没有执行
                if (($info['status'] == 1) && count($result) == 0) {
                    // 执行命令
                    $cmd = "cd /root/qingscan/code && nohup php think scan custom {$key} > /tmp/{$key}.txt 2>&1 &";
                    systemLog($cmd);
                } elseif ((($info['status'] == 0) or in_array($key, $keyArr)) && (count($result) > 0)) {
                    $cmd = "kill -9 $(ps -ef |  grep 'scan custom {$key}'  | grep -v grep | awk '{print \$2}')";
                    systemLog($cmd);
                    addlog("已强制终止任务:{$value}");
                }
            }
            // 每次循环完毕将休眠2个小时
            sleep($timeSleep);
        }
    }

    public static function custom_event($name)
    {
        $info = Db::table(self::$tableName)->where(['name' => $name])->find();
        if (empty($info)) {
            addlog(["运行的自定义脚本不存在", $name]);
            die;
        }
        ini_set('max_execution_time', 0);
        $toolPath = "/data/tools/{$name}/";
        if (!file_exists($toolPath)) {
            mkdir($toolPath, 0777);
        }
        while (true) {
            $rids = [0];
            $tempRids = Db::table("plugin_result")->where(['plugin_name' => $name])->field('app_id')->select()->toArray();
            $rids = array_merge($rids, array_column($tempRids, 'app_id'));

            $list = Db::name('app')->whereNotIn('id', $rids)->where('is_delete', 0)->limit(1)->orderRand()->select()->toArray();

            foreach ($list as $v) {
                $hash = md5(microtime());
                $filename = "/tmp/{$hash}.txt";
                $cmd = str_replace("##URL##", $v['url'], $info['cmd']);

                @unlink($filename);
                $cmd = "cd $toolPath && nohup {$cmd}  > $filename  2>&1 ";
                systemLog($cmd);
                if (!file_exists($filename)) {
                    addlog(["自定义工具扫描结果文件内容不存在", $info, $v]);
                }
                $content = file_get_contents($filename);
                if (empty($content)) {
                    addlog(["自定义工具扫描结果文件内容为空", $info, $v]);
                }

                $data = ['app_id' => $v['id'], 'user_id' => $v['user_id'], 'content' => $content,
                    'plugin_id' => $info['id'], 'plugin_name' => $info['name']];

                Db::table("plugin_result")->extra('IGNORE')->insert($data);
            }

            addlog("自定义工具{$info['name']},跑完一轮，即将休息 10 秒");
            sleep(10);
        }
    }
}
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
                    Db::name('plugin_scan_log')->insert($data);
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
        $scanType = ['app', 'host', 'code', 'url'];
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
                $cmd = "ps -ef | grep 'scan custom {$key} {$scanType[$info['scan_type']]}' | grep -v ' grep'";
                $result = [];
                exec($cmd, $result);
                // 如果返回值长度是0说明任务没有执行
                if (($info['status'] == 1) && count($result) == 0) {
                    // 执行命令
                    $cmd = "cd /root/qingscan/code && nohup php think scan custom {$key} {$scanType[$info['scan_type']]}> /tmp/{$key}_{$scanType[$info['scan_type']]}.txt 2>&1 &";
                    systemLog($cmd);
                } elseif ((($info['status'] == 0) or in_array($key, $keyArr)) && (count($result) > 0)) {
                    $cmd = "kill -9 $(ps -ef |  grep 'scan custom {$key} {$scanType[$info['scan_type']]}'  | grep -v grep | awk '{print \$2}')";
                    systemLog($cmd);
                    addlog("已强制终止任务:{$value}");
                }
            }
            // 每次循环完毕将休眠2个小时
            sleep($timeSleep);
        }
    }

    /**
     * @param string $name
     * @param int $scanType
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function custom_event(string $name, int $scanType)
    {
        $info = Db::table(self::$tableName)->where(['name' => $name, 'scan_type' => $scanType])->find();
        if (empty($info)) {
            addlog(["运行的自定义脚本不存在", $name, $scanType]);
            die;
        }
        ini_set('max_execution_time', 0);
        while (true) {
            //根据插件名字和扫描类型获取需要扫描的目标
            $list = self::getScanList($name, $scanType);
            foreach ($list as $v) {
                $time = date('YmdHis');
                $filename = "/tmp/{$info['name']}_{$time}.txt";

                //如果是扫描APP和URL
                if ($scanType == 0 or $scanType == 3) {
                    $cmd = str_replace("_####_", $v['url'] ?? '替换URL出错', $info['cmd']);
                    //如果是扫描主机
                } elseif ($scanType == 1) {
                    $cmd = str_replace("_####_", $v['host'] ?? '替换URL出错', $info['cmd']);
                } elseif ($scanType == 2) {
                    //如果是扫描代码
                    $cmd = str_replace("_####_", $v['ssh_url'] ?? '替换URL出错', $info['cmd']);
                }

                @unlink($filename);
                $pathCmd = empty($info['tool_path']) ? '' : "cd {$info['tool_path']} &&";
                $cmd = "{$pathCmd}  {$cmd} ";

                if (strpos($cmd, '.json')) {
                    $result_path = '';
                    if (!file_exists($result_path)) {
                        addlog(["自定义工具扫描失败", $info, $v]);
                    }
                    $content = file_get_contents($result_path);
                    if (!$content) {
                        addlog(["自定义工具扫描结果文件内容为空", $info, $v]);
                    }
                } elseif(strpos($cmd, '.txt')){
                    $file = fopen($result_path, "r");
                    //检测指正是否到达文件的未端
                    $data = [];
                    while (!feof($file)) {
                        $result = fgets($file);
                        if ($result) {
                            $data[] = $result;
                        }
                    }
                    fclose($file);
                    $content = implode("\n", $data);
                } else {
                    $content = systemLog($cmd, false);
                    if (empty($content)) {
                        addlog(["自定义工具扫描结果文件内容为空", $info, $v]);
                    }
                    $content = implode("\n", $content);
                }
                $data = [
                    'app_id' => $v['id'],
                    'user_id' => $v['user_id'] ?? 0,
                    'content' => $content,
                    'scan_type' => $info['scan_type'],
                    'plugin_id' => $info['id'],
                    'plugin_name' => $info['name'],
                    'log_type' => 1,
                ];

                Db::table("plugin_scan_log")->extra('IGNORE')->insert($data);
            }
            sleep(10);
        }
    }

    private static function getScanList(string $name, int $scanType)
    {
        $rids = [0];
        $tempRids = Db::table("plugin_scan_log")->where(['plugin_name' => $name, 'scan_type' => $scanType])->field('app_id')->select()->toArray();
        $rids = array_merge($rids, array_column($tempRids, 'app_id'));
        if ($scanType == 0) {
            $list = Db::name('app')->whereNotIn('id', $rids)->where('is_delete', 0)->limit(1)->orderRand()->select()->toArray();
        } else if ($scanType == 1) {
            $list = Db::name('host')->whereNotIn('id', $rids)->where('is_delete', 0)->limit(1)->orderRand()->select()->toArray();
        } else if ($scanType == 2) {
            $list = Db::name('code')->whereNotIn('id', $rids)->where('is_delete', 0)->limit(1)->orderRand()->select()->toArray();
        } else if ($scanType == 3) {
            $list = Db::name('url')->whereNotIn('id', $rids)->where('is_delete', 0)->limit(1)->orderRand()->select()->toArray();
        }
        return $list;
    }


    /**
     * 添加扫描日志
     * @param int $appId
     * @param string $pluginName
     * @param int $logType
     * @param int $scanType
     * @param array $data
     */
    public static function addScanLog(int $appId, string $pluginName, int $logType, int $scanType = 0, array $data = [])
    {

        $pluginName = explode('::', $pluginName)[1] ?? 'method_error';
        $data['app_id'] = $appId;
        $data['plugin_name'] = $pluginName;
        $data['log_type'] = $logType;
        $data['scan_type'] = $scanType;
        $data['content'] = (isset($data['content']) && !is_string($data['content'])) ? var_export($data['content'], true) : '';
        $data['content'] = substr($data['content'], 0, 4999);

        Db::table('plugin_scan_log')->extra("IGNORE")->insert($data);
    }
}
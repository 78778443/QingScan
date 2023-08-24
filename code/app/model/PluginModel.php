<?php

namespace app\model;

use think\facade\Db;
use think\facade\Env;

class PluginModel extends BaseModel
{
    public static $tableName = "plugin";

    public static function execPlugin()
    {

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

    public static function deleteCodeDir()
    {
        $codeCheck = "./data/codeCheck";
            $resource = opendir($codeCheck);
            while ($file = readdir($resource)) {
                if ($file == '.' || $file == '..') {
                    continue;
                }
                $dirName = "{$codeCheck}/{$file}";
                //如果目录是fortify正在扫描的目录，先不管它了
                if ($dirName == self::getFortifyScanDir()) {
                    continue;
                }
                //2. 获取文件的创建时间
                $ctime = filectime($dirName);

                //获取磁盘空间,根据剩余空间，决定代码保存时间
                $cmd = "df -h | grep 'G' | head -n 1 | awk '{print $4}'";
                $size = intval(exec($cmd));
                //如果空间大于200G,那么保留72小时,否则48小时
                $hour = ($size > 200) ? 72 : 48;
                //如果空间小于60G,那么保留24小时
                $hour = ($size < 60) ? 24 : $hour;
                //如果空间小于20G,那么保留2小时
                $hour = ($size < 20) ? 2 : $hour;

//                echo "空间剩余 {$size} G 保留时间为 {$hour} 小时" . PHP_EOL;
                //3. 如果时间超过24小时
                if ($ctime < (time() - $hour * 3600)) {
                    //4. 删除此文件
                    echo "正在删除过期的代码文件夹 {$dirName}";
                    $cmd = "rm -rf $dirName";
                    systemLog($cmd);
                }
            }

    }

    public static function getFortifyScanDir()
    {
        $cmd = "ps -ef | grep -v def  | awk '{print $22}' | grep './extend/codeCheck'";
        $str = exec($cmd);
        return $str;
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

                if ($info['result_type'] == 'json') {
                    $result_path = '';
                    if (!file_exists($result_path)) {
                        addlog(["自定义工具扫描失败", $info, $v]);
                    }
                    $content = json_decode(file_get_contents($result_path), true);
                    if (!$content) {
                        addlog(["自定义工具扫描结果文件内容为空", $info, $v]);
                    }
                } elseif (strpos($cmd, '.txt')) {
                    $result_path = '';
                    $file = fopen($result_path, "r");
                    //检测指正是否到达文件的未端
                    $data = [];
                    while (!feof($file)) {
                        $result = fgets($file);
                        if ($result) {
                            $data[] = $result;
                        }
                    }
                    @unlink($result_path);
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
                    'is_custom' => 2,
                    'check_status' => 0,
                    'create_time' => date('Y-m-d H:i:s', time())
                ];

                Db::table("plugin_scan_log")->extra('IGNORE')->insert($data);
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
     * @param int $scanType 类型 0app 1host 2code  3url
     * @param int $logType  进度 0开始扫描   1完成   2失败
     * @param int $isCustom 是否为自定义插件  1否   2是
     * @param array $data
     */
    public static function addScanLog(int $appId, string $pluginName, int $scanType, int $logType = 0, $isCustom=1,array $data = [])
    {
        $pluginName = explode('::', $pluginName)[1] ?? 'method_error';
        $data['app_id'] = $appId;
        $data['plugin_name'] = $pluginName;
        $data['scan_type'] = $scanType;
        $data['log_type'] = $logType;
        $data['is_custom'] = $isCustom;
        $data['content'] = (isset($data['content']) && !is_string($data['content'])) ? var_export($data['content'], true) : '';
        $data['content'] = substr($data['content'], 0, 4999);
        Db::table('plugin_scan_log')->extra("IGNORE")->insert($data);
    }
}
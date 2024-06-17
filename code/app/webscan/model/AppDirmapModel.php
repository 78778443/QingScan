<?php


namespace app\webscan\model;


use app\model\BaseModel;
use app\model\PluginModel;
use think\facade\Db;

class AppDirmapModel extends BaseModel
{
    public static function dirmapScan()
    {
        $file_path = './extend/tools/dirmap/';

        $where = ['tool' => 'scan_app_dirmap', 'status' => 0];
        $list = Db::table('task_scan')->where($where)->limit(10)->select()->toArray();
        foreach ($list as $task) {
            Db::table('task_scan')->where(['id' => $task['id']])->update(['status' => 1]);
            $v = json_decode($task['ext_info'], true);

            if (!self::checkToolAuth(1, $v['id'], 'dirmap')) {
                continue;
            }

            PluginModel::addScanLog($v['id'], __METHOD__, 0);


            $cmd = "cd {$file_path}  && python3 ./dirmap.py -i {$v['url']} -lcf";
            systemLog($cmd);
            $host = parse_url($v['url'])['host'];
            $port = parse_url($v['url'])['port'] ?? null;
            $port = $port ? "_{$port}" : "";
            $filename = $file_path . "output/{$host}{$port}.txt";
            if (!file_exists($filename)) {
                PluginModel::addScanLog($v['id'], __METHOD__, 0, 2);
                addlog(["dirmap扫描结果文件不存在:{$filename}", $v]);
                continue;
            }
            //打开一个文件
            $file = fopen($filename, "r");
            //检测指正是否到达文件的未端
            $data = [];
            while (!feof($file)) {
                $result = fgets($file);
                if (empty($result)) {
                    PluginModel::addScanLog($v['id'], __METHOD__, 0, 1);
                    addlog(["dirmap 扫描目标结果为空", $v['url']]);
                    continue;
                }
                $arr = explode('http', $result);
                $regex = "/(?:\[)(.*?)(?:\])/i";
                preg_match_all($regex, trim($arr[0]), $acontent);
                $data[] = [
                    'url' => trim('http' . $arr[1]),
                    'code' => isset($acontent[1][0]) ? $acontent[1][0] : '',
                    'type' => isset($acontent[1][1]) ? $acontent[1][1] : '',
                    'size' => isset($acontent[1][2]) ? $acontent[1][2] : '',
                    'app_id' => $v['id'],
                    'user_id' => $v['user_id'],
                ];

            }
            //关闭被打开的文件
            fclose($file);
            if ($data) {
                Db::name('app_dirmap')->insertAll($data);
            }
            @unlink($filename);
            PluginModel::addScanLog($v['id'], __METHOD__, 0, 1);
        }

    }
}
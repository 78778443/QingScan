<?php


namespace app\model;


use think\facade\App;
use think\facade\Db;

class AppDirmapModel extends BaseModel
{
    public static function dirmapScan()
    {
        ini_set('max_execution_time', 0);
        while (true) {
            $list = Db::name('app')->whereTime('dirmap_scan_time', '<=', date('Y-m-d H:i:s', time() - (86400 * 15)))->where('is_delete', 0)->orderRand()->limit(1)->field('id,url,user_id')->select();
            $file_path = '/data/tools/dirmap/';
            foreach ($list as $k => $v) {
                self::scanTime('app', $v['id'], 'dirmap_scan_time');

                $cmd = "cd {$file_path}  && python3 ./dirmap.py -i {$v['url']} -lcf";
                systemLog($cmd);
                $host = parse_url($v['url'])['host'];
                $port = parse_url($v['url'])['port'] ?? null;
                $port = $port ? "_{$port}" : "";
                $filename = $file_path . "output/{$host}{$port}.txt";
                if (!file_exists($filename)) {
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
            }
            sleep(10);
        }
    }
}
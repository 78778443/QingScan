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
            //$file_path = App::getRuntimePath().'/tools/dirmap/';
            //$url = 'https://ss-stage-stargate.kingsgroupgames.com/';
            foreach ($list as $k => $v) {
                //$v['url'] = $url;
                $cmd = "cd {$file_path}  && python3 ./dirmap.py -i {$v['url']} -lcf";
                systemLog($cmd);
                $host = parse_url($v['url'])['host'];
                $filename = $file_path . "output/{$host}.txt";
                if (file_exists($filename)) {
                    //打开一个文件
                    $file = fopen($filename, "r");
                    //检测指正是否到达文件的未端
                    $data = [];
                    while (!feof($file)) {
                        $result = fgets($file);
                        if (!empty($result)) {
                            $arr = explode('http', $result);
                            $regex = "/(?:\[)(.*?)(?:\])/i";
                            preg_match_all($regex, trim($arr[0]), $acontent);
                            $data[] = [
                                'url' => trim('http'.$arr[1]),
                                'code' => isset($acontent[1][0])?$acontent[1][0]:'',
                                'type' => isset($acontent[1][1]) ?$acontent[1][1]: '',
                                'size' => isset($acontent[1][2]) ?$acontent[1][2]: '',
                                'app_id'=>$v['id'],
                                'user_id'=>$v['user_id'],
                            ];
                        }
                    }
                    //关闭被打开的文件
                    fclose($file);
                    if ($data) {
                        Db::name('app_dirmap')->insertAll($data);
                    }
                    @unlink($filename);
                    Db::name('app')->where('id',$v['id'])->update(['dirmap_scan_time'=>date('Y-m-d H:i:s',time())]);
                } else {
                    addlog("项目文件内容为空:{$filename}");
                    self::scanTime('app',$v['id'],'dirmap_scan_time');
                }
            }
            sleep(10);
        }
    }
}
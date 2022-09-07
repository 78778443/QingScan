<?php


namespace app\model;


use think\facade\App;
use think\facade\Db;

class OneForAllModel extends BaseModel
{
    // OneForAll子域名扫描
    public static function subdomainScan()
    {
        ini_set('max_execution_time', 0);
        $tools = '/data/tools/OneForAll';
        while (true) {
            processSleep(1);
            $app_list = self::getAppStayScanList('subdomain_scan_time');
            foreach ($app_list as $k => $v) {
                if (!self::checkToolAuth(1,$v['id'],'subdomain')) {
                    continue;
                }

                PluginModel::addScanLog($v['id'], __METHOD__, 0);
                self::scanTime('app',$v['id'],'subdomain_scan_time');

                $host = parse_url($v['url'])['host'];
                if (filter_var($host, FILTER_VALIDATE_IP)) {
                    PluginModel::addScanLog($v['id'], __METHOD__, 0,2,1,["content"=>"项目不是域名:{$v['url']}"]);
                    addlog(["此地址不是域名:{$v['url']}"]);
                    continue;
                }
                $host_arr = explode('.',$host);
                if (strstr($host, 'www.')) {
                    unset($host_arr[0]);
                }
                $domain = implode('.',$host_arr);
                $file_path = $tools.'/results/';
                $cmd = "cd {$tools}  && python3 ./oneforall.py --target {$host} --path {$file_path} run";
                systemLog($cmd);
                $filename = $file_path.$domain.'.csv';
                if (!file_exists($filename)) {
                    PluginModel::addScanLog($v['id'], __METHOD__,0, 2);
                    addlog(["OneForAll子域名扫描结果，文件不存在:{$filename}"]);
                    continue;
                }
                $list = assoc_getcsv($filename);
                if (!empty($list)) {
                    $data = [];
                    foreach ($list as $key=>$val) {
                        unset($val['id']);
                        $val['app_id'] = $v['id'];
                        $val['user_id'] = $v['user_id'];
                        $data[] = $val;
                    }
                    if ($data) {
                        Db::name('one_for_all')->insertAll($data);
                    }
                    addlog(["OneForAll子域名扫描数据写入成功:".json_encode($data)]);
                    @unlink($filename);
                } else {
                    addlog(["OneForAll子域名扫描,内容获取失败:{$filename}"]);
                }
                PluginModel::addScanLog($v['id'], __METHOD__, 0,1);
            }
            sleep(30);
        }
    }

}
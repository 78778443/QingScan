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
        while (true) {
            $app_list = Db::name('app')->whereTime('subdomain_scan_time', '<=', date('Y-m-d H:i:s', time() - (86400 * 15)))->where('is_delete',0)->field('id,url')->orderRand()->limit(1)->select()->toArray();
            $tools = '/data/tools/OneForAll';
            foreach ($app_list as $k => $v) {
                //AppModel::updateScanTime($v['id'],'subdomain_scan_time');

                $host = parse_url($v['url'])['host'];
                $host_arr = explode('.',$host);
                unset($host_arr[0]);
                $domain = implode('.',$host_arr);
                $file_path = $tools.'/results/';
                $cmd = "cd {$tools}  && python3 ./oneforall.py --target {$host} --path {$file_path} run";
                systemLog($cmd);
                $filename = $file_path.$domain.'.csv';
                if (!file_exists($filename)) {
                    addlog(["文件不存在:{$filename}"]);
                    AppModel::updateScanTime($v['id'],'subdomain_scan_time');
                    continue;
                }
                $list = assoc_getcsv($filename);
                @unlink($filename);
                if (!empty($list)) {
                    $data = [];
                    Db::name('app')->where('id',$v['id'])->update(['subdomain_scan_time'=>date('Y-m-d H:i:s',time())]);
                    foreach ($list as $key=>$val) {
                        unset($val['id']);
                        $val['app_id'] = $v['id'];
                        $data[] = $val;
                    }
                    if ($data) {
                        Db::name('one_for_all')->insertAll($data);
                    }
                } else {
                    addlog(["内容获取失败:{$filename}"]);
                }
            }
            sleep(10);
        }
    }

}
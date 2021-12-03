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
            $file_path = '/data/tools/OneForAll/';
            @mkdir($file_path, 0777, true);
            foreach ($app_list as $k => $v) {
                $domain = parse_url($v['url'])['host'];
                $cmd = "cd {$file_path}  && python3 ./oneforall.py --target {$domain} --path {$file_path} run";
//            echo $cmd  . PHP_EOL;die;
                systemLog($cmd);
                $filename = $file_path.$domain;
                if (file_exists($filename)) {
                    $list = assoc_getcsv($filename);
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
                        AppModel::updateScanTime($v['id'],'subdomain_scan_time');
                    }
                    @unlink($filename);
                } else {
                    addlog(["文件不存在:{$filename}"]);
                    AppModel::updateScanTime($v['id'],'subdomain_scan_time');
                }
            }
            sleep(10);
        }
    }

}
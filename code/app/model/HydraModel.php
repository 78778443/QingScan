<?php


namespace app\model;


use think\facade\App;
use think\facade\Db;

class HydraModel extends BaseModel
{
    public static function sshScan(){
        ini_set('max_execution_time', 0);
        while (true) {
            $app_list = Db::name('host')->whereTime('hydra_scan_time', '<=', date('Y-m-d H:i:s', time() - (86400 * 15)))->limit(1)->orderRand()->field('id,host,app_id')->select()->toArray();
            $file_path = '/data/tools/hydra/';
            $hydra = config('tools.hydra');
            $filename = "{$file_path}hydra.txt";
            foreach ($app_list as $k => $v) {
                systemLog("hydra -l root -P {$hydra['password']} -b json -o {$file_path}hydra.txt -e ns {$v['host']} ssh -V");
                if (file_exists($filename)) {
                    $result = json_decode(file_get_contents($filename),true);
                    if ($result['results']) {
                        Db::name('host')->where('id',$v['id'])->update(['hydra_scan_time'=>date('Y-m-d H:i:s',time())]);
                        $dataAll = [];
                        foreach ($result['results'] as $kk=>$vv) {
                            $data['username'] = $vv['login'];
                            $data['password'] = $vv['password'];
                            $data['host_id'] = $v['id'];
                            $data['app_id'] = $v['app_id'];
                            $data['create_time'] = date('Y-m-d H:i:s',time());
                            $dataAll[] = $data;
                        }
                        Db::name('host_hydra_scan_details')->insertAll($dataAll);
                    } else {
                        addlog(["文件内容格式错误：{$filename}"]);
                        self::scanTime('host',$v['id'],'hydra_scan_time');
                    }
                    @unlink($filename);
                } else {
                    addlog(["文件内容获取失败：{$filename}"]);
                    self::scanTime('host',$v['id'],'hydra_scan_time');
                }
            }
            sleep(10);
        }
    }
}
<?php

namespace app\model;

use think\facade\App;
use think\facade\Db;

class MobsfscanModel extends BaseModel
{
    public static function mobsfscan(){
        ini_set('max_execution_time', 0);
        while (true) {
            processSleep(1);

            $list = Db::table('code')->whereTime('mobsfscan_scan_time', '<=', date('Y-m-d H:i:s', time() - (86400 * 15)))->where('is_delete', 0)->limit(1)->orderRand()->select()->toArray();
            foreach ($list as $k=>$v) {
                $filename = App::getRuntimePath().'tools/mobsfscan/'.$v['name'].'.json';

                PluginModel::addScanLog($v['id'], __METHOD__, 0);
                self::scanTime('code', $v['id'], 'mobsfscan_scan_time');

                $codePath = "/data/codeCheck/".$v['name'];
                $cmd = "mobsfscan {$codePath} --json -o {$filename}";
                systemLog($cmd);
                if (!file_exists($filename)) {
                    PluginModel::addScanLog($v['id'], __METHOD__, 2);
                    addlog(["mobsfscan扫描失败，name:{$v['name']}"]);
                    continue;
                }
                $results = json_decode(file_get_contents($filename), true)['results'];
                $num = 0;
                $data = [];
                foreach ($results as $key=>$val) {
                    $data[$num] = [
                        'code_id'=>$v['id'],
                        'user_id'=>$v['user_id'],
                        'type'=>$key,
                        'files'=>isset($val['files'])?json_encode($val['files']):'',
                        'cwe'=>$val['metadata']['cwe']??'',
                        'description'=>$val['metadata']['description']??'',
                        'input_case'=>$val['metadata']['input_case']??'',
                        'masvs'=>$val['metadata']['masvs']??'',
                        'owasp_mobile'=>$val['metadata']['owasp_mobile']??'',
                        'reference'=>$val['metadata']['reference']??'',
                        'severity'=>$val['metadata']['severity']??'',
                        'create_time'=>date('Y-m-d H:i:s',time())
                    ];
                    $num++;
                }
                Db::name('mobsfscan')->insertAll($data);
                PluginModel::addScanLog($v['id'], __METHOD__, 1,1);
                addlog(["mobsfscan扫描数据写入成功:" . json_encode($data)]);
            }
            sleep(10);
        }
    }
}
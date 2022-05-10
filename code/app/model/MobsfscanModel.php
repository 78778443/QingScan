<?php

namespace app\model;

use think\facade\App;
use think\facade\Db;

class MobsfscanModel extends BaseModel
{
    public static function mobsfscan(){
        $filename = App::getRuntimePath().'tools/mobsfscan/';
        if (!is_dir($filename)) {
            mkdir($filename, 0777, true);
        }
        ini_set('max_execution_time', 0);
        while (true) {
            processSleep(1);
            $endTime = date('Y-m-d', time() - 86400 * 15);
            $list = Db::table('code')->whereTime('mobsfscan_scan_time', '<=', $endTime)->where('is_delete', 0)->limit(1)->orderRand()->select()->toArray();
            $count = Db::table('code')->whereTime('mobsfscan_scan_time', '<=', $endTime)->count('id');
            print("开始执行mobsfscan扫描代码任务,{$count} 个项目等待扫描..." . PHP_EOL);
            foreach ($list as $k=>$v) {
                $v['name'] = cleanString($v['name']);

                $filename .= $v['name'].'.json';
                PluginModel::addScanLog($v['id'], __METHOD__, 0,2);

                $codePath = "/data/codeCheck/".$v['name'];
                $cmd = "mobsfscan {$codePath} --json -o {$filename}";
                systemLog($cmd);
                if (!file_exists($filename)) {
                    PluginModel::addScanLog($v['id'], __METHOD__, 2,2);
                    addlog(["mobsfscan扫描失败，项目名称：{$v['name']}"]);
                    continue;
                }
                $results = json_decode(file_get_contents($filename), true)['results'];
                if (!$results) {
                    addlog(["mobsfscan扫描成功，未找到漏洞；项目名称：{$v['name']}"]);
                    self::scanTime('code', $v['id'], 'mobsfscan_scan_time');
                    continue;
                }
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
                PluginModel::addScanLog($v['id'], __METHOD__, 1,2);
                addlog(["mobsfscan扫描数据写入成功:" . json_encode($data)]);
                self::scanTime('code', $v['id'], 'mobsfscan_scan_time');
                @unlink($filename);
            }
            sleep(10);
        }
    }
}
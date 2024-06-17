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

            $endTime = date('Y-m-d', time() - 86400 * 15);
            $where[] = ['is_delete','=',0];
            $where[] = ['project_type','in',[5,6]];
            $list = Db::table('code')->whereTime('mobsfscan_scan_time', '<=', $endTime)->where($where)->limit(1)->orderRand()->select()->toArray();
            $count = Db::table('code')->whereTime('mobsfscan_scan_time', '<=', $endTime)->where($where)->count('id');
            print("开始执行mobsfscan扫描代码任务,{$count} 个项目等待扫描..." . PHP_EOL);
            foreach ($list as $k=>$v) {
                $v['name'] = cleanString($v['name']);
                PluginModel::addScanLog($v['id'], __METHOD__, 2);
                

                $filename .= $v['name'].'.json';

                $codePath = "./data/codeCheck/".$v['name'];
                $cmd = "mobsfscan {$codePath} --json -o {$filename}";
                systemLog($cmd);
                if (!file_exists($filename)) {
                    PluginModel::addScanLog($v['id'], __METHOD__, 2,2);
                    addlog(["mobsfscan扫描失败，项目名称：{$v['name']}"]);
                    continue;
                }
                $results = json_decode(file_get_contents($filename), true)['results'];
                if (!$results) {
                    PluginModel::addScanLog($v['id'], __METHOD__, 2,1);
                    addlog(["mobsfscan扫描成功，未找到漏洞；项目名称：{$v['name']}"]);
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
                PluginModel::addScanLog($v['id'], __METHOD__, 2,1);
                addlog(["mobsfscan扫描数据写入成功:" . json_encode($data)]);
                @unlink($filename);
            }

    }
}
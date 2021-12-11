<?php

namespace app\model;

use think\facade\Db;

class CodeWebshellModel extends BaseModel
{
    public static function code_webshell_scan(){
        ini_set('max_execution_time', 0);
        $tools = '/data/tools/hm-linux-amd64/';
        while (true) {
            $list = Db::name('code')->whereTime('webshell_scan_time', '<=', date('Y-m-d H:i:s', time() - (86400)))
                ->where('is_delete', 0)->limit(10)->orderRand()->select()->toArray();
            //$list = Db::name('code')->where('id',1517)->select();
            foreach ($list as $v) {
                $codePath = "/data/codeCheck/";
                $filepath = $codePath.cleanString($v['name']);
                if (!file_exists($filepath)) {
                    addlog(["项目目录不存在:{$codePath}"]);
                    continue;
                }
                $filename = $tools.'/result.csv';
                @unlink($filename);
                $cmd = "cd {$tools} && ./hm scan {$filepath}";
                systemLog($cmd);
                if (file_exists($filename)) {
                    $result = readCsv($filename);
                    unset($result[0]);
                    foreach ($result as $val) {
                        $data = [
                            'code_id'=>$v['id'],
                            'create_time'=>date('Y-m-d H:i:s',time()),
                            'user_id'=>$v['user_id'],
                            'type'=>$val[1],
                            'filename'=>$val[2],
                        ];
                        Db::name('code_webshell')->insert($data);
                    }
                    self::scanTime('code',$v['id'],'webshell_scan_time');
                }
            }
            sleep(120);
        }
    }
}
<?php

namespace app\code\model;

use app\model\BaseModel;
use app\model\PluginModel;
use think\facade\Db;

class CodeWebshellModel extends BaseModel
{
    public static function code_webshell_scan()
    {
        $tools = './extend/tools/hm-linux-amd64/';
            $list = self::getCodeStayScanList('webshell_scan_time');
            foreach ($list as $v) {
                if (!self::checkToolAuth(2,$v['id'],'webshell')) {
                    continue;
                }

                
                PluginModel::addScanLog($v['id'], __METHOD__, 2);
                $codePath = "./data/codeCheck/";
                $value = $v;
                $prName = cleanString($value['name']);
                $codeUrl = $value['ssh_url'];
                downCode($codePath, $prName, $codeUrl, $value['is_private'], $value['username'], $value['password'], $value['private_key']);
                $filepath = "./data/codeCheck/{$prName}";

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
                }
                PluginModel::addScanLog($v['id'], __METHOD__, 1, 2);
            }

    }
}
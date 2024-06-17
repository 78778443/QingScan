<?php

namespace app\code\model;

use app\model\BaseModel;
use app\model\ConfigModel;
use app\model\PluginModel;
use think\facade\App;
use think\facade\Db;

class MurphysecModel extends BaseModel
{
    public static function murphysec_scan()
    {

        $tools = './extend/tools/amd64/murphysec';

        chmod('./extend/tools/amd64/murphysec', 0777);
        $filename = App::getRuntimePath() . 'tools/murphysec/';
        if (!is_dir($filename)) {
            mkdir($filename, 0777, true);
        }

        $where = ['tool' => 'code_murphysec', 'status' => 0];
        $list = Db::table('task_scan')->where($where)->limit(10)->select()->toArray();
        foreach ($list as $task) {
            Db::table('task_scan')->where(['id' => $task['id']])->update(['status' => 1]);
            $val = json_decode($task['ext_info'], true);

            if (!self::checkToolAuth(2, $val['id'], 'murphysec')) {
                continue;
            }
            $murphysec_token = ConfigModel::value('murphysec_token');
            if (!$murphysec_token) {
                addlog(["murphysec扫描失败，请先配置墨菲安全平台token"]);
                exit;
            }
            PluginModel::addScanLog($val['id'], __METHOD__, 2);


            $prName = cleanString($val['name']);

            $filepath = "./data/codeCheck/{$prName}";
            $cmd = "{$tools} scan --json --token={$murphysec_token} {$filepath}";

            $result = [];
            execLog($cmd, $result);
            file_put_contents($filename . $prName . '.json', $result);
            if (!$result) {
                PluginModel::addScanLog($val['id'], __METHOD__, 2, 2);
                addlog(["murphysec扫描失败，项目名称：{$val['name']}"]);
                continue;
            }
            $result = json_decode(file_get_contents($filename . $prName . '.json'), true);
            if (!isset($result['comps'])) {
                PluginModel::addScanLog($val['id'], __METHOD__, 2, 1);
                addlog(["murphysec扫描成功，未找到漏洞；项目名称：{$val['name']}"]);
                continue;
            }
            $list = $result['comps'];
            $data = [];
            foreach ($list as $v) {
                $data[] = [
                    'user_id' => $val['user_id'],
                    'code_id' => $val['id'],
                    'comp_name' => $v['comp_name'],
                    'min_fixed_version' => $v['min_fixed_version'],
                    'version' => $v['version'],
                    'show_level' => $v['show_level'],
                    'language' => $v['language'],
                    'solutions' => isset($v['solutions']) ? json_encode($v['solutions']) : '',
                    'create_time' => date('Y-m-d H:i:s', time()),
                ];
            }
            Db::name('murphysec')->insertAll($data);
            PluginModel::addScanLog($val['id'], __METHOD__, 2, 1);
            addlog(["murphysec扫描数据写入成功:" . json_encode($data)]);
        }

    }
}
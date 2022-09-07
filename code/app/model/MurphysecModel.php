<?php

namespace app\model;

use think\facade\App;
use think\facade\Db;

class MurphysecModel extends BaseModel
{
    public static function murphysec_scan()
    {
        ini_set('max_execution_time', 0);
        $tools = '/data/tools/amd64/murphysec';

        chmod('/data/tools/amd64/murphysec', 0777);
        $filename = App::getRuntimePath().'tools/murphysec/';
        if (!is_dir($filename)) {
            mkdir($filename, 0777, true);
        }
        while (true) {
            processSleep(1);
            $where[] = ['project_type', 'in', [1, 2, 3, 4, 6]];
            $list = self::getCodeStayScanList('murphysec_scan_time');
            foreach ($list as $k => $val) {
                if (!self::checkToolAuth(2,$val['id'],'murphysec')) {
                    continue;
                }
                $murphysec_token = ConfigModel::value('murphysec_token');
                if (!$murphysec_token) {
                    addlog(["murphysec扫描失败，请先配置墨菲安全平台token"]);
                    exit;
                }
                PluginModel::addScanLog($val['id'], __METHOD__, 2);
                self::scanTime('code', $val['id'], 'murphysec_scan_time');

                $prName = cleanString($val['name']);

                $filepath = "/data/codeCheck/{$prName}";
                $cmd = "{$tools} scan --json --token={$murphysec_token} {$filepath}";

                $result = [];
                execLog($cmd, $result);
                file_put_contents($filename.$prName.'.json',$result);
                if (!$result) {
                    PluginModel::addScanLog($val['id'], __METHOD__, 2,2);
                    addlog(["murphysec扫描失败，项目名称：{$val['name']}"]);
                    continue;
                }
                $result = json_decode(file_get_contents($filename.$prName.'.json'), true);
                if (!isset($result['comps'])) {
                    PluginModel::addScanLog($val['id'], __METHOD__, 2,1);
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
            sleep(30);
        }
    }
}
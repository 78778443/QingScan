<?php


namespace app\model;


use think\facade\App;
use think\facade\Db;

class HydraModel extends BaseModel
{
    public static function sshScan()
    {

        $file_path = './extend/tools/hydra/';
        $hydra = config('tools.hydra');
        $filename = "{$file_path}hydra.txt";
        $where = ['tool' => 'scan_ip_hydra', 'status' => 0];
        $list = Db::table('task_scan')->where($where)->limit(10)->select()->toArray();
        foreach ($list as $task) {
            Db::table('task_scan')->where(['id' => $task['id']])->update(['status' => 1]);
            $v = json_decode($task['ext_info'], true);


            PluginModel::addScanLog($v['id'], __METHOD__, 1);


            systemLog("hydra -L {$hydra['username']} -P {$hydra['password']} -b json -o {$file_path}hydra.txt -e ns {$v['ip']} ssh -V");
            if (file_exists($filename)) {
                $result = json_decode(file_get_contents($filename), true);
                if ($result['results']) {
                    $dataAll = [];
                    foreach ($result['results'] as $kk => $vv) {
                        $data['username'] = $vv['login'];
                        $data['password'] = $vv['password'];
                        $data['host_id'] = $v['id'];
                        $data['app_id'] = $v['app_id'];
                        $data['user_id'] = $v['user_id'];
                        $data['create_time'] = date('Y-m-d H:i:s', time());
                        $dataAll[] = $data;
                    }
                    Db::name('host_hydra_scan_details')->insertAll($dataAll);
                } else {
                    PluginModel::addScanLog($v['id'], __METHOD__, 1, 2);
                    addlog(["hydra文件内容格式错误：{$filename}"]);
                }
                @unlink($filename);
            } else {
                PluginModel::addScanLog($v['id'], __METHOD__, 1, 2);
                addlog(["hydra文件内容获取失败：{$filename}"]);
            }
            PluginModel::addScanLog($v['id'], __METHOD__, 1, 1);
        }

    }
}
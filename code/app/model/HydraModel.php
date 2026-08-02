<?php


namespace app\model;


use app\scan\BruteScan;
use think\facade\Db;

class HydraModel extends BaseModel
{
    public static function sshScan()
    {

        // 内置弱口令爆破引擎支持的端口（ssh 22 暂不支持）
        $supportPorts = [21, 23, 3306, 6379];
        $defaultPorts = [21, 23, 6379, 3306];

        $where = ['tool' => 'scan_ip_hydra', 'status' => 0];
        $list = Db::table('task_scan')->where($where)->limit(10)->select()->toArray();
        foreach ($list as $task) {
            Db::table('task_scan')->where(['id' => $task['id']])->update(['status' => 1]);
            $v = json_decode($task['ext_info'], true);
            if (!is_array($v)) {
                $v = [];
            }
            $hostId = $v['id'] ?? 0;
            $appId = $v['app_id'] ?? 0;

            // 项目工具授权检查（仅在有项目归属时校验，不阻塞 asm_ip 全量任务）
            if (!empty($appId) && !self::checkToolAuth(1, $appId, 'hydra')) {
                PluginModel::addScanLog($hostId, __METHOD__, 1, 2);
                addlog(["项目 {$appId} 未授权 hydra 工具，跳过任务 task_id:{$task['id']}"]);
                continue;
            }

            PluginModel::addScanLog($hostId, __METHOD__, 1, 1);

            // 目标 IP 从任务 ext_info 中读取
            $host = $v['ip'] ?? $v['host'] ?? '';
            if ($host === '') {
                PluginModel::addScanLog($hostId, __METHOD__, 1, 2);
                addlog(["弱口令爆破任务缺少 IP，跳过 task_id:{$task['id']}"]);
                continue;
            }

            // 端口集合：优先合并端口扫描记录，否则使用默认端口
            $ports = $defaultPorts;
            $dbPorts = [];
            try {
                $list1 = Db::table('asm_host_port')->where('host', $host)->where('is_delete', 0)->field('port')->select()->toArray();
                foreach ($list1 as $row) {
                    $dbPorts[] = (int)$row['port'];
                }
                $list2 = Db::table('asm_ip_port')->where('ip', $host)->field('port')->select()->toArray();
                foreach ($list2 as $row) {
                    $dbPorts[] = (int)$row['port'];
                }
            } catch (\Throwable $e) {
                addlog(["读取端口扫描记录失败：{$e->getMessage()}"]);
            }
            if ($dbPorts) {
                $ports = array_values(array_unique(array_merge($dbPorts, $ports)));
            }

            // 任务目标仅有 SSH(22) 端口时，内置引擎暂不支持，跳过该任务
            $targetPorts = array_values(array_intersect($ports, $supportPorts));
            if (in_array(22, $ports) && empty($targetPorts)) {
                PluginModel::addScanLog($hostId, __METHOD__, 1, 2);
                addlog(["主机 {$host} 任务目标为 SSH(22) 端口，内置引擎暂不支持 SSH 爆破，跳过该任务 task_id:{$task['id']}"]);
                continue;
            }
            if (in_array(22, $ports)) {
                addlog(["主机 {$host} 开放 SSH(22) 端口，内置引擎暂不支持 SSH 爆破，已跳过 SSH 端口"]);
            }
            if (empty($targetPorts)) {
                PluginModel::addScanLog($hostId, __METHOD__, 1, 2);
                addlog(["主机 {$host} 无支持的弱口令爆破端口，跳过该任务 task_id:{$task['id']}"]);
                continue;
            }

            // 使用内置 PHP 引擎爆破
            $scanResult = BruteScan::scan($host, $targetPorts);
            if ($scanResult) {
                $dataAll = [];
                foreach ($scanResult as $item) {
                    $data['host_id'] = $hostId;
                    $data['type'] = $item['service'];
                    $data['username'] = $item['username'];
                    $data['password'] = $item['password'];
                    $data['create_time'] = date('Y-m-d H:i:s', time());
                    $data['app_id'] = $appId;
                    $data['user_id'] = $v['user_id'] ?? 0;
                    $dataAll[] = $data;
                }
                Db::name('host_hydra_scan_details')->insertAll($dataAll);
            }
            PluginModel::addScanLog($hostId, __METHOD__, 1, 1);
        }

    }
}

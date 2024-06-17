<?php


namespace app\model;


use think\facade\App;
use think\facade\Db;

class OneForAllModel extends BaseModel
{
    // OneForAll子域名扫描
    public static function subdomainScan()
    {
        $tools = './extend/tools/OneForAll';
        $where = ['tool' => 'asm_domain_oneforall', 'status' => 0];
        $list = Db::table('task_scan')->where($where)->limit(10)->select()->toArray();
        foreach ($list as $task) {
            Db::table('task_scan')->where(['id' => $task['id']])->update(['status' => 1]);
            $v = json_decode($task['ext_info'], true);
            if (!self::checkToolAuth(1, $v['id'], 'subdomain')) {
                continue;
            }

            PluginModel::addScanLog($v['id'], __METHOD__, 0);


            $host = parse_url($v['url'])['host'];
            if (filter_var($host, FILTER_VALIDATE_IP)) {
                PluginModel::addScanLog($v['id'], __METHOD__, 0, 2, 1, ["content" => "项目不是域名:{$v['url']}"]);
                addlog(["此地址不是域名:{$v['url']}"]);
                continue;
            }
            $host_arr = explode('.', $host);
            if (strstr($host, 'www.')) {
                unset($host_arr[0]);
            }
            $domain = implode('.', $host_arr);
            $file_path = $tools . '/results/';
            $cmd = "cd {$tools}  && python3 ./oneforall.py --target {$host} --path {$file_path} run";
            systemLog($cmd);
            $filename = $file_path . $domain . '.csv';
            if (!file_exists($filename)) {
                PluginModel::addScanLog($v['id'], __METHOD__, 0, 2);
                addlog(["OneForAll子域名扫描结果，文件不存在:{$filename}"]);
                continue;
            }
            $list = assoc_getcsv($filename);
            if (!empty($list)) {
                $data = [];
                foreach ($list as $key => $val) {
                    unset($val['id']);
                    $val['app_id'] = $v['id'];
                    $val['user_id'] = $v['user_id'];
                    $data[] = $val;
                }
                if ($data) {
                    Db::name('one_for_all')->insertAll($data);
                }
                addlog(["OneForAll子域名扫描数据写入成功:" . json_encode($data)]);
                @unlink($filename);
            } else {
                addlog(["OneForAll子域名扫描,内容获取失败:{$filename}"]);
            }
            PluginModel::addScanLog($v['id'], __METHOD__, 0, 1);
        }
    }

}
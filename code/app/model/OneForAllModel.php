<?php


namespace app\model;


use think\facade\App;
use think\facade\Db;

class OneForAllModel extends BaseModel
{
    // OneForAll子域名扫描
    public static function subdomainScan()
    {
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
            $domain = \app\scan\SubdomainScan::extractDomain($v['url']);
            if ($domain === '') {
                PluginModel::addScanLog($v['id'], __METHOD__, 0, 2, 1, ["content" => "无法提取根域名:{$v['url']}"]);
                addlog(["无法提取根域名:{$v['url']}"]);
                continue;
            }
            $records = \app\scan\SubdomainScan::scan($domain);
            if (!empty($records)) {
                $data = [];
                foreach ($records as $r) {
                    $data[] = [
                        'app_id'    => $v['id'],
                        'user_id'   => $v['user_id'] ?? 0,
                        'alive'     => '1',
                        'resolve'   => $r['resolve'],
                        'url'       => $r['url'],
                        'subdomain' => $r['subdomain'],
                        'level'     => $r['level'],
                        'cname'     => $r['cname'],
                        'ip'        => $r['ip'],
                        'port'      => '',
                    ];
                }
                if ($data) {
                    Db::name('one_for_all')->extra('IGNORE')->insertAll($data);
                }
                addlog(["内置PHP子域名引擎扫描数据写入成功:" . json_encode($data)]);
            } else {
                addlog(["内置PHP子域名引擎扫描,未发现存活子域名:{$domain}"]);
            }
            PluginModel::addScanLog($v['id'], __METHOD__, 0, 1);
        }
    }

}
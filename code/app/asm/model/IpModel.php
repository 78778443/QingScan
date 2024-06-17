<?php


namespace app\asm\model;


use app\model\BaseModel;
use think\Exception;
use think\facade\Db;

class IpModel extends BaseModel
{

    public static function ip_location()
    {
        //转IP
        $where = ['tool' => 'asm_ip_info', 'status' => 0];
        $list = Db::table('task_scan')->where($where)->limit(10)->select()->toArray();
        foreach ($list as $task) {
            Db::table('task_scan')->where(['id' => $task['id']])->update(['status' => 1]);
            $val = json_decode($task['ext_info'], true);

            $ip = $val['ip'];
            $data = [];
            if (!isIPv4($ip)) continue;
            $ip2region = new \Ip2Region();
            $tempStr = $ip2region->simple($ip);
            $data['location'] = explode('【', $tempStr)[0] ?? '';
            $data['isp'] = explode('【', $tempStr)[1] ?? '';
            $data['isp'] = trim($data['isp'], '】');
            if (base64_encode($data['isp']) == '6Zi/6YeM5Lo=') $data['isp'] = '教育网';
            if (strpos($data['isp'], '教育') !== false) $data['isp'] = '教育网';
            echo "{$ip}-{$data['location']}--{$data['isp']}" . PHP_EOL;
            try {
                $ret = Db::table('asm_ip')->where(['ip' => $ip])->update($data);
            } catch (Exception $e) {
                var_dump($data, $e->getMessage(), base64_encode($data['isp']), base64_encode("阿里"));
            }

            Db::table('asm_ip_port')->where(['ip' => $ip])->update($data);
        }
    }
}
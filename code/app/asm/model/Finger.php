<?php
declare (strict_types=1);

namespace app\asm\model;

use think\facade\Cache;
use think\facade\Db;
use think\Model;

/**
 * @mixin \think\Model
 */
class Finger extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'asm_finger';

    public static function start()
    {
        $where = ['tool' => 'scan_app_finger', 'status' => 0];
        $list = Db::table('task_scan')->where($where)->limit(10)->select()->toArray();
        foreach ($list as $task) {
            Db::table('task_scan')->where(['id' => $task['id']])->update(['status' => 1]);
            $item = json_decode($task['ext_info'], true);

            $url = $item['url'];
            $data = self::fingerScan($url);
            $data = array_change_key_case($data, CASE_LOWER);
            unset($data['id']);
            unset($data['url']);
            $data['status'] = intval($data['status']);

            echo $url . '------' . $data['title'] . '------';
            echo Db::table('asm_urls')->strict(false)->where(['id' => $item['id']])->update($data);
            echo PHP_EOL;
        }
    }

    public static function fingerScan($url)
    {
        $parsedUrl = parse_url($url);
        $baseUrl = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];
        if (isset($parsedUrl['port'])) {
            $baseUrl .= ':' . $parsedUrl['port'] . '/';
        }

        //从数据库中获取缓存
        $isHaveData = Db::name('asm_finger')->where(['url' => $baseUrl])->find();
        if ($isHaveData) return $isHaveData;

        // 使用内置指纹识别引擎，替代外部 Finger.py 工具
        $result = \app\scan\FingerScan::scan($baseUrl);

        // 写入 asm_finger 表（仅写表结构存在的字段）
        $info = [
            'url' => $baseUrl,
            'title' => $result['title'],
            'status' => (int)$result['code'],
            'headers' => json_encode($result['headers'], JSON_UNESCAPED_UNICODE),
            'body' => json_encode($result['body_preview'], JSON_UNESCAPED_UNICODE),
        ];
        Db::name('asm_finger')->extra('IGNORE')->insert($info);

        return $info;
    }
}
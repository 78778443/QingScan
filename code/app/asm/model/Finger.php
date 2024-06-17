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

    public static function autoInstall($toolPath)
    {
        // 判断工具是否安装
        if (!file_exists($toolPath)) {
            $dirName = dirname($toolPath);
            !file_exists($dirName) && mkdir($dirName, 0777, true);

            $cmd = "cd {$dirName} && git clone --depth=1 https://github.com/EASY233/Finger.git  && chmod -R 777 Finger";
            exec($cmd, $result);
            $cmd = "cd {$toolPath} && pip install -r requirements.txt -i http://pypi.douban.com/simple/  ";
            exec($cmd, $result);

        }
    }

    public static function fingerScan($url)
    {
        $toolPath = app()->getRootPath() . "extend/tools/Finger";
        self::autoInstall($toolPath);

        $parsedUrl = parse_url($url);
        $baseUrl = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];
        if (isset($parsedUrl['port'])) {
            $baseUrl .= ':' . $parsedUrl['port'] . '/';
        }

        //从数据库中获取
        $isHaveData = Db::name('asm_finger')->where(['url' => $baseUrl])->find();
        if ($isHaveData) return $isHaveData;

        $cmd = "cd {$toolPath} && rm -rf output/* && python3 ./Finger.py -u {$baseUrl}  -o json";
        echo $cmd . PHP_EOL;
        exec($cmd, $result);
        // 扫描$con目录下的所有文件
        $path = "{$toolPath}/output/";
        $files = scandir($path);
        $info = [];
        foreach ($files as $k => $v) {
            // 跳过两个特殊目录   continue跳出循环
            if ($v == "." || $v == "..") {
                continue;
            }
            $result = file_get_contents($path . $v);
            $info = json_decode($result, true)[0];
            foreach ($info as $key => $val) {
                if (is_string($val)) $info[$key] = json_encode($val, 256);
            }
            Db::name('asm_finger')->extra('IGNORE')->insert($info);
        }
        return $info;
    }
}
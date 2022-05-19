<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/8/15
 * Time: 上午10:54
 */


namespace app\model;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use think\facade\Db;


class SemgrepModel extends BaseModel
{
    public static function startScan(string $codePath, string $outPath)
    {
        $cmd = "semgrep -f /data/tools/semgrep/rules.yaml {$codePath} --json  -o {$outPath}";

        $result = systemLog($cmd);
    }

    public static function addDataAll(int $codeId, string $jsonPath, $user_id = 0)
    {
        $data = json_decode(file_get_contents($jsonPath), true);

        foreach ($data['results'] as $v1) {
            $data = [];
            foreach ($v1 as $k2 => $v2) {
                if (is_array($v2)) {
                    foreach ($v2 as $k3 => $v3) {
                        $data["{$k2}_{$k3}"] = is_string($v3) ? $v3 : json_encode($v3, JSON_UNESCAPED_UNICODE);
                    }
                } else {
                    $data[$k2] = $v2;
                }
            }
            $data['code_id'] = $codeId;
            $data['user_id'] = $user_id;
            Db::table('semgrep')->insert($data);
        }
    }
}

<?php

namespace app\code\model;

use app\model\BaseModel;
use think\facade\Db;


class KunlunModel extends BaseModel
{
    public static function startScan(string $codePath)
    {
        $cmd = "python3 kunlun.py scan -t {$codePath}";
        $cmd = "cd ./extend/tools/Kunlun-M  && {$cmd}";
        $result = systemLog($cmd);
        return true;
    }

    public static function addDataAll(int $codeId, string $jsonPath)
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
            Db::table('semgrep')->insert($data);
        }
    }
}

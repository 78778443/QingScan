<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/8/15
 * Time: 上午10:54
 */


namespace app\code\model;

use app\model\BaseModel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use think\facade\Db;


class SemgrepModel extends BaseModel
{
    public static function startScan(string $codePath, string $outPath)
    {
        if (file_exists($outPath)) return false;
        // 内置代码审计引擎（纯 PHP 实现）替代外部 semgrep 工具：
        // 递归扫描代码文件并按内置规则库逐行匹配，输出 JSON 结构与 semgrep --json 兼容
        // （check_id/path/start.line/end.line/extra），addDataAll() 解析入库逻辑无需任何改动
        return \app\scan\CodeAudit::scan($codePath, $outPath);
    }

    public static function addDataAll(int $codeId, string $jsonPath, $user_id = 0)
    {

        $data = json_decode(file_get_contents($jsonPath), true);
        $num = count($data['results']);
        echo "在{$jsonPath}找到{$num}条结果" . PHP_EOL;
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
            $ret = Db::table('semgrep')->insert($data);

            var_dump([$ret, $data]);
        }
    }
}

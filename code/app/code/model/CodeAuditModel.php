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


class CodeAuditModel extends BaseModel
{
    public static function startScan(string $codePath, string $outPath)
    {
        if (file_exists($outPath)) return false;
        // 内置代码审计引擎（纯 PHP 实现）：
        // 递归扫描代码文件并按内置规则库逐行匹配，输出 JSON 结构
        // （check_id/path/start.line/end.line/extra），addDataAll() 解析入库逻辑无需任何改动
        return \app\scan\CodeAudit::scan($codePath, $outPath);
    }

    public static function addDataAll(int $codeId, string $jsonPath, $user_id = 0)
    {

        $data = json_decode(file_get_contents($jsonPath), true);
        $num = count($data['results']);
        echo "在{$jsonPath}找到{$num}条结果" . PHP_EOL;
        foreach ($data['results'] as $v1) {
            $row = [
                'code_id'   => $codeId,
                'user_id'   => $user_id,
                'file'      => $v1['path'] ?? '',
                'line'      => $v1['start']['line'] ?? 0,
                'rule_id'   => $v1['check_id'] ?? '',
                'message'   => $v1['extra']['message'] ?? '',
                'severity'  => strtolower($v1['extra']['severity'] ?? 'warning'),
                'is_delete' => 0,
            ];
            $ret = Db::table('scan_code_audit')->insert($row);

            var_dump([$ret, $row]);
        }
    }
}

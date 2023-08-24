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
        self::installTool();
        $rulePath = "extend/tools/semgrep/";
        $ruleConfig = "--config=./{$rulePath}python.yaml --config=./{$rulePath}go.yaml --config=./{$rulePath}java.yaml --config=./{$rulePath}kotlin.yaml --config=./{$rulePath}php.yaml";
        $cmd = "semgrep $ruleConfig {$codePath} --json  -o {$outPath}";

        $result = systemLog($cmd);
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

    public static function installTool()
    {
        // 检查 semgrep 是否已安装
        $semgrepInstalled = shell_exec("command -v semgrep");

        if (!$semgrepInstalled) {
            // 如果 semgrep 未安装，则执行安装命令
            echo "semgrep is not installed. Installing...\n";
            $installCommand = "apt install -y python3-pip && python3 -m pip install semgrep -i https://pypi.tuna.tsinghua.edu.cn/simple";
            shell_exec($installCommand);
            echo "semgrep has been installed.\n";
        } else {
            echo "semgrep is already installed.\n";
        }
    }
}

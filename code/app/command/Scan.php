<?php
declare (strict_types=1);

namespace app\command;

use app\asm\model\Finger;
use app\asm\model\Fofa;
use app\asm\model\IpModel;
use app\code\model\CodeCheckModel;
use app\code\model\MurphysecModel;
use app\model\AppModel;
use app\model\CveModel;
use app\model\GoogleModel;
use app\model\HostPortModel;
use app\model\HydraModel;
use app\model\OneForAllModel;
use app\model\TaskModel;
use app\model\ToolsCheckModel;
use app\model\UrlsModel;
use app\model\WebScanModel;
use app\webscan\model\AppDirmapModel;
use app\webscan\model\AwvsModel;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\Output;
use Throwable;

class Scan extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('scan')
            ->addArgument("func", Argument::OPTIONAL, "扫描的内容")
            ->addArgument("custom", Argument::OPTIONAL, "自定义工具名")
            ->addArgument("scan_type", Argument::OPTIONAL, "自定义工具扫描类型")
            ->addArgument("custom_store", Argument::OPTIONAL, "自定义工具结果分析")
            ->setDescription('the scan command');
    }

    protected function execute(Input $input, Output $output): void
    {
        $func = trim($input->getArgument('func'));
        
        // 定义扫描任务映射关系
        $scanTasks = [
            // 生成扫描任务
            "create_task" => [TaskModel::class, 'autoAddTask'],
            "start_task" => [TaskModel::class, 'startTask'],

            // asm扫描
            "asm_discover_fofa" => [Fofa::class, 'discover', 'fofa'],
            "asm_domain_oneforall" => [OneForAllModel::class, 'subdomainScan', 'oneforall'],
            "asm_domain_fofa" => [CveModel::class, 'fofaSearch', 'fofa'],
            "asm_ip_info" => [IpModel::class, 'ip_location'],
            "asm_ip_nmap" => [HostPortModel::class, 'NmapPortScan', 'nmap'],
            "nmap" => [HostPortModel::class, 'NmapPortScan', 'nmap'],

            // web扫描，默认扫描app表
            "scan_app_finger" => [Finger::class, 'start'],
            "scan_app_dirmap" => [AppDirmapModel::class, 'dirmapScan', 'dirmap'],
            "scan_app_nuclei" => [WebScanModel::class, 'nucleiScan', 'nuclei'],
            "scan_app_vulmap" => [WebScanModel::class, 'vulmapPocTest', 'vulmap'],
            "scan_app_dismap" => [WebScanModel::class, 'dismapScan', 'dismap'],
            "scan_app_xray" => [WebScanModel::class, 'xray', 'xray'],
            "scan_app_awvs" => [AwvsModel::class, 'awvsScan', 'awvs'],
            "scan_app_rad" => [WebScanModel::class, 'rad', 'rad'],
            "scan_app_jietu" => [GoogleModel::class, 'jietu'],
            "scan_app_whatweb" => [AppModel::class, 'whatweb', 'whatweb'],
            "scan_app_google" => [GoogleModel::class, 'getBaseInfo'],
            "scan_ip_hydra" => [HydraModel::class, 'sshScan', 'hydra'],
            "scan_url_sqlmap" => [UrlsModel::class, 'sqlmapScan', 'sqlmap'],

            // 代码扫描
            "code_fortify" => [CodeCheckModel::class, 'fortifyScan', 'fortify'],
            "code_semgrep" => [CodeCheckModel::class, 'semgrep', 'semgrep'],
            "code_murphysec" => [MurphysecModel::class, 'murphysec_scan', 'murphysec'],
            "code_codeql" => [MurphysecModel::class, 'murphysec_scan', 'codeql'],
        ];

        // 执行对应的任务
        if (isset($scanTasks[$func])) {
            $task = $scanTasks[$func];
            try {
                // 检查是否需要进行工具检查
                if (isset($task[2])) {
                    $output->writeln("正在检查 {$task[2]} 工具环境...");
                    if (!ToolsCheckModel::checkToolInstalled($task[2])) {
                        $output->writeln("工具 {$task[2]} 未安装或配置不正确，跳过执行");
                        return;
                    } else {
                        $output->writeln("工具 {$task[2]} 环境检查通过");
                    }
                }
                
                // 调用对应的执行方法
                $output->writeln("开始执行任务: {$func}");
                call_user_func([$task[0], $task[1]]);
                $output->writeln("任务执行完成: {$func}");
            } catch (Throwable $e) {
                $output->writeln("执行任务时发生错误: " . $e->getMessage());
                $output->writeln("错误位置: " . $e->getFile() . ":" . $e->getLine());
            }
        } else {
            // 如果没有匹配的任务，输出帮助信息
            $output->writeln("未找到指定的任务: {$func}");
            $output->writeln("可用的任务列表:");
            foreach (array_keys($scanTasks) as $taskName) {
                $output->writeln("  - {$taskName}");
            }
        }
    }
}
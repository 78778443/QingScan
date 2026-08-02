<?php
declare (strict_types=1);

namespace app\command;

use app\asm\model\WebInfo;
use app\asm\model\Fofa;
use app\asm\model\IpModel;
use app\code\model\CodeCheckModel;
use app\model\AppModel;
use app\model\HostPortModel;
use app\model\WeakPassModel;
use app\model\SubdomainModel;
use app\model\TaskModel;
use app\model\ToolsCheckModel;
use app\model\UrlsModel;
use app\model\WebScanModel;
use app\webscan\model\AppDirmapModel;
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

    /** 扫描任务映射关系（手动执行与常驻调度器共用） */
    public static function getScanTasks(): array
    {
        return [
            // 生成扫描任务
            "create_task" => [TaskModel::class, 'autoAddTask'],
            "start_task" => [TaskModel::class, 'startTask'],

            // asm扫描
                        "asm_domain_subdomain" => [SubdomainModel::class, 'subdomainScan', 'subdomain'],
                        "asm_ip_info" => [IpModel::class, 'ip_location'],
            "asm_ip_port_scan" => [HostPortModel::class, 'portScan', 'port_scan'],
            "nmap" => [HostPortModel::class, 'portScan', 'port_scan'],

            // web扫描，默认扫描app表
            "scan_app_web_info" => [WebInfo::class, 'webInfoScan'],
            "scan_app_dir_scan" => [AppDirmapModel::class, 'dirmapScan', 'dir_scan'],
            "scan_app_gen_vuln" => [WebScanModel::class, 'genVulnScan', 'gen_vuln'],
            "scan_app_vul_verify" => [WebScanModel::class, 'vulVerifyScan', 'vul_verify'],
            "scan_app_asset_finger" => [WebScanModel::class, 'assetFingerScan', 'asset_finger'],
            "scan_app_web_vuln" => [WebScanModel::class, 'webVulnScan', 'web_vuln'],
                        "scan_app_crawler" => [WebScanModel::class, 'crawlerScan', 'crawler'],
                        "scan_app_finger" => [AppModel::class, 'fingerScan', 'finger'],
            "scan_app_waf" => [WafCheckModel::class, 'wafCheckScan', 'waf'],

                        "scan_ip_weak_pass" => [WeakPassModel::class, 'weakPassScan', 'weak_pass'],
            "scan_url_sql_inject" => [UrlsModel::class, 'sqlInjectScan', 'sql_inject'],

            // 代码扫描
                        "code_audit" => [CodeCheckModel::class, 'semgrep', 'code_audit'],
                        
            // 旧工具名兼容（历史任务数据已通过 SQL 迁移，此处兜底处理未迁移的存量数据）
            "scan_app_xray" => [WebScanModel::class, 'webVulnScan', 'web_vuln'],
            "scan_app_nuclei" => [WebScanModel::class, 'genVulnScan', 'gen_vuln'],
            "scan_app_vulmap" => [WebScanModel::class, 'vulVerifyScan', 'vul_verify'],
            "scan_url_sqlmap" => [UrlsModel::class, 'sqlInjectScan', 'sql_inject'],
            "scan_app_dirmap" => [AppDirmapModel::class, 'dirmapScan', 'dir_scan'],
            "scan_app_whatweb" => [AppModel::class, 'fingerScan', 'finger'],
            "scan_app_dismap" => [WebScanModel::class, 'assetFingerScan', 'asset_finger'],
            // 注意：旧 scan_app_finger（现 scan_app_web_info）与现 scan_app_finger（原 whatweb）键名冲突，
            // 旧数据已由 SQL 迁移到 scan_app_web_info，此处不再追加兼容条目
            "scan_app_rad" => [WebScanModel::class, 'crawlerScan', 'crawler'],
            "scan_app_crawlergo" => [WebScanModel::class, 'spiderScan', 'spider'],
            "scan_ip_hydra" => [WeakPassModel::class, 'weakPassScan', 'weak_pass'],
            "asm_ip_nmap" => [HostPortModel::class, 'portScan', 'port_scan'],
            "asm_domain_oneforall" => [SubdomainModel::class, 'subdomainScan', 'subdomain'],
            "code_semgrep" => [CodeCheckModel::class, 'semgrep', 'code_audit'],
                                ];
    }

    protected function execute(Input $input, Output $output): void
    {
        $func = trim($input->getArgument('func'));
        $scanTasks = self::getScanTasks();

        // 执行对应的任务
        if (isset($scanTasks[$func])) {
            $task = $scanTasks[$func];
            
            try {
                // 检查是否需要进行工具检查
                if (isset($task[2])) {
                    $output->writeln("正在检查 {$task[2]} 工具环境...");
                    if (!ToolsCheckModel::checkToolInstalled($task[2])) {
                        $output->writeln("<error>工具 {$task[2]} 未安装或配置不正确</error>");
                        $output->writeln("<info>安装引导:</info>");
                        $output->writeln(ToolsCheckModel::getToolInstallGuide($task[2]));
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
                $output->writeln("<error>执行任务时发生错误: " . $e->getMessage() . "</error>");
                $output->writeln("<error>错误位置: " . $e->getFile() . ":" . $e->getLine() . "</error>");
                $output->writeln("<error>详细详细: " . $e->getTraceAsString() . "</error>");
            }
        } else {
            // 如果没有匹配的任务，输出帮助信息
            $output->writeln("<error>未找到指定的任务: {$func}</error>");
            $output->writeln("<info>可用的任务列表:</info>");
            foreach (array_keys($scanTasks) as $taskName) {
                $output->writeln("  - {$taskName}");
            }
        }
    }
}
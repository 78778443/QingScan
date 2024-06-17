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
use app\model\UrlsModel;
use app\model\WebScanModel;
use app\webscan\model\AppDirmapModel;
use app\webscan\model\AwvsModel;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\Output;

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
        //生成扫描任务
        if ($func == "create_task") TaskModel::autoAddTask();
        if ($func == "start_task") TaskModel::startTask();

        //asm扫描
        if ($func == "asm_discover_fofa") Fofa::discover();
        if ($func == 'asm_domain_oneforall') OneForAllModel::subdomainScan();
        if ($func == 'asm_domain_fofa') CveModel::fofaSearch();
        if ($func == 'asm_ip_info') IpModel::ip_location();
        if ($func == "asm_ip_nmap") HostPortModel::NmapPortScan();

        //web扫描，默认扫描app表
        if ($func == 'scan_app_finger') Finger::start();
        if ($func == 'scan_app_dirmap') AppDirmapModel::dirmapScan();
        if ($func == 'scan_app_nuclei') WebScanModel::nucleiScan();
        if ($func == 'scan_app_vulmap') WebScanModel::vulmapPocTest();
        if ($func == 'scan_app_dismap') WebScanModel::dismapScan();
        if ($func == "scan_app_xray") WebScanModel::xray();
        if ($func == "scan_app_awvs") AwvsModel::awvsScan();
        if ($func == "scan_app_rad") WebScanModel::rad();
        if ($func == 'scan_app_jietu') GoogleModel::jietu();
        if ($func == 'scan_app_whatweb') AppModel::whatweb();
        if ($func == 'scan_app_google') GoogleModel::getBaseInfo();
        if ($func == 'scan_ip_hydra') HydraModel::sshScan();
        if ($func == 'scan_url_sqlmap') UrlsModel::sqlmapScan();

        //代码扫描
        if ($func == "code_fortify") CodeCheckModel::fortifyScan();
        if ($func == "code_semgrep") CodeCheckModel::semgrep();
        if ($func == 'code_murphysec') MurphysecModel::murphysec_scan();
    }
}

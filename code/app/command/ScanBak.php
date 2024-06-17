<?php
declare (strict_types=1);

namespace app\command;

use app\asm\model\Finger;
use app\asm\model\Fofa;
use app\asm\model\IpModel;
use app\code\model\CodeCheckModel;
use app\code\model\CodeJavaModel;
use app\code\model\CodeModel;
use app\code\model\CodeWebshellModel;
use app\code\model\MurphysecModel;
use app\model\AppModel;
use app\model\ConfigModel;
use app\model\CveModel;
use app\model\GithubKeywordMonitorModel;
use app\model\GithubNoticeModel;
use app\model\GoogleModel;
use app\model\HostModel;
use app\model\HostPortModel;
use app\model\HydraModel;
use app\model\MobsfscanModel;
use app\model\OneForAllModel;
use app\model\PluginModel;
use app\model\ProcessSafeModel;
use app\model\ProxyModel;
use app\model\PythonLibraryModel;
use app\model\TaskModel;
use app\model\UnauthorizedModel;
use app\model\UrlsModel;
use app\model\WebScanModel;
use app\webscan\model\AppDirmapModel;
use app\webscan\model\AppWafw00fModel;
use app\webscan\model\AwvsModel;
use app\webscan\model\XrayModel;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\Output;

class ScanBak extends Command
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

    protected function execute(Input $input, Output $output)
    {
        $func = trim($input->getArgument('func'));
        if ($func == "task_scan") {
            TaskModel::autoAddTask();
        } elseif ($func == "asm_discover") {
            Fofa::discover();
        } elseif ($func == "safe") {
            ProcessSafeModel::safe();
        } elseif ($func == 'plugin_safe') {
            PluginModel::safe();
        } elseif ($func == "xray") {
            WebScanModel::xray();
        } elseif ($func == "awvs") {
            AwvsModel::awvsScan();
        } elseif ($func == "rad") {
            WebScanModel::rad();
        } elseif ($func == "host") {
            HostModel::autoAddHost();
        } elseif ($func == "port") {
            HostPortModel::scanHostPort();
        } elseif ($func == "nmap") {
            HostPortModel::NmapPortScan();
        }  elseif ($func == "temp") {
            XrayModel::temp();
        } elseif ($func == "cve") {
            CveModel::cveScan();
        } elseif ($func == 'google') {
            GoogleModel::getBaseInfo();
        } elseif ($func == 'jietu') {
            GoogleModel::jietu();
        } elseif ($func == 'hostinfo') {
            HostPortModel::upadteRegion();
        } elseif ($func == 'whatweb') {
            AppModel::whatweb();
        } elseif ($func == 'subdomainScan') {
            OneForAllModel::subdomainScan();
        } elseif ($func == 'hydra') {
            HydraModel::sshScan();
        } elseif ($func == 'sqlmapScan') {
            UrlsModel::sqlmapScan();
        } elseif ($func == 'fofa') {
            CveModel::fofaSearch();
        } elseif ($func == 'dirmapScan') {
            AppDirmapModel::dirmapScan();
        } elseif ($func == 'getNotice') {
            GithubNoticeModel::getNotice();
        } elseif ($func == 'reptile') {
            UrlsModel::reptile();
        } elseif ($func == 'backup') {
            ConfigModel::backup();
        } elseif ($func == 'whatwebPocTest') {
            AppModel::whatwebPocTest();
        } elseif ($func == 'wafw00fScan') {
            AppWafw00fModel::wafw00fScan();
        } elseif ($func == 'nuclei') {
            WebScanModel::nucleiScan();
        } elseif ($func == 'vulmapPocTest') {
            WebScanModel::vulmapPocTest();
        } elseif ($func == 'crawlergoScan') {
            WebScanModel::crawlergoScan();
        } elseif ($func == 'dismapScan') {
            WebScanModel::dismapScan();
        } elseif ($func == "fortify") {
            CodeCheckModel::fortifyScan();
        } elseif ($func == "kunlun") {
            CodeCheckModel::kunLunScan();
        } elseif ($func == "semgrep") {
            CodeCheckModel::semgrep();
        } elseif ($func == 'getProjectComposer') {
            CodeModel::code_php();
        } elseif ($func == 'code_python') {
            PythonLibraryModel::code_python();
        } elseif ($func == 'code_java') {
            CodeJavaModel::code_java();
        } elseif ($func == 'code_webshell_scan') {
            CodeWebshellModel::code_webshell_scan();
        } elseif ($func == 'mobsfscan') {
            MobsfscanModel::mobsfscan();
        } elseif ($func == 'murphysecScan') {
            MurphysecModel::murphysec_scan();
        } elseif ($func == 'unauthorizeScan') {
            UnauthorizedModel::unauthorizeScan();
        } elseif ($func == 'deleteDir') {
            PluginModel::deleteCodeDir();
        } elseif ($func == 'custom') {
            $custom = trim($input->getArgument('custom'));
            $scanType = $input->getArgument('scan_type');
            $scanType = array_search($scanType, ['app', 'host', 'code', 'url']);
            PluginModel::custom_event($custom, $scanType);
        } elseif ($func == 'custom_store') {
            $className = 'app\model\\' . trim($input->getArgument('custom')) . 'PluginsModel';
            $funcName = $input->getArgument('scan_type');
            $className::$funcName();
        } elseif ($func == 'domainFindIp') {
            HostPortModel::domainFindIp();
        } elseif ($func == 'scanWebPort') {
            HostPortModel::scanWebPort();
        } elseif ($func == 'domainFindUrl') {
            HostPortModel::domainFindUrl();
        } elseif ($func == 'ip_location') {
            IpModel::ip_location();
        } elseif ($func == 'finger') {
            $finger = new Finger();
            $finger->start();
        }
    }
}

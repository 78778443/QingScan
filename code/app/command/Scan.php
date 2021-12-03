<?php
declare (strict_types=1);

namespace app\command;

use app\model\AppDirmapModel;
use app\model\AppModel;
use app\model\AwvsModel;
use app\model\CodeCheckModel;
use app\model\CodeJavaModel;
use app\model\CodeModel;
use app\model\ConfigModel;
use app\model\CveModel;
use app\model\GithubKeywordMonitorModel;
use app\model\GithubNoticeModel;
use app\model\GoogleModel;
use app\model\HostModel;
use app\model\HostPortModel;
use app\model\HydraModel;
use app\model\KafkaModel;
use app\model\OneForAllModel;
use app\model\ProcessSafeModel;
use app\model\ProxyModel;
use app\model\PythonLibraryModel;
use app\model\UrlsModel;
use app\model\WebScanModel;
use app\model\XrayModel;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;

class Scan extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('scan')
            ->addArgument("func", Argument::OPTIONAL, "扫描的内容")
            ->setDescription('the scan command');
    }

    protected function execute(Input $input, Output $output)
    {

        $func = trim($input->getArgument('func'));
        if ($func == "safe") {
            ProcessSafeModel::safe();
        } elseif ($func == "xray") {
            WebScanModel::xray();
        } elseif ($func == "awvs") {
            AwvsModel::scan();
        } elseif ($func == "rad") {
            WebScanModel::rad();
        } elseif ($func == "host") {
            HostModel::autoAddHost();
        } elseif ($func == "port") {
            HostPortModel::scanHostPort();
        } elseif ($func == "nmap") {
            HostPortModel::NmapPortScan();
        } elseif ($func == "fortify") {
            CodeCheckModel::scan();
        } elseif ($func == "kunlun") {
            CodeCheckModel::kunLunScan();
        } elseif ($func == "semgrep") {
            CodeCheckModel::semgrep();
        } elseif ($func == "subdomain") {
            AppModel::subdomain();
        } elseif ($func == "temp") {
            XrayModel::temp();
        } elseif ($func == "cve") {
            CveModel::scan();
        } elseif ($func == 'google') {
            GoogleModel::screenshot();
        } elseif ($func == 'upadteRegion') {
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
        } elseif($func == 'getNotice'){
            GithubNoticeModel::getNotice();
        } elseif($func == 'reptile'){
            UrlsModel::reptile();
        } elseif($func == 'getProjectComposer'){
            CodeModel::getProjectComposer();
        } elseif($func == 'code_python'){
            PythonLibraryModel::code_python();
        } elseif($func == 'code_java'){
            CodeJavaModel::code_java();
        } elseif($func == 'backup'){
            ConfigModel::backup();
        } elseif($func == 'giteeProject'){
            CodeModel::giteeProject();
        } elseif($func == 'freeAgent'){
            ProxyModel::freeAgent();
        } elseif($func == 'github_keyword_monitor'){
            GithubKeywordMonitorModel::keywordMonitor();
        } elseif($func == 'whatwebPocTest'){
            AppModel::whatwebPocTest();
        } elseif ($func == 'xrayAgentResult'){
            WebScanModel::xrayAgentResult();
        }

        // 指令输出
//        $output->writeln($func);
//        $output->writeln('scan');
    }
}

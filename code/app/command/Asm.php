<?php
declare (strict_types=1);

namespace app\command;

use app\asm\model\HostAssetsModel;
use app\asm\model\HostAssetsSyncModel;
use app\asm\model\VulnerabilityModel;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\Output;
use think\facade\Db;
use Throwable;
use Volcengine\Common\Configuration;
use Volcengine\Ecs\Api\ECSApi;
use Volcengine\Ecs\Model\DescribeInstancesRequest;

class Asm extends Command
{
    protected function configure()
    {
        // 指令配置
        Asm::setName('asm')
            ->addArgument("platform", Argument::OPTIONAL, "云平台类型", "huoshan")
            ->addArgument("file", Argument::OPTIONAL, "文件路径", "")
            ->setDescription('ASM相关命令：拉取主机资产、导入漏洞数据等');
    }

    protected function execute(Input $input, Output $output): void
    {
        $platform = trim($input->getArgument('platform'));
        $filePath = trim($input->getArgument('file'));
        $output->writeln("开始执行主机资产拉取任务，平台：{$platform}");

        if ($platform === 'huoshan') {
            HostAssetsSyncModel::importFromHuoshan($output);
        } elseif ($platform === 'tianyi') {
            HostAssetsSyncModel::importFromTianyi($output, 'A');
            $output->writeln("<info>天翼云A团队主机资产导入完成</info>");
        } elseif ($platform === 'tianyi_b') {
            HostAssetsSyncModel::importFromTianyi($output, 'B');
            $output->writeln("<info>天翼云B团队主机资产导入完成</info>");
        } elseif ($platform === 'yidong') {
            HostAssetsSyncModel::importFromYidong($output);
            $output->writeln("<info>移动云主机资产导入完成</info>");
        } elseif ($platform === 'aliyun') {
            HostAssetsSyncModel::importFromAliyun($output);
            $output->writeln("<info>阿里云主机资产导入完成</info>");
        } elseif ($platform === 'baidu') {
            HostAssetsSyncModel::importFromBaidu($output);
            $output->writeln("<info>百度云主机资产导入完成</info>");
        } elseif ($platform === 'qingteng') {
            HostAssetsSyncModel::syncFromQingTengHids($output);
            $output->writeln("<info>青藤云HIDS状态同步任务执行完成</info>");
        } elseif ($platform === 'qingteng_bug') {
            if (empty($filePath)) {
                $output->writeln("<error>请指定要导入的JSON文件路径</error>");
                return;
            }
            // 导入青藤云漏洞数据
            VulnerabilityModel::importFromStaticJson($output, $filePath);
        } else {
            $output->writeln("<error>不支持的云平台类型：{$platform}</error>");
        }

    }


}
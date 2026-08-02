<?php

namespace app\api\controller;

use app\api\BaseController;
use think\facade\Db;

class Index extends BaseController
{
    /**
     * 首页统计看板
     * 复用 app\controller\Index::index() 的统计逻辑
     */
    public function dashboard()
    {
        try {
            $where = [];
        // 黑盒项目数量
        $appCount = Db::table('app')->where($where)->count();
        // 黑盒rad数量
        $urlsCount = Db::table('asm_urls')->where($where)->count();
        // 黑盒xray数量（统一漏洞表 scan_vuln）
        $xrayCount = Db::table('scan_vuln')->where(['source' => 'xray'])->count();
        // 黑盒sqlmap数量
        $sqlmapCount = Db::table('urls_sqlmap')->where($where)->count();
        // 黑盒awvs数量
        $awvsCount = Db::table('awvs_app')->where($where)->count();
        // 黑盒vulmap数量（统一漏洞表 scan_vuln）
        $vulmapCount = Db::table('scan_vuln')->where(['source' => 'vulmap'])->count();
        // 黑盒nuclei数量（统一漏洞表 scan_vuln）
        $nucleiCount = Db::table('scan_vuln')->where(['source' => 'nuclei'])->count();
        // 黑盒dirmap数量
        $dirmapCount = Db::table('app_dirmap')->where($where)->count();
        // 黑盒whatweb数量
        $whatwebCount = Db::table('app_whatweb')->where($where)->count();
        // 子域名数量（scan_subdomain）
        $oneforallCount = Db::table('scan_subdomain')->where($where)->count();

        // 资产探测
        $hostCount = Db::table('asm_host')->count();
        // 端口数量
        $portCount = Db::table('asm_host_port')->count();
        // 服务数量
        $serviceCount = Db::table('asm_host_port')->group("service")->count();
        // 未授权漏洞
        $unauthorizedCount = Db::table('host_unauthorized')->count();

        // 白盒统计
        $codeCount = Db::table('code')->count();
        $semgrepCount = Db::table('scan_code_audit')->count();
        $fortifyCount = Db::table('fortify')->count();
        $mobsfscanCount = Db::table('mobsfscan')->count();
        $murphysecCount = Db::table('murphysec')->count();
        $phpCount = Db::table('code_composer')->count();
        $pythonCount = Db::table('code_python')->count();
        $javaCount = Db::table('code_java')->count();
        $hemaCount = Db::table('code_webshell')->count();

        // 漏洞信息库
        $pocsuite3Count = Db::table('pocsuite3')->count();
        $vulnerableCount = Db::table('vulnerable')->count();
        $pocsCount = Db::table('pocs_file')->count();
        $targetCount = Db::table('vul_target')->count();

        $data = [
            [
                "name" => "网站扫描",
                "value" => $appCount,
                "subInfo" => [
                    ["name" => "Web漏洞", "value" => $xrayCount, "href" => "/webscan/xray"],
                    ["name" => "SQL注入", "value" => $sqlmapCount, "href" => "/webscan/sqlmap"],
                    ["name" => "awvs", "value" => $awvsCount, "href" => "/webscan/awvs"],
                    ["name" => "漏洞验证", "value" => $vulmapCount, "href" => "/webscan/vulmap"],
                    ["name" => "通用漏洞", "value" => $nucleiCount, "href" => "/webscan/nuclei"],
                    ["name" => "目录扫描", "value" => $dirmapCount, "href" => "/webscan/dirmap"],
                    ["name" => "指纹识别", "value" => $whatwebCount, "href" => "/webscan/whatweb"],
                ]
            ],
            [
                "name" => "资产探测",
                "value" => $hostCount,
                "subInfo" => [
                    ["name" => "主机", "value" => $hostCount, "href" => "/asm/host"],
                    ["name" => "子域名", "value" => $oneforallCount, "href" => "/asm/subdomain"],
                    ["name" => "URL", "value" => $urlsCount, "href" => "/asm/url"],
                    ["name" => "端口", "value" => $portCount, "href" => "/asm/port"],
                    ["name" => "中间件", "value" => $serviceCount, "href" => "/asm/port"],
                    ["name" => "未授权漏洞", "value" => $unauthorizedCount, "href" => "/result/unauthorized"],
                ]
            ],
            [
                "name" => "白盒审计",
                "value" => $codeCount,
                "subInfo" => [
                    ["name" => "代码审计", "value" => $fortifyCount, "href" => "/code"],
                    ["name" => "规则扫描", "value" => $semgrepCount, "href" => "/code"],
                    ["name" => "移动应用", "value" => $mobsfscanCount, "href" => "/code"],
                    ["name" => "软件依赖", "value" => $murphysecCount, "href" => "/code"],
                    ["name" => "webshell", "value" => $hemaCount, "href" => "/code"],
                ]
            ],
            [
                "name" => "专项利用",
                "value" => $pocsuite3Count,
                "subInfo" => [
                    ["name" => "漏洞情报", "value" => $vulnerableCount, "href" => "/result/vulnerable"],
                    ["name" => "Poc脚本", "value" => $pocsCount, "href" => "/result/plugin"],
                    ["name" => "漏洞数量", "value" => $targetCount, "href" => "/webscan/targets"],
                ]
            ]
        ];

            return $this->apiReturn(1, $data);
        } catch (\Throwable $e) {
            return $this->apiReturn(0, [], $e->getMessage());
        }
    }

    /**
     * 统计图表（6 组）
     * 复用 app\controller\Index::tongji() 的图表查询逻辑
     */
    public function tongji()
    {
        try {
            // 漏洞类型分布
            $folderCount = Db::table('fortify')->field('Folder as name,count(Folder) as value')->group('Folder')->select()->toArray();

            // 漏洞统计
            $shijianCount = [];
            $shijianCount[] = ['name' => '14天', 'value' => Db::table('fortify')->whereTime('create_time', '>=', date('Y-m-d H:i:s', time() - 14 * 86400))->count('id')];
            $shijianCount[] = ['name' => '7天', 'value' => Db::table('fortify')->whereTime('create_time', '>=', date('Y-m-d H:i:s', time() - 7 * 86400))->count('id')];
            $shijianCount[] = ['name' => '24小时', 'value' => Db::table('fortify')->whereTime('create_time', '>=', date('Y-m-d H:i:s', time() - 1 * 86400))->count('id')];

            // 漏洞排行
            $bugPaihang = Db::table('fortify')->field('Category as name,count(Category) as value')->where("Folder != 'Low'")->group('Category')->select()->toArray();
            array_multisort(array_column($bugPaihang, 'value'), SORT_DESC, $bugPaihang);
            $bugPaihang = array_slice($bugPaihang, 0, 10);

            // 端口发现
            $portCount = Db::table('asm_host_port')->field('port as name,count(port) as value')->group('port')->select()->toArray();
            array_multisort(array_column($portCount, 'value'), SORT_DESC, $portCount);
            $portCount = array_slice($portCount, 0, 10);

            // 主机统计
            $hostCount = Db::table('asm_host_port')->field('host as name,count(host) as value')->group('host')->select()->toArray();
            array_multisort(array_column($hostCount, 'value'), SORT_DESC, $hostCount);
            $hostCount = array_slice($hostCount, 0, 10);

            // 网站统计
            $serviceCount = Db::table('app_info')->where('server', '<>', 'unknown')->field('server as name,count(server) as value')->group('server')->select()->toArray();
            array_multisort(array_column($serviceCount, 'value'), SORT_DESC, $serviceCount);
            $serviceCount = array_slice($serviceCount, 0, 10);

            $data = [
                ['key' => 'folderCount', 'data' => $folderCount, 'title' => "危害等级"],
                ['key' => 'shijianCount', 'data' => $shijianCount, 'title' => "新增统计"],
                ['key' => 'bugPaihang', 'data' => $bugPaihang, 'title' => "漏洞分类"],
                ['key' => 'portCount', 'data' => $portCount, 'title' => "端口统计"],
                ['key' => 'hostCount', 'data' => $hostCount, 'title' => "主机统计"],
                ['key' => 'serviceCount', 'data' => $serviceCount, 'title' => "服务统计"],
            ];

            return $this->apiReturn(1, $data);
        } catch (\Throwable $e) {
            return $this->apiReturn(0, [], $e->getMessage());
        }
    }
}

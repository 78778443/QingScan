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
        // 黑盒Web漏洞数量（统一漏洞表 scan_vuln，source=web_vuln）
        $xrayCount = Db::table('scan_vuln')->where(['source' => 'web_vuln'])->count();
        // 黑盒sqlmap数量
        $sqlmapCount = Db::table('scan_sql_inject')->where($where)->count();
        // 黑盒漏洞验证数量（统一漏洞表 scan_vuln，source=vul_verify）
        $vulmapCount = Db::table('scan_vuln')->where(['source' => 'vul_verify'])->count();
        // 黑盒通用漏洞数量（统一漏洞表 scan_vuln，source=gen_vuln）
        $nucleiCount = Db::table('scan_vuln')->where(['source' => 'gen_vuln'])->count();
        // 黑盒dirmap数量
        $dirmapCount = Db::table('scan_dir')->where($where)->count();
        // 黑盒whatweb数量
        $fingerCount = Db::table('scan_finger')->where($where)->count();
        // 子域名数量（scan_subdomain）
        $subdomainCount = Db::table('scan_subdomain')->where($where)->count();

        // 资产探测
        $hostCount = Db::table('asm_host')->count();
        // 端口数量
        $portCount = Db::table('asm_host_port')->count();
        // 服务数量
        $serviceCount = Db::table('asm_host_port')->group("service")->count();
        // 未授权漏洞

        // 白盒统计
        $codeCount = Db::table('code')->count();
        $semgrepCount = Db::table('scan_code_audit')->count();

        // 代码审计按漏洞类型分类（rule_id 关键字归类）
        $auditTypeMap = [
            'SQL注入' => ['sql'],
            'XSS' => ['xss'],
            '命令执行' => ['command', 'exec', 'system', 'eval', 'backtick', 'code-execution'],
            '文件包含' => ['inclusion', 'include', 'require'],
            'SSRF' => ['ssrf'],
            '反序列化' => ['unserialize'],
            '文件上传' => ['upload'],
            '硬编码凭据' => ['hardcoded', 'password'],
            '变量覆盖' => ['overwrite', 'parse_str'],
        ];
        $auditTypeCounts = [];
        $auditRows = Db::table('scan_code_audit')->field('rule_id,count(id) as cnt')->group('rule_id')->select()->toArray();
        foreach ($auditRows as $ar) {
            $matched = false;
            foreach ($auditTypeMap as $label => $kws) {
                foreach ($kws as $kw) {
                    if (strpos($ar['rule_id'], $kw) !== false) {
                        $auditTypeCounts[$label] = ($auditTypeCounts[$label] ?? 0) + (int)$ar['cnt'];
                        $matched = true;
                        break 2;
                    }
                }
            }
            if (!$matched) {
                $auditTypeCounts['其他'] = ($auditTypeCounts['其他'] ?? 0) + (int)$ar['cnt'];
            }
        }
        // 白盒审计子项：始终展示全部分类（无数据时值为 0）
        $auditSubInfo = [["name" => "审计总量", "value" => $semgrepCount, "href" => "/code"]];
        foreach (array_keys($auditTypeMap) as $label) {
            $auditSubInfo[] = ["name" => $label, "value" => $auditTypeCounts[$label] ?? 0, "href" => "/code"];
        }

        // 工单统计
        $workOrderCount = Db::table('asm_work_order')->count();
        $woPending = Db::table('asm_work_order')->where('status', 'pending_dispatch')->count();
        $woDispatched = Db::table('asm_work_order')->where('status', 'dispatched')->count();
        $woConfirmed = Db::table('asm_work_order')->where('status', 'confirmed')->count();
        $woFixedUnconfirmed = Db::table('asm_work_order')->where('status', 'fixed_unconfirmed')->count();
        $woFixed = Db::table('asm_work_order')->where('status', 'fixed_confirmed')->count();

        // 漏洞信息库

        $data = [
            [
                "name" => "网站扫描",
                "value" => $appCount,
                "subInfo" => [
                    ["name" => "Web漏洞", "value" => $xrayCount, "href" => "/webscan/web-vuln"],
                    ["name" => "SQL注入", "value" => $sqlmapCount, "href" => "/webscan/sql-inject"],
                    ["name" => "漏洞验证", "value" => $vulmapCount, "href" => "/webscan/vul-verify"],
                    ["name" => "通用漏洞", "value" => $nucleiCount, "href" => "/webscan/gen-vuln"],
                    ["name" => "目录扫描", "value" => $dirmapCount, "href" => "/webscan/dir-scan"],
                    ["name" => "指纹识别", "value" => $fingerCount, "href" => "/webscan/finger"],
                ]
            ],
            [
                "name" => "资产探测",
                "value" => $hostCount,
                "subInfo" => [
                    ["name" => "主机", "value" => $hostCount, "href" => "/asm/host"],
                    ["name" => "子域名", "value" => $subdomainCount, "href" => "/asm/subdomain"],
                    ["name" => "URL", "value" => $urlsCount, "href" => "/asm/url"],
                    ["name" => "端口", "value" => $portCount, "href" => "/asm/port"],
                    ["name" => "中间件", "value" => $serviceCount, "href" => "/asm/port"],
                ]
            ],
            [
                "name" => "白盒审计",
                "value" => $codeCount,
                "subInfo" => $auditSubInfo,
            ],
            [
                "name" => "工单管理",
                "value" => $workOrderCount,
                "subInfo" => [
                    ["name" => "待派发", "value" => $woPending, "href" => "/workorder"],
                    ["name" => "已派发", "value" => $woDispatched, "href" => "/workorder"],
                    ["name" => "已确认", "value" => $woConfirmed, "href" => "/workorder"],
                    ["name" => "修复待确认", "value" => $woFixedUnconfirmed, "href" => "/workorder"],
                    ["name" => "已修复", "value" => $woFixed, "href" => "/workorder"],
                ]
            ],
        ];

            return $this->apiReturn(1, $data);
        } catch (\Throwable $e) {
            return $this->apiReturn(0, [], $e->getMessage());
        }
    }

    /**
     * 统计图表（20 组：6 组原有 + 14 组新增大盘图表）
     * 复用 app\controller\Index::tongji() 的图表查询逻辑
     */
    public function tongji()
    {
        try {
            // 代码审计规则统计（scan_code_audit 按规则分组）
            $folderCount = Db::table('scan_code_audit')->field('rule_id as name,count(rule_id) as value')->group('rule_id')->select()->toArray();
            array_multisort(array_column($folderCount, 'value'), SORT_DESC, $folderCount);
            $folderCount = array_slice($folderCount, 0, 10);

            // 新增统计（最近 14/7/1 天）
            $shijianCount = [];
            $shijianCount[] = ['name' => '14天', 'value' => Db::table('scan_code_audit')->whereTime('create_time', '>=', date('Y-m-d H:i:s', time() - 14 * 86400))->count('id')];
            $shijianCount[] = ['name' => '7天', 'value' => Db::table('scan_code_audit')->whereTime('create_time', '>=', date('Y-m-d H:i:s', time() - 7 * 86400))->count('id')];
            $shijianCount[] = ['name' => '24小时', 'value' => Db::table('scan_code_audit')->whereTime('create_time', '>=', date('Y-m-d H:i:s', time() - 1 * 86400))->count('id')];

            // 审计文件排行
            $bugPaihang = Db::table('scan_code_audit')->field('file as name,count(file) as value')->where('severity', 'error')->group('file')->select()->toArray();
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

            // ==================== 新增 14 组（Dashboard 大盘） ====================

            // 1. 漏洞严重级别（scan_vuln GROUP BY severity，无数据补 0）
            $vulnSeverity = [];
            $severityCount = Db::table('scan_vuln')->field('severity as name,count(id) as value')->group('severity')->select()->toArray();
            $severityMap = array_column($severityCount, 'value', 'name');
            foreach (['low', 'medium', 'high', 'critical'] as $severity) {
                $vulnSeverity[] = ['name' => $severity, 'value' => (int)($severityMap[$severity] ?? 0)];
            }

            // 2. 漏洞来源（source 中文映射）
            $sourceMap = [
                'web_vuln' => 'Web漏洞检测',
                'gen_vuln' => '通用漏洞扫描',
                'vul_verify' => '漏洞验证',
            ];
            $vulnSource = [];
            $sourceCount = Db::table('scan_vuln')->field('source as name,count(id) as value')->group('source')->select()->toArray();
            foreach ($sourceCount as $row) {
                $vulnSource[] = ['name' => $sourceMap[$row['name']] ?? $row['name'], 'value' => (int)$row['value']];
            }

            // 3. 漏洞新增趋势（近 14 天，无数据补 0）
            $vulnTrend = [];
            for ($i = 13; $i >= 0; $i--) {
                $vulnTrendDate = date('Y-m-d', strtotime("-{$i} days"));
                $vulnTrend[] = [
                    'name' => date('m-d', strtotime($vulnTrendDate)),
                    'value' => Db::table('scan_vuln')->whereDay('create_time', $vulnTrendDate)->count('id'),
                ];
            }

            // 4. 资产概览（主机/端口/域名/子域名/URL）
            $assetOverview = [
                ['name' => '主机', 'value' => Db::table('asm_host')->count()],
                ['name' => '端口', 'value' => Db::table('asm_host_port')->count()],
                ['name' => '域名', 'value' => Db::table('asm_domain')->count()],
                ['name' => '子域名', 'value' => Db::table('scan_subdomain')->count()],
                ['name' => 'URL', 'value' => Db::table('asm_urls')->count()],
            ];

            // 6. 端口 Top10
            $portTop = Db::table('asm_host_port')->field('port as name,count(port) as value')->group('port')->select()->toArray();
            array_multisort(array_column($portTop, 'value'), SORT_DESC, $portTop);
            $portTop = array_slice($portTop, 0, 10);

            // 7. 服务分布（service 非空 Top10）
            $serviceDist = Db::table('asm_host_port')->where('service', '<>', '')->field('service as name,count(service) as value')->group('service')->select()->toArray();
            array_multisort(array_column($serviceDist, 'value'), SORT_DESC, $serviceDist);
            $serviceDist = array_slice($serviceDist, 0, 10);

            // 8. 工单状态（中文映射）
            $statusMap = [
                'pending_dispatch' => '待派发',
                'dispatched' => '已派发',
                'confirmed' => '已确认',
                'fixed_unconfirmed' => '修复待确认',
                'fixed_confirmed' => '已修复',
            ];
            $workorderStatus = [];
            $statusCount = Db::table('asm_work_order')->field('status as name,count(id) as value')->group('status')->select()->toArray();
            foreach ($statusCount as $row) {
                $workorderStatus[] = ['name' => $statusMap[$row['name']] ?? $row['name'], 'value' => (int)$row['value']];
            }

            // 10. 工单类型（中文映射）
            $typeMap = [
                'vulnerability' => '漏洞',
                'system' => '系统',
                'other' => '其他',
            ];
            $workorderType = [];
            $typeCount = Db::table('asm_work_order')->field('type as name,count(id) as value')->group('type')->select()->toArray();
            foreach ($typeCount as $row) {
                $workorderType[] = ['name' => $typeMap[$row['name']] ?? $row['name'], 'value' => (int)$row['value']];
            }

            // 11. 工单趋势（近 14 天，按 created_at，无数据补 0）
            $workorderTrend = [];
            for ($i = 13; $i >= 0; $i--) {
                $workorderTrendDate = date('Y-m-d', strtotime("-{$i} days"));
                $workorderTrend[] = [
                    'name' => date('m-d', strtotime($workorderTrendDate)),
                    'value' => Db::table('asm_work_order')->whereDay('created_at', $workorderTrendDate)->count('id'),
                ];
            }

            // 12. 审计规则 Top10
            $auditRules = Db::table('scan_code_audit')->field('rule_id as name,count(rule_id) as value')->group('rule_id')->select()->toArray();
            array_multisort(array_column($auditRules, 'value'), SORT_DESC, $auditRules);
            $auditRules = array_slice($auditRules, 0, 10);

            // 13. 审计级别（error/warning）
            $auditSeverity = Db::table('scan_code_audit')->field('severity as name,count(severity) as value')->group('severity')->select()->toArray();
            array_multisort(array_column($auditSeverity, 'value'), SORT_DESC, $auditSeverity);

            // 14. 高危文件 Top10（severity=error 按文件）
            $auditFiles = Db::table('scan_code_audit')->where('severity', 'error')->field('file as name,count(file) as value')->group('file')->select()->toArray();
            array_multisort(array_column($auditFiles, 'value'), SORT_DESC, $auditFiles);
            $auditFiles = array_slice($auditFiles, 0, 10);

            $data = [
                ['key' => 'folderCount', 'data' => $folderCount, 'title' => "审计规则"],
                ['key' => 'shijianCount', 'data' => $shijianCount, 'title' => "新增统计"],
                ['key' => 'bugPaihang', 'data' => $bugPaihang, 'title' => "高危文件"],
                ['key' => 'portCount', 'data' => $portCount, 'title' => "端口统计"],
                ['key' => 'hostCount', 'data' => $hostCount, 'title' => "主机统计"],
                ['key' => 'serviceCount', 'data' => $serviceCount, 'title' => "服务统计"],
                ['key' => 'vuln_severity', 'data' => $vulnSeverity, 'title' => "漏洞严重级别"],
                ['key' => 'vuln_source', 'data' => $vulnSource, 'title' => "漏洞来源"],
                ['key' => 'vuln_trend', 'data' => $vulnTrend, 'title' => "漏洞新增趋势"],
                ['key' => 'asset_overview', 'data' => $assetOverview, 'title' => "资产概览"],
                ['key' => 'port_top', 'data' => $portTop, 'title' => "端口 Top10"],
                ['key' => 'service_dist', 'data' => $serviceDist, 'title' => "服务分布"],
                ['key' => 'workorder_status', 'data' => $workorderStatus, 'title' => "工单状态"],
                ['key' => 'workorder_type', 'data' => $workorderType, 'title' => "工单类型"],
                ['key' => 'workorder_trend', 'data' => $workorderTrend, 'title' => "工单趋势"],
                ['key' => 'audit_rules', 'data' => $auditRules, 'title' => "审计规则 Top10"],
                ['key' => 'audit_severity', 'data' => $auditSeverity, 'title' => "审计级别"],
                ['key' => 'audit_files', 'data' => $auditFiles, 'title' => "高危文件 Top10"],
            ];

            return $this->apiReturn(1, $data);
        } catch (\Throwable $e) {
            return $this->apiReturn(0, [], $e->getMessage());
        }
    }
}

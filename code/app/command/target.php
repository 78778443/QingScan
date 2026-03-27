<?php
declare(strict_types=1);

namespace app\command;

use app\model\LlmAnalysis;
use app\model\ScanResult;
use app\model\ScanTarget;
use app\model\ScanTask;
use app\model\ScanTool;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\facade\Db;

/**
 * 安全扫描工具命令
 * 用于授权的安全测试和漏洞扫描
 */
class target extends Command
{
    protected function configure()
    {
        $this->setName('scan')
            ->addOption('init', 'i', Option::VALUE_NONE, '初始化工具到数据库')
            ->addOption('list', 'l', Option::VALUE_NONE, '列出可用工具')
            ->addOption('target', 't', Option::VALUE_OPTIONAL, '扫描目标URL/IP')
            ->addOption('scan', 's', Option::VALUE_OPTIONAL, '指定扫描工具')
            ->addOption('tasks', null, Option::VALUE_NONE, '查看任务列表')
            ->addOption('results', 'r', Option::VALUE_OPTIONAL, '查看扫描结果(可选指定任务ID)')
            ->addOption('analyze', 'a', Option::VALUE_OPTIONAL, '执行LLM分析(可选指定任务ID)')
            ->setDescription('安全扫描工具平台');
    }

    protected function execute(Input $input, Output $output)
    {
        try {
            // 初始化工具
            if ($input->getOption('init')) {
                return $this->initTools($output);
            }

            // 列出工具
            if ($input->getOption('list')) {
                return $this->listTools($output);
            }

            // 执行扫描
            $target = $input->getOption('target');
            $tool = $input->getOption('scan');
            if ($target) {
                return $this->runScan($target, $tool, $output);
            }

            // 查看任务列表
            if ($input->getOption('tasks')) {
                return $this->listTasks($output);
            }

            // 查看结果 - 通过检查参数是否存在
            $argv = $_SERVER['argv'] ?? [];
            if (in_array('--results', $argv) || in_array('-r', $argv)) {
                $taskId = $input->getOption('results');
                return $this->showResults($taskId, $output);
            }

            // LLM分析
            if (in_array('--analyze', $argv) || in_array('-a', $argv)) {
                $taskId = $input->getOption('analyze');
                return $this->runAnalyze($taskId, $output);
            }

            // 默认显示帮助
            $output->writeln($this->getHelp());
            $output->writeln('');
            $output->writeln('使用示例:');
            $output->writeln('  php think scan --init              初始化工具');
            $output->writeln('  php think scan --list              列出可用工具');
            $output->writeln('  php think scan -t "http://example.com?id=1" -s sqlmap');
            $output->writeln('  php think scan --tasks             查看任务列表');
            $output->writeln('  php think scan --results [任务ID]  查看扫描结果');
            $output->writeln('  php think scan --analyze [任务ID]  执行LLM分析');

        } catch (\Exception $e) {
            $output->error('错误: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }

    /**
     * 初始化工具到数据库
     */
    protected function initTools(Output $output): int
    {
        $output->writeln('========== 初始化工具 ==========');

        $tools = [
            [
                'tool_name' => 'sqlmap',
                'tool_label' => 'SQLMap',
                'tool_type' => 'sql_inject',
                'command' => 'sqlmap -u {target} --batch',
                'description' => 'SQL注入检测工具'
            ],
            [
                'tool_name' => 'xray',
                'tool_label' => 'XRay',
                'tool_type' => 'vuln_scan',
                'command' => 'xray webscan --url {target} --html-output',
                'description' => '通用漏洞扫描工具'
            ]
        ];

        $count = 0;
        foreach ($tools as $tool) {
            $exists = ScanTool::where('tool_name', $tool['tool_name'])->find();
            if (!$exists) {
                ScanTool::create($tool);
                $output->writeln("  + {$tool['tool_label']} ({$tool['tool_name']})");
                $count++;
            } else {
                $output->writeln("  = {$tool['tool_label']} 已存在");
            }
        }

        $output->writeln("初始化完成，新增 {$count} 个工具");
        return 0;
    }

    /**
     * 列出可用工具
     */
    protected function listTools(Output $output): int
    {
        $output->writeln('========== 可用工具列表 ==========');

        $tools = ScanTool::where('is_enabled', 1)->select();

        if ($tools->isEmpty()) {
            $output->writeln('暂无工具，请先执行 --init 初始化');
            return 0;
        }

        foreach ($tools as $tool) {
            $status = $tool->is_enabled ? '✓' : '✗';
            $output->writeln(sprintf(
                "  [%s] %-10s %-15s %s",
                $status,
                $tool->tool_name,
                "({$tool->tool_label})",
                $tool->description
            ));
        }

        $output->writeln('');
        $output->writeln("共 {$tools->count()} 个工具");
        return 0;
    }

    /**
     * 执行扫描工作流
     */
    protected function runScan(string $target, ?string $toolName, Output $output): int
    {
        $output->writeln('========== 开始扫描工作流 ==========');
        $output->writeln('');

        // Step 1: 添加目标
        $output->writeln('[Step 1] 添加目标...');
        $targetModel = ScanTarget::addOrUpdate($target);
        $output->writeln("  目标ID: {$targetModel->id}, URL: {$target}");
        $output->writeln('');

        // Step 2: 获取工具
        $output->writeln('[Step 2] 获取工具...');
        if (!$toolName) {
            $toolName = 'sqlmap'; // 默认工具
            $output->writeln("  未指定工具，使用默认: sqlmap");
        }

        $tool = ScanTool::getByName($toolName);
        if (!$tool) {
            $output->error("  工具 {$toolName} 不存在");
            return 1;
        }
        $output->writeln("  工具: {$tool->tool_label} ({$tool->tool_name}) - {$tool->description}");
        $output->writeln('');

        // Step 3: 创建任务
        $output->writeln('[Step 3] 创建扫描任务...');
        $task = ScanTask::createTask($targetModel->id, $tool->id);
        $output->writeln("  任务ID: {$task->id}");
        $output->writeln('');

        // Step 4: 执行扫描
        $output->writeln('[Step 4] 执行扫描...');
        $task->start();

        $scanResult = $this->executeToolScan($tool, $target, $task->id, $output);

        $task->complete($scanResult['count'], $scanResult['output']);
        $output->writeln("  扫描完成，发现 {$scanResult['count']} 个结果");
        $output->writeln('');

        // Step 5: LLM分析
        $output->writeln('[Step 5] LLM分析...');
        $this->performLlmAnalysis($task->id, $output);
        $output->writeln('');

        $output->writeln('========== 工作流完成 ==========');
        return 0;
    }

    /**
     * 执行工具扫描
     */
    protected function executeToolScan(ScanTool $tool, string $target, int $taskId, Output $output): array
    {
        $command = str_replace('{target}', escapeshellarg($target), $tool->command);
        $output->writeln("  执行命令: {$command}");

        // 检查工具是否可用
        $toolBinary = explode(' ', $tool->command)[0];
        $toolAvailable = $this->checkToolAvailable($toolBinary);

        if (!$toolAvailable) {
            $output->writeln("  警告: {$toolBinary} 未安装，模拟扫描结果");
            return $this->simulateScan($taskId, $tool);
        }

        // 实际执行扫描
        $resultCount = 0;
        $exitCode = 0;
        $outputLines = [];

        exec($command . ' 2>&1', $outputLines, $exitCode);

        // 保存原始输出
        $rawOutput = implode("\n", $outputLines);

        foreach ($outputLines as $line) {
            // 解析输出并存储结果
            $parsed = $this->parseToolOutput($line, $tool);
            if ($parsed) {
                ScanResult::addResult($taskId, $parsed);
                $resultCount++;
            }
        }

        // 如果没有解析到结果，添加一个完成标记
        if ($resultCount === 0) {
            ScanResult::addResult($taskId, [
                'vuln_level' => 'info',
                'vuln_type' => 'scan_complete',
                'vuln_detail' => $exitCode === 0 ? '扫描完成，未发现漏洞' : '扫描完成，请检查输出'
            ]);
        }

        return ['count' => $resultCount, 'output' => $rawOutput];
    }

    /**
     * 检查工具是否可用
     */
    protected function checkToolAvailable(string $toolBinary): bool
    {
        exec("which {$toolBinary} 2>/dev/null", $output, $exitCode);
        return $exitCode === 0;
    }

    /**
     * 模拟扫描（工具未安装时）
     */
    protected function simulateScan(int $taskId, ScanTool $tool): array
    {
        ScanResult::addResult($taskId, [
            'vuln_level' => 'info',
            'vuln_type' => 'simulation',
            'vuln_detail' => "模拟扫描: {$tool->tool_label} 工具未安装"
        ]);

        return ['count' => 1, 'output' => "模拟扫描: {$tool->tool_label} 工具未安装"];
    }

    /**
     * 解析工具输出
     */
    protected function parseToolOutput(string $line, ScanTool $tool): ?array
    {
        // SQLMap 输出解析
        if ($tool->tool_name === 'sqlmap') {
            if (stripos($line, 'injectable') !== false) {
                return [
                    'vuln_level' => 'high',
                    'vuln_type' => 'sql_injection',
                    'vuln_detail' => $line
                ];
            }
            if (stripos($line, 'Parameter') !== false && stripos($line, 'vulnerable') !== false) {
                return [
                    'vuln_level' => 'critical',
                    'vuln_type' => 'sql_injection',
                    'vuln_detail' => $line
                ];
            }
        }

        // XRay 输出解析
        if ($tool->tool_name === 'xray') {
            if (preg_match('/"plugin"\s*:\s*"([^"]+)"/', $line, $matches)) {
                $plugin = $matches[1];
                return [
                    'vuln_level' => 'high',
                    'vuln_type' => $plugin,
                    'vuln_detail' => $line
                ];
            }
        }

        return null;
    }

    /**
     * 执行LLM分析
     */
    protected function performLlmAnalysis(int $taskId, Output $output): void
    {
        $counts = ScanResult::countByLevel($taskId);
        $riskLevel = LlmAnalysis::calculateRiskLevel($counts);

        // 生成分析摘要
        $summary = sprintf(
            '统计结果: 严重 %d 个，高危 %d 个，中危 %d 个，低危 %d 个',
            $counts['critical'],
            $counts['high'],
            $counts['medium'],
            $counts['low']
        );

        // 生成修复建议
        $suggestions = $this->generateFixSuggestions($counts);

        LlmAnalysis::createAnalysis($taskId, [
            'risk_level' => $riskLevel,
            'critical_count' => $counts['critical'],
            'high_count' => $counts['high'],
            'medium_count' => $counts['medium'],
            'low_count' => $counts['low'],
            'analysis_summary' => $summary,
            'fix_suggestion' => $suggestions,
            'llm_model' => 'local-analysis'
        ]);

        $riskLabels = [
            'critical' => '严重',
            'high' => '高危',
            'medium' => '中危',
            'low' => '低危',
            'none' => '无风险'
        ];

        $output->writeln("  风险等级: " . ($riskLabels[$riskLevel] ?? $riskLevel));
        $output->writeln("  分析结果: {$summary}");
        $output->writeln("  修复建议: " . mb_substr($suggestions, 0, 50) . '...');
    }

    /**
     * 生成修复建议
     */
    protected function generateFixSuggestions(array $counts): string
    {
        $suggestions = [];

        if ($counts['critical'] > 0 || $counts['high'] > 0) {
            $suggestions[] = '1. 立即修复高危/严重漏洞，建议进行代码审计';
        }
        if ($counts['medium'] > 0) {
            $suggestions[] = '2. 中危漏洞建议在下一个迭代中修复';
        }
        if ($counts['low'] > 0) {
            $suggestions[] = '3. 低危漏洞可纳入技术债务清单';
        }

        $suggestions[] = '建议定期进行安全扫描和代码审计';
        $suggestions[] = '对用户输入进行严格的过滤和验证';
        $suggestions[] = '使用参数化查询防止SQL注入';

        return implode("\n", $suggestions);
    }

    /**
     * 查看任务列表
     */
    protected function listTasks(Output $output): int
    {
        $output->writeln('========== 任务列表 ==========');

        $tasks = ScanTask::with(['target', 'tool'])
            ->order('id', 'desc')
            ->limit(20)
            ->select();

        if ($tasks->isEmpty()) {
            $output->writeln('暂无任务');
            return 0;
        }

        $statusColors = [
            'pending' => 'yellow',
            'running' => 'blue',
            'success' => 'green',
            'failed' => 'red'
        ];

        foreach ($tasks as $task) {
            $status = $task->task_status;
            $target = $task->target ? $task->target->target : 'N/A';
            $tool = $task->tool ? $task->tool->tool_label : 'N/A';

            $output->writeln(sprintf(
                "  #%d | %-8s | %-10s | %s | 结果: %d",
                $task->id,
                $status,
                $tool,
                mb_substr($target, 0, 40),
                $task->result_count
            ));
        }

        $output->writeln('');
        $output->writeln("共 {$tasks->count()} 个任务");
        return 0;
    }

    /**
     * 查看扫描结果
     */
    protected function showResults(?string $taskId, Output $output): int
    {
        $output->writeln('========== 扫描结果 ==========');

        $query = ScanResult::with(['task']);

        if ($taskId) {
            $query->where('task_id', (int)$taskId);
        }

        $results = $query->order('id', 'desc')
            ->limit(50)
            ->select();

        if ($results->isEmpty()) {
            $output->writeln('暂无扫描结果');
            return 0;
        }

        $levelLabels = [
            'critical' => '严重',
            'high' => '高危',
            'medium' => '中危',
            'low' => '低危',
            'info' => '信息'
        ];

        foreach ($results as $result) {
            $level = $levelLabels[$result->vuln_level] ?? $result->vuln_level;
            $output->writeln(sprintf(
                "  #%d | [%s] %s | %s",
                $result->id,
                $level,
                $result->vuln_type ?? '未知',
                mb_substr($result->vuln_detail ?? '', 0, 50)
            ));
        }

        $output->writeln('');
        $output->writeln("共 {$results->count()} 条结果");
        return 0;
    }

    /**
     * 执行LLM分析
     */
    protected function runAnalyze(?string $taskId, Output $output): int
    {
        $output->writeln('========== LLM分析 ==========');

        $query = ScanTask::with(['results', 'target']);

        if ($taskId) {
            $query->where('id', (int)$taskId);
        }

        $tasks = $query->order('id', 'desc')
            ->limit(10)
            ->select();

        if ($tasks->isEmpty()) {
            $output->writeln('暂无可分析的任务');
            return 0;
        }

        foreach ($tasks as $task) {
            // 检查是否已有分析结果
            $existing = LlmAnalysis::where('task_id', $task->id)->find();
            if ($existing) {
                $output->writeln("  任务 #{$task->id} 已有分析结果，跳过");
                continue;
            }

            $output->writeln("  分析任务 #{$task->id}...");
            $this->performLlmAnalysis($task->id, $output);
        }

        return 0;
    }
}

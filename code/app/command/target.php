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
            ->addOption('list', 'l', Option::VALUE_NONE, '列出可用工具')
            ->addOption('target', 't', Option::VALUE_OPTIONAL, '扫描目标URL/IP')
            ->addOption('scan', 's', Option::VALUE_OPTIONAL, '指定扫描工具')
            ->addOption('tasks', null, Option::VALUE_NONE, '查看任务列表')
            ->addOption('status', null, Option::VALUE_OPTIONAL, '查看任务状态(可选指定任务ID)')
            ->addOption('results', 'r', Option::VALUE_OPTIONAL, '查看扫描结果(可选指定任务ID)')
            ->addOption('analyze', 'a', Option::VALUE_OPTIONAL, '执行LLM分析(可选指定任务ID)')
            ->setDescription('安全扫描工具平台');
    }

    protected function execute(Input $input, Output $output)
    {
        try {
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

            // 查看任务状态
            $argv = $_SERVER['argv'] ?? [];
            if (in_array('--status', $argv)) {
                $taskId = $input->getOption('status');
                return $this->showStatus($taskId, $output);
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
            $output->writeln('  php think scan --list              列出可用工具');
            $output->writeln('  php think scan -t "http://example.com?id=1" -s <工具名>');
            $output->writeln('  php think scan --tasks             查看任务列表');
            $output->writeln('  php think scan --status <任务ID>   查看任务状态');
            $output->writeln('  php think scan --results [任务ID]  查看扫描结果');
            $output->writeln('  php think scan --analyze [任务ID]  执行LLM分析');

        } catch (\Exception $e) {
            $output->error('错误: ' . $e->getMessage());
            return 1;
        }

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
            $output->writeln('暂无工具，请先添加工具');
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
     * 执行扫描工作流（回调模式）
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
            $tool = ScanTool::getDefaultTool();
            if (!$tool) {
                $output->error("  没有可用的工具，请先添加工具");
                return 1;
            }
            $output->writeln("  未指定工具，使用默认: {$tool->tool_name}");
        } else {
            $tool = ScanTool::getByName($toolName);
            if (!$tool) {
                $output->error("  工具 {$toolName} 不存在");
                return 1;
            }
        }
        $output->writeln("  工具: {$tool->tool_label} ({$tool->tool_name}) - {$tool->description}");
        $output->writeln('');

        // Step 3: 创建任务
        $output->writeln('[Step 3] 创建扫描任务...');
        $task = ScanTask::createTask($targetModel->id, $tool->id);
        $output->writeln("  任务ID: {$task->id}");
        $output->writeln('');

        // Step 4: 启动异步扫描（回调模式）
        $output->writeln('[Step 4] 启动异步扫描...');

        // 检查是否配置了回调模式的脚本
        if (!empty($tool->start_command) && !empty($tool->script_code)) {
            // 使用新的回调模式
            $runner = new \app\service\TaskRunner();
            $started = $runner->start($task->id);

            if (!$started) {
                $output->error("  任务启动失败");
                $task->fail('任务启动失败');
                return 1;
            }

            $output->writeln("  任务已启动，等待插件回调...");
            $output->writeln("  任务ID: {$task->id}");
            $output->writeln('');
            $output->writeln('使用以下命令查询结果:');
            $output->writeln("  php think scan --results {$task->id}");
            $output->writeln("  php think scan --status {$task->id}");
        } else {
            // 兼容旧模式：同步执行
            $output->writeln("  使用同步模式执行...");
            $task->start();

            $scanResult = $this->executeToolScan($tool, $target, $task->id, $output);

            $task->complete($scanResult['count'], $scanResult['output']);
            $output->writeln("  扫描完成，发现 {$scanResult['count']} 个结果");
            $output->writeln('');

            // Step 5: LLM分析
            $output->writeln('[Step 5] LLM分析...');
            $this->performLlmAnalysis($task->id, $output);
            $output->writeln('');
        }

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
            // 解析输出并存储结果（使用数据库配置的解析规则）
            $parsed = $tool->parseOutput($line);
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

        $statusLabels = [
            'pending' => '等待中',
            'running' => '运行中',
            'success' => '成功',
            'failed' => '失败'
        ];

        foreach ($tasks as $task) {
            $status = $statusLabels[$task->task_status] ?? $task->task_status;
            $target = $task->target ? $task->target->target : 'N/A';
            $tool = $task->tool ? $task->tool->tool_label : 'N/A';
            $progress = $task->progress ?? 0;

            $output->writeln(sprintf(
                "  #%d | %-6s | %3d%% | %-10s | %s | 结果: %d",
                $task->id,
                $status,
                $progress,
                $tool,
                mb_substr($target, 0, 30),
                $task->result_count
            ));
        }

        $output->writeln('');
        $output->writeln("共 {$tasks->count()} 个任务");
        return 0;
    }

    /**
     * 查看任务状态
     */
    protected function showStatus(?string $taskId, Output $output): int
    {
        $output->writeln('========== 任务状态 ==========');

        if (!$taskId) {
            $output->error('请指定任务ID: php think scan --status <任务ID>');
            return 1;
        }

        $task = ScanTask::with(['target', 'tool'])->find((int)$taskId);
        if (!$task) {
            $output->error("任务 #{$taskId} 不存在");
            return 1;
        }

        $statusLabels = [
            'pending' => '等待中',
            'running' => '运行中',
            'success' => '成功',
            'failed' => '失败'
        ];

        $status = $statusLabels[$task->task_status] ?? $task->task_status;
        $target = $task->target ? $task->target->target : 'N/A';
        $tool = $task->tool ? $task->tool->tool_name : 'N/A';

        $output->writeln("  任务ID:     {$task->id}");
        $output->writeln("  状态:       {$status}");
        $output->writeln("  进度:       " . ($task->progress ?? 0) . "%");
        $output->writeln("  目标:       {$target}");
        $output->writeln("  工具:       {$tool}");
        $output->writeln("  结果数:     {$task->result_count}");
        $output->writeln("  消息:       " . ($task->message ?? '-'));
        $output->writeln("  开始时间:   " . ($task->start_time ?? '-'));
        $output->writeln("  结束时间:   " . ($task->end_time ?? '-'));

        // 如果任务完成，显示结果统计
        if ($task->task_status === 'success') {
            $counts = ScanResult::countByLevel($task->id);
            $output->writeln('');
            $output->writeln('  结果统计:');
            $output->writeln("    严重: {$counts['critical']}  高危: {$counts['high']}  中危: {$counts['medium']}  低危: {$counts['low']}  信息: {$counts['info']}");
        }

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

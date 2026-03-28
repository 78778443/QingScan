<?php
declare(strict_types=1);

namespace app\service;

use app\model\ScanTask;
use app\model\ScanTool;
use app\model\ScanTarget;

/**
 * 任务启动服务
 */
class TaskRunner
{
    /**
     * 脚本临时目录
     */
    protected string $scriptBaseDir = '/tmp/scan_scripts';

    /**
     * 启动任务
     */
    public function start(int $taskId): bool
    {
        $task = ScanTask::find($taskId);
        if (!$task) {
            return false;
        }

        $tool = ScanTool::find($task->tool_id);
        if (!$tool || empty($tool->start_command)) {
            return false;
        }

        $target = ScanTarget::find($task->target_id);
        if (!$target) {
            return false;
        }

        // 构建回调URL
        $callbackUrl = $this->getCallbackUrl();

        // 判断模式：有script_code是脚本模式，否则是命令模式(Docker等)
        $scriptPath = '';
        $workingDir = '/tmp';

        if (!empty($tool->script_code)) {
            // 脚本模式：写入脚本到磁盘
            $scriptDir = $this->scriptBaseDir . '/task_' . $taskId;
            if (!is_dir($scriptDir)) {
                mkdir($scriptDir, 0755, true);
            }

            // 解码脚本代码（数据库存储的是base64编码）
            $scriptCode = base64_decode($tool->script_code);

            // 写入脚本到磁盘
            $scriptPath = $scriptDir . '/runner.py';
            file_put_contents($scriptPath, $scriptCode);
            chmod($scriptPath, 0755);

            $workingDir = $scriptDir;
        }

        // 构建启动命令
        $command = $this->buildCommand($tool->start_command, [
            'task_id' => (string)$taskId,
            'target' => $target->target,
            'callback_url' => $callbackUrl,
            'script_path' => $scriptPath,
        ]);

        // 更新任务状态为运行中
        $task->start();

        // 后台执行命令
        $this->executeAsync($command, $workingDir);

        return true;
    }

    /**
     * 构建命令
     */
    protected function buildCommand(string $template, array $vars): string
    {
        $command = $template;

        foreach ($vars as $key => $value) {
            $placeholder = '{' . $key . '}';
            // 对 target 进行转义
            if ($key === 'target') {
                $value = escapeshellarg($value);
            }
            $command = str_replace($placeholder, $value, $command);
        }

        return $command;
    }

    /**
     * 异步执行命令
     */
    protected function executeAsync(string $command, string $workingDir): void
    {
        // 构建后台执行命令
        $fullCommand = sprintf(
            'cd %s && nohup %s > /dev/null 2>&1 &',
            escapeshellarg($workingDir),
            $command
        );

        // 执行
        exec($fullCommand);
    }

    /**
     * 获取回调URL
     */
    protected function getCallbackUrl(): string
    {
        $baseUrl = config('app.callback_base_url') ?: config('app.url') ?: 'http://localhost';

        return rtrim($baseUrl, '/') . '/api/callback';
    }

    /**
     * 清理脚本文件
     */
    public function cleanup(int $taskId): void
    {
        $scriptDir = $this->scriptBaseDir . '/task_' . $taskId;
        if (is_dir($scriptDir)) {
            exec('rm -rf ' . escapeshellarg($scriptDir));
        }
    }
}

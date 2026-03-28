<?php
declare(strict_types=1);

namespace app\controller;

use app\BaseController;
use app\model\ScanTask;
use app\model\ScanTool;
use app\model\ScanTarget;
use app\service\TaskRunner;
use think\response\Json;

/**
 * 任务控制器
 */
class Task extends BaseController
{
    /**
     * 创建并启动扫描任务
     * POST /api/task/create
     */
    public function create(): Json
    {
        $data = $this->request->post();

        // 验证参数
        $this->validate($data, [
            'target' => 'require',
            'tool_id' => 'require|integer',
        ]);

        // 检查工具是否存在
        $tool = ScanTool::find($data['tool_id']);
        if (!$tool || !$tool->is_enabled) {
            return json(['success' => false, 'message' => '工具不存在或未启用'], 400);
        }

        // 创建或获取目标
        $targetType = $this->detectTargetType($data['target']);
        $target = ScanTarget::addOrUpdate($data['target'], $targetType);

        // 创建任务
        $task = ScanTask::create([
            'target_id' => $target->id,
            'tool_id' => $tool->id,
            'task_status' => ScanTask::STATUS_PENDING,
        ]);

        // 启动任务
        $runner = new TaskRunner();
        $started = $runner->start($task->id);

        if (!$started) {
            $task->fail();
            return json(['success' => false, 'message' => '任务启动失败'], 500);
        }

        return json([
            'success' => true,
            'data' => [
                'task_id' => $task->id,
                'status' => 'running',
            ],
        ]);
    }

    /**
     * 查询任务状态
     * GET /api/task/status
     */
    public function status(): Json
    {
        $taskId = $this->request->get('task_id');

        if (!$taskId) {
            return json(['success' => false, 'message' => '缺少task_id参数'], 400);
        }

        $task = ScanTask::with(['tool', 'target'])->find($taskId);
        if (!$task) {
            return json(['success' => false, 'message' => '任务不存在'], 404);
        }

        // 获取结果统计
        $resultStats = \app\model\ScanResult::countByLevel($task->id);

        return json([
            'success' => true,
            'data' => [
                'task_id' => $task->id,
                'status' => $task->task_status,
                'progress' => $task->progress ?? 0,
                'message' => $task->message ?? '',
                'target' => $task->target?->target,
                'tool' => $task->tool?->tool_name,
                'result_count' => $task->result_count,
                'result_stats' => $resultStats,
                'start_time' => $task->start_time,
                'end_time' => $task->end_time,
            ],
        ]);
    }

    /**
     * 获取任务结果列表
     * GET /api/task/results
     */
    public function results(): Json
    {
        $taskId = $this->request->get('task_id');

        if (!$taskId) {
            return json(['success' => false, 'message' => '缺少task_id参数'], 400);
        }

        $task = ScanTask::find($taskId);
        if (!$task) {
            return json(['success' => false, 'message' => '任务不存在'], 404);
        }

        $results = \app\model\ScanResult::where('task_id', $taskId)
            ->order('id', 'desc')
            ->select();

        return json([
            'success' => true,
            'data' => $results,
        ]);
    }

    /**
     * 检测目标类型
     */
    protected function detectTargetType(string $target): string
    {
        // URL
        if (preg_match('/^https?:\/\//i', $target)) {
            return ScanTarget::TYPE_URL;
        }

        // IP地址
        if (preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/', $target)) {
            return ScanTarget::TYPE_IP;
        }

        // 域名
        return ScanTarget::TYPE_DOMAIN;
    }
}

<?php
declare(strict_types=1);

namespace app\controller;

use app\BaseController;
use app\model\ScanTask;
use app\model\ScanResult;
use think\response\Json;

/**
 * 插件回调控制器
 */
class Callback extends BaseController
{
    /**
     * 状态更新接口
     * POST /api/callback/status
     */
    public function status(): Json
    {
        $data = $this->request->post();

        // 验证参数
        $this->validate($data, [
            'task_id' => 'require|integer',
            'status' => 'require|in:running,completed,failed',
        ]);

        $task = ScanTask::find($data['task_id']);
        if (!$task) {
            return json(['success' => false, 'message' => '任务不存在'], 404);
        }

        // 更新任务状态
        $updateData = [
            'task_status' => $data['status'],
        ];

        // 可选字段
        if (isset($data['progress'])) {
            $updateData['progress'] = min(100, max(0, (int)$data['progress']));
        }
        if (isset($data['message'])) {
            $updateData['message'] = $data['message'];
        }

        // 如果是完成或失败状态，设置结束时间
        if (in_array($data['status'], [ScanTask::STATUS_SUCCESS, ScanTask::STATUS_FAILED])) {
            $updateData['end_time'] = date('Y-m-d H:i:s');
        }

        $task->save($updateData);

        return json(['success' => true]);
    }

    /**
     * 结果提交接口
     * POST /api/callback/result
     */
    public function result(): Json
    {
        $data = $this->request->post();

        // 验证参数
        $this->validate($data, [
            'task_id' => 'require|integer',
            'status' => 'require|in:completed,failed',
        ]);

        $task = ScanTask::find($data['task_id']);
        if (!$task) {
            return json(['success' => false, 'message' => '任务不存在'], 404);
        }

        // 如果任务失败
        if ($data['status'] === 'failed') {
            $task->save([
                'task_status' => ScanTask::STATUS_FAILED,
                'end_time' => date('Y-m-d H:i:s'),
                'progress' => 100,
                'message' => $data['message'] ?? '任务执行失败',
            ]);
            return json(['success' => true]);
        }

        // 处理结果数据
        $results = $data['results'] ?? [];
        $savedCount = 0;

        foreach ($results as $result) {
            $saved = $this->saveResult($task->id, $result);
            if ($saved) {
                $savedCount++;
            }
        }

        // 更新任务状态
        $task->save([
            'task_status' => ScanTask::STATUS_SUCCESS,
            'end_time' => date('Y-m-d H:i:s'),
            'progress' => 100,
            'result_count' => $savedCount,
        ]);

        return json([
            'success' => true,
            'saved_count' => $savedCount,
        ]);
    }

    /**
     * 保存单个结果
     */
    protected function saveResult(int $taskId, array $result): bool
    {
        // 必填字段验证
        if (empty($result['vuln_type']) || empty($result['vuln_level'])) {
            return false;
        }

        // 等级映射
        $level = $this->normalizeLevel($result['vuln_level'] ?? 'info');

        // 创建结果
        ScanResult::create([
            'task_id' => $taskId,
            'vuln_level' => $level,
            'vuln_type' => $result['vuln_type'] ?? null,
            'vuln_title' => $result['vuln_title'] ?? null,
            'vuln_detail' => $result['vuln_detail'] ?? null,
            'vuln_url' => $result['vuln_url'] ?? null,
            'vuln_request' => $result['vuln_request'] ?? null,
            'vuln_response' => $result['vuln_response'] ?? null,
            'vuln_evidence' => $result['vuln_evidence'] ?? null,
            'is_fixed' => 0,
        ]);

        return true;
    }

    /**
     * 标准化漏洞等级
     */
    protected function normalizeLevel(string $level): string
    {
        $map = [
            'critical' => ScanResult::LEVEL_CRITICAL,
            'high' => ScanResult::LEVEL_HIGH,
            'medium' => ScanResult::LEVEL_MEDIUM,
            'moderate' => ScanResult::LEVEL_MEDIUM,
            'low' => ScanResult::LEVEL_LOW,
            'info' => ScanResult::LEVEL_INFO,
            'information' => ScanResult::LEVEL_INFO,
            'warning' => ScanResult::LEVEL_MEDIUM,
            'error' => ScanResult::LEVEL_HIGH,
        ];

        return $map[strtolower($level)] ?? ScanResult::LEVEL_INFO;
    }
}

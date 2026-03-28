<?php
declare(strict_types=1);

namespace app\controller;

use app\BaseController;
use app\model\ScanTask;
use app\model\ScanResult;
use think\response\Json;

/**
 * 插件回调控制器
 * 平台只做管道，不处理数据格式
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

        $this->validate($data, [
            'task_id' => 'require|integer',
            'status' => 'require|in:running,completed,failed',
        ]);

        $task = ScanTask::find($data['task_id']);
        if (!$task) {
            return json(['success' => false, 'message' => '任务不存在'], 404);
        }

        $updateData = ['task_status' => $data['status']];

        if (isset($data['progress'])) {
            $updateData['progress'] = min(100, max(0, (int)$data['progress']));
        }
        if (isset($data['message'])) {
            $updateData['message'] = $data['message'];
        }

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

        $this->validate($data, [
            'task_id' => 'require|integer',
            'status' => 'require|in:completed,failed',
        ]);

        $task = ScanTask::find($data['task_id']);
        if (!$task) {
            return json(['success' => false, 'message' => '任务不存在'], 404);
        }

        if ($data['status'] === 'failed') {
            $task->save([
                'task_status' => ScanTask::STATUS_FAILED,
                'end_time' => date('Y-m-d H:i:s'),
                'progress' => 100,
                'message' => $data['message'] ?? '任务执行失败',
            ]);
            return json(['success' => true]);
        }

        // 保存结果
        $savedCount = 0;
        $resultData = $data['data'] ?? null;
        $dataType = $data['data_type'] ?? 'raw';

        if ($resultData !== null) {
            ScanResult::create([
                'task_id' => $task->id,
                'data' => $this->stringify($resultData),
                'data_type' => $dataType,
            ]);
            $savedCount = 1;
        }

        $task->save([
            'task_status' => ScanTask::STATUS_SUCCESS,
            'end_time' => date('Y-m-d H:i:s'),
            'progress' => 100,
            'result_count' => $savedCount,
        ]);

        return json(['success' => true, 'saved_count' => $savedCount]);
    }

    /**
     * 转换为字符串
     */
    protected function stringify($data): string
    {
        if (is_array($data) || is_object($data)) {
            return json_encode($data, JSON_UNESCAPED_UNICODE);
        }
        return (string)$data;
    }
}

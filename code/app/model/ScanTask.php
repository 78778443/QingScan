<?php
declare(strict_types=1);

namespace app\model;

use think\Model;

/**
 * 扫描任务模型
 */
class ScanTask extends Model
{
    protected $name = 'scan_task';
    protected $pk = 'id';

    // 任务状态
    const STATUS_PENDING = 'pending';
    const STATUS_RUNNING = 'running';
    const STATUS_SUCCESS = 'success';
    const STATUS_FAILED = 'failed';

    /**
     * 关联目标
     */
    public function target()
    {
        return $this->belongsTo(ScanTarget::class, 'target_id');
    }

    /**
     * 关联工具
     */
    public function tool()
    {
        return $this->belongsTo(ScanTool::class, 'tool_id');
    }

    /**
     * 关联结果
     */
    public function results()
    {
        return $this->hasMany(ScanResult::class, 'task_id');
    }

    /**
     * 关联LLM分析
     */
    public function llmAnalysis()
    {
        return $this->hasOne(LlmAnalysis::class, 'task_id');
    }

    /**
     * 创建任务
     */
    public static function createTask(int $targetId, int $toolId): self
    {
        return self::create([
            'target_id' => $targetId,
            'tool_id' => $toolId,
            'task_status' => self::STATUS_PENDING,
            'progress' => 0,
        ]);
    }

    /**
     * 开始任务
     */
    public function start(): void
    {
        $this->task_status = self::STATUS_RUNNING;
        $this->start_time = date('Y-m-d H:i:s');
        $this->progress = 0;
        $this->save();
    }

    /**
     * 更新进度
     */
    public function updateProgress(int $progress, string $message = ''): void
    {
        $this->progress = min(100, max(0, $progress));
        if ($message) {
            $this->message = $message;
        }
        $this->save();
    }

    /**
     * 完成任务
     */
    public function complete(int $resultCount = 0, string $toolOutput = ''): void
    {
        $this->task_status = self::STATUS_SUCCESS;
        $this->end_time = date('Y-m-d H:i:s');
        $this->result_count = $resultCount;
        $this->tool_output = $toolOutput;
        $this->progress = 100;
        $this->save();
    }

    /**
     * 任务失败
     */
    public function fail(string $message = ''): void
    {
        $this->task_status = self::STATUS_FAILED;
        $this->end_time = date('Y-m-d H:i:s');
        if ($message) {
            $this->message = $message;
        }
        $this->save();
    }
}

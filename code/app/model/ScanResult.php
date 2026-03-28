<?php
declare(strict_types=1);

namespace app\model;

use think\Model;

/**
 * 扫描结果模型
 * 只存储原始数据，不解析格式
 */
class ScanResult extends Model
{
    protected $name = 'scan_result';
    protected $pk = 'id';

    /**
     * 关联任务
     */
    public function task()
    {
        return $this->belongsTo(ScanTask::class, 'task_id');
    }

    /**
     * 获取解析后的数据
     */
    public function getParsedData()
    {
        $decoded = json_decode($this->data, true);
        return $decoded !== null ? $decoded : $this->data;
    }

    /**
     * 统计任务结果数
     */
    public static function countByTask(int $taskId): int
    {
        return self::where('task_id', $taskId)->count();
    }
}

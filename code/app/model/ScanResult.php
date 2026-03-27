<?php
declare(strict_types=1);

namespace app\model;

use think\Model;

/**
 * 扫描结果模型
 */
class ScanResult extends Model
{
    protected $name = 'scan_result';
    protected $pk = 'id';

    // 漏洞等级
    const LEVEL_CRITICAL = 'critical';
    const LEVEL_HIGH = 'high';
    const LEVEL_MEDIUM = 'medium';
    const LEVEL_LOW = 'low';
    const LEVEL_INFO = 'info';

    /**
     * 关联任务
     */
    public function task()
    {
        return $this->belongsTo(ScanTask::class, 'task_id');
    }

    /**
     * 添加结果
     */
    public static function addResult(int $taskId, array $data): self
    {
        return self::create([
            'task_id' => $taskId,
            'vuln_level' => $data['vuln_level'] ?? self::LEVEL_INFO,
            'vuln_type' => $data['vuln_type'] ?? null,
            'vuln_detail' => $data['vuln_detail'] ?? null,
            'vuln_url' => $data['vuln_url'] ?? null,
            'is_fixed' => 0
        ]);
    }

    /**
     * 统计各等级漏洞数量
     */
    public static function countByLevel(int $taskId): array
    {
        $result = [
            'critical' => 0,
            'high' => 0,
            'medium' => 0,
            'low' => 0,
            'info' => 0
        ];

        $counts = self::where('task_id', $taskId)
            ->field('vuln_level, count(*) as count')
            ->group('vuln_level')
            ->select();

        foreach ($counts as $item) {
            $level = $item['vuln_level'];
            if (isset($result[$level])) {
                $result[$level] = (int)$item['count'];
            }
        }

        return $result;
    }
}

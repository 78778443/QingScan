<?php
declare(strict_types=1);

namespace app\model;

use think\Model;

/**
 * LLM分析结果模型
 */
class LlmAnalysis extends Model
{
    protected $name = 'llm_analysis';
    protected $pk = 'id';

    // 风险等级
    const RISK_CRITICAL = 'critical';
    const RISK_HIGH = 'high';
    const RISK_MEDIUM = 'medium';
    const RISK_LOW = 'low';
    const RISK_NONE = 'none';

    /**
     * 关联任务
     */
    public function task()
    {
        return $this->belongsTo(ScanTask::class, 'task_id');
    }

    /**
     * 创建分析结果
     */
    public static function createAnalysis(int $taskId, array $data): self
    {
        return self::create([
            'task_id' => $taskId,
            'risk_level' => $data['risk_level'] ?? self::RISK_NONE,
            'critical_count' => $data['critical_count'] ?? 0,
            'high_count' => $data['high_count'] ?? 0,
            'medium_count' => $data['medium_count'] ?? 0,
            'low_count' => $data['low_count'] ?? 0,
            'analysis_summary' => $data['analysis_summary'] ?? '',
            'fix_suggestion' => $data['fix_suggestion'] ?? '',
            'llm_model' => $data['llm_model'] ?? null
        ]);
    }

    /**
     * 根据漏洞统计计算风险等级
     */
    public static function calculateRiskLevel(array $counts): string
    {
        if ($counts['critical'] > 0) return self::RISK_CRITICAL;
        if ($counts['high'] > 0) return self::RISK_HIGH;
        if ($counts['medium'] > 0) return self::RISK_MEDIUM;
        if ($counts['low'] > 0) return self::RISK_LOW;
        return self::RISK_NONE;
    }
}

<?php
declare(strict_types=1);

namespace app\model;

use think\Model;

/**
 * 扫描目标模型
 */
class ScanTarget extends Model
{
    protected $name = 'scan_target';
    protected $pk = 'id';

    // 目标类型
    const TYPE_URL = 'url';
    const TYPE_IP = 'ip';
    const TYPE_DOMAIN = 'domain';

    /**
     * 添加或获取目标
     */
    public static function addOrUpdate(string $target, string $targetType = 'url'): self
    {
        $record = self::where('target', $target)->find();
        if ($record) {
            return $record;
        }
        return self::create([
            'target' => $target,
            'target_type' => $targetType,
            'status' => 1
        ]);
    }
}

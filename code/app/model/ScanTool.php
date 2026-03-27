<?php
declare(strict_types=1);

namespace app\model;

use think\Model;

/**
 * 扫描工具模型
 */
class ScanTool extends Model
{
    protected $name = 'scan_tool';
    protected $pk = 'id';

    // 工具类型常量
    const TYPE_SQL_INJECT = 'sql_inject';
    const TYPE_XSS = 'xss';
    const TYPE_VULN_SCAN = 'vuln_scan';
    const TYPE_FUZZ = 'fuzz';

    /**
     * 获取所有启用的工具
     */
    public static function getEnabledTools(): array
    {
        return self::where('is_enabled', 1)->select()->toArray();
    }

    /**
     * 根据工具名获取工具
     */
    public static function getByName(string $toolName): ?self
    {
        return self::where('tool_name', $toolName)->find();
    }
}

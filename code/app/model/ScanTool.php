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

    /**
     * 获取所有启用的工具
     */
    public static function getEnabledTools(): array
    {
        return self::where('is_enabled', 1)->select()->toArray();
    }

    /**
     * 获取第一个启用的工具（默认工具）
     */
    public static function getDefaultTool(): ?self
    {
        return self::where('is_enabled', 1)->order('id', 'asc')->find();
    }

    /**
     * 根据工具名获取工具
     */
    public static function getByName(string $toolName): ?self
    {
        return self::where('tool_name', $toolName)->find();
    }

    /**
     * 获取输出解析规则
     */
    public function getParseRules(): array
    {
        if (empty($this->output_parse)) {
            return [];
        }

        $data = json_decode($this->output_parse, true);
        return $data['rules'] ?? [];
    }

    /**
     * 根据解析规则解析输出行
     */
    public function parseOutput(string $line): ?array
    {
        $rules = $this->getParseRules();

        foreach ($rules as $rule) {
            $pattern = $rule['pattern'] ?? '';
            if (empty($pattern)) {
                continue;
            }

            if (preg_match('/' . $pattern . '/i', $line, $matches)) {
                $level = $rule['level'] ?? 'info';
                $type = $rule['type'] ?? 'unknown';

                // 替换 $1, $2 等捕获组
                if (preg_match('/^\$\d+$/', $level) && isset($matches[substr($level, 1)])) {
                    $level = strtolower($matches[substr($level, 1)]);
                    // 验证 level 是否合法
                    $validLevels = ['critical', 'high', 'medium', 'low', 'info'];
                    if (!in_array($level, $validLevels)) {
                        $level = 'info';
                    }
                }

                if (preg_match('/^\$\d+$/', $type) && isset($matches[substr($type, 1)])) {
                    $type = $matches[substr($type, 1)];
                }

                return [
                    'vuln_level' => $level,
                    'vuln_type' => $type,
                    'vuln_detail' => $line
                ];
            }
        }

        return null;
    }
}

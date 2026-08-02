<?php
/**
 * 自定义代码审计规则（L1 正则规则）
 * 结构：['id' => 规则唯一标识, 'ext' => ['php','java',...], 'pattern' => '#正则#i', 'message' => '描述', 'severity' => 'ERROR'|'WARNING']
 * 与内置规则合并生效，无需修改代码
 */
return [
    // 示例：检测 ThinkPHP input() 直接进入 SQL 查询
    // ['id' => 'custom.thinkphp.input-sql', 'ext' => ['php'], 'pattern' => '#(query|where)\s*\([^)]*input\(#i', 'message' => '自定义规则：input() 进入 SQL 查询，需参数化', 'severity' => 'ERROR'],
];

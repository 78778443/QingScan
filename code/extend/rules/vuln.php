<?php
/**
 * 自定义漏洞检测规则
 * 结构：['name' => '漏洞名称', 'severity' => 'low|medium|high|critical', 'paths' => ['/路径1','/路径2'], 'keywords' => ['匹配关键字'], 'description' => '描述']
 */
return [
    // 示例：检测敏感接口泄露
    // ['name' => '敏感接口泄露', 'severity' => 'high', 'paths' => ['/admin/api/config'], 'keywords' => ['access_key', 'secret'], 'description' => '自定义规则：检测敏感配置接口'],
];

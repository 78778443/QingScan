<?php
/**
 * 自定义污点分析规则（L2 语法感知）
 * 结构：['id' => '规则标识', 'sinks' => ['函数名1','函数名2'], 'message' => '描述', 'severity' => 'ERROR'|'WARNING']
 */
return [
    // 示例：检测用户输入流入自定义加密函数
    // ['id' => 'custom.taint.crypto', 'sinks' => ['my_encrypt'], 'message' => '自定义规则：用户输入流入 my_encrypt()', 'severity' => 'WARNING'],
];

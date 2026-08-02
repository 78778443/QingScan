<?php
/**
 * 自定义 WAF 识别规则
 * 结构：['headers' => ['响应头名' => '值(可选)'], 'cookie' => ['cookie名'], 'body' => ['页面关键字'], 'firewall' => 'WAF名称', 'manufacturer' => '厂商']
 */
return [
    // 示例：识别自建 WAF
    // ['headers' => ['x-corp-waf' => ''], 'cookie' => [], 'body' => [], 'firewall' => 'corp-waf', 'manufacturer' => '公司自研WAF'],
];

<?php
/**
 * 自定义指纹识别规则
 * 结构：['name' => '指纹名称', 'headers' => ['响应头名' => '值(可选)'], 'body' => ['页面关键字']]
 * headers 值留空表示只要头存在即命中；body 任一关键字命中即识别
 */
return [
    // 示例：识别公司内部系统
    // ['name' => '公司门户', 'headers' => ['x-corp' => ''], 'body' => ['corp-portal']],
];

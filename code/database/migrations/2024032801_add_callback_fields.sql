-- 工具回调方案数据库迁移
-- 执行时间: 2024-03-28

-- 1. scan_tool 表添加字段
ALTER TABLE `scan_tool`
ADD COLUMN `start_command` VARCHAR(1000) NULL COMMENT '启动命令模板，支持变量: {task_id}, {target}, {callback_url}, {script_path}' AFTER `command`,
ADD COLUMN `script_code` MEDIUMTEXT NULL COMMENT '脚本代码（Base64编码存储），运行时解码写入磁盘执行' AFTER `start_command`;

-- 2. scan_task 表添加字段
ALTER TABLE `scan_task`
ADD COLUMN `progress` TINYINT UNSIGNED DEFAULT 0 COMMENT '进度 0-100' AFTER `task_status`,
ADD COLUMN `message` VARCHAR(500) NULL COMMENT '状态消息' AFTER `progress`;

-- 3. scan_result 表添加字段
ALTER TABLE `scan_result`
ADD COLUMN `vuln_title` VARCHAR(255) NULL COMMENT '漏洞标题' AFTER `vuln_type`,
ADD COLUMN `vuln_request` TEXT NULL COMMENT '请求包' AFTER `vuln_detail`,
ADD COLUMN `vuln_response` TEXT NULL COMMENT '响应包' AFTER `vuln_request`,
ADD COLUMN `vuln_evidence` TEXT NULL COMMENT '证据' AFTER `vuln_response`;

-- 4. 添加索引
ALTER TABLE `scan_task`
ADD INDEX `idx_task_status` (`task_status`),
ADD INDEX `idx_start_time` (`start_time`);

ALTER TABLE `scan_result`
ADD INDEX `idx_vuln_level` (`vuln_level`),
ADD INDEX `idx_vuln_type` (`vuln_type`);

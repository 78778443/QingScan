SET FOREIGN_KEY_CHECKS=0;


ALTER TABLE `github_keyword_monitor` ADD COLUMN `app_id` int(11) NOT NULL DEFAULT 0 AFTER `scan_time`;
ALTER TABLE `github_keyword_monitor_notice` ADD COLUMN `app_id` int(10) NOT NULL DEFAULT 0 AFTER `create_time`;
ALTER TABLE `plugin` ADD COLUMN `type` int(2) NOT NULL DEFAULT 1 COMMENT '1执行插件扫描   2结果分析' AFTER `scan_type`;
ALTER TABLE `plugin_scan_log` ADD COLUMN `file_content` varchar(5000) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL AFTER `log_type`;
ALTER TABLE `plugin_scan_log` ADD COLUMN `is_read` tinyint(1) NOT NULL DEFAULT 1 COMMENT '结果是否已读取   1未读  2已读取' AFTER `file_content`;
ALTER TABLE `plugin_scan_log` MODIFY COLUMN `plugin_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL COMMENT '插件名称' AFTER `check_status`;

INSERT INTO `auth_rule`( `href`, `title`, `is_delete`, `is_open_auth`, `pid`, `sort`, `created_at`, `menu_status`, `update_time`, `level`, `delete_time`, `icon_url`) VALUES ( 'zhiwen/index', '指纹列表', 0, 1, 35, 11, 1643338608, 1, 0, 2, 0, '');
INSERT INTO `auth_rule`( `href`, `title`, `is_delete`, `is_open_auth`, `pid`, `sort`, `created_at`, `menu_status`, `update_time`, `level`, `delete_time`, `icon_url`) VALUES ( 'backup/backup', '未知', 0, 1, 43, 0, 1643091277, 1, 0, 3, 0, '');
INSERT INTO `auth_rule`( `href`, `title`, `is_delete`, `is_open_auth`, `pid`, `sort`, `created_at`, `menu_status`, `update_time`, `level`, `delete_time`, `icon_url`) VALUES ( 'user_log/index', '登录日志', 0, 1, 23, 9, 1643018839, 1, 0, 2, 0, '');
INSERT INTO `auth_rule`( `href`, `title`, `is_delete`, `is_open_auth`, `pid`, `sort`, `created_at`, `menu_status`, `update_time`, `level`, `delete_time`, `icon_url`) VALUES ( 'backup/index', '数据备份', 0, 1, 23, 8, 1642854435, 1, 0, 2, 0, '');
INSERT INTO `auth_rule`( `href`, `title`, `is_delete`, `is_open_auth`, `pid`, `sort`, `created_at`, `menu_status`, `update_time`, `level`, `delete_time`, `icon_url`) VALUES ( 'github_keyword_monitor_notice/index', 'github关键词监控结果', 0, 1, 35, 8, 1642852575, 1, 0, 2, 0, '');
INSERT INTO `auth_rule` (`auth_rule_id`,`href`, `title`, `is_delete`, `is_open_auth`, `pid`, `sort`, `created_at`, `menu_status`, `update_time`, `level`, `delete_time`, `icon_url`) VALUES (333,'', '插件中心', 0, 1, 0, 8, 1642257783, 1, 0, 1, 0, '');


INSERT INTO `system_config` (`name`, `key`, `value`, `is_delete`) VALUES ('暂停扫描', 'maxProcesses', '1', 0);
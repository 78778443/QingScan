DROP TABLE IF EXISTS `mobsfscan`;
CREATE TABLE `mobsfscan` (
                             `id` int(10) NOT NULL AUTO_INCREMENT,
                             `code_id` int(10) NOT NULL DEFAULT '0',
                             `user_id` int(10) NOT NULL DEFAULT '0',
                             `type` varchar(255) NOT NULL DEFAULT '',
                             `files` text,
                             `cwe` varchar(255) NOT NULL DEFAULT '',
                             `description` varchar(500) NOT NULL DEFAULT '',
                             `input_case` varchar(255) NOT NULL DEFAULT '',
                             `masvs` varchar(255) NOT NULL DEFAULT '',
                             `owasp_mobile` varchar(255) NOT NULL DEFAULT '',
                             `reference` varchar(500) NOT NULL DEFAULT '',
                             `severity` varchar(255) NOT NULL DEFAULT '',
                             `create_time` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
                             `check_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0未审核  1有效漏洞  2无效漏洞',
                             PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

ALTER TABLE `code_composer` MODIFY COLUMN `dist` text;
ALTER TABLE `code_composer` MODIFY COLUMN `authors` text;
ALTER TABLE `code_composer` MODIFY COLUMN `funding` text;
ALTER TABLE `code_composer` MODIFY COLUMN `require_dev` text;
ALTER TABLE `code_composer` MODIFY COLUMN `keywords` text;
ALTER TABLE `code_composer` MODIFY COLUMN `require` text;
ALTER TABLE `plugin_scan_log` ADD COLUMN `is_custom` tinyint(10) NOT NULL DEFAULT 1 COMMENT '是否为自定义插件 1否  2是';
ALTER TABLE `code` ADD COLUMN `is_online` int(10) NOT NULL DEFAULT 1 COMMENT '1线上   2本地';
ALTER TABLE `code` ADD COLUMN `mobsfscan_scan_time` datetime(0) NOT NULL DEFAULT '2000-01-01 00:00:00';
ALTER TABLE `code` ADD COLUMN `project_type` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1php 2java 3python 4go 5app 6其他';
INSERT INTO `auth_rule` (`href`, `title`, `is_delete`, `is_open_auth`, `pid`, `sort`, `created_at`, `menu_status`, `update_time`, `level`, `delete_time`, `icon_url`) VALUES ('mobsfscan/index', 'mobsfscan列表', 0, 1, 14, 4, 1652079904, 1, 1652079930, 2, 0, '');
INSERT INTO `process_safe` (`id`,`key`, `value`, `status`, `note`, `update_time`, `type`) VALUES (42,'scan mobsfscan', 'cd /root/qingscan/code  &&  php think scan mobsfscan>> /tmp/mobsfscan.txt & ', 0, 'mobsfscan代码审计(app)', '2022-05-10 11:09:24', 1);
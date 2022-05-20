ALTER TABLE `code` ADD COLUMN `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1启用 2禁用';
ALTER TABLE `code` ADD COLUMN `murphysec_scan_time` datetime(0) NOT NULL DEFAULT '2000-01-01 00:00:00';
INSERT INTO `auth_rule` (`href`, `title`, `is_delete`, `is_open_auth`, `pid`, `sort`, `created_at`, `menu_status`, `update_time`, `level`, `delete_time`, `icon_url`) VALUES ('murphysec/index', 'murphysec列表', 0, 1, 14, 4, 1652861447, 1, 1652861478, 2, 0, '');
ALTER TABLE `log` CHANGE `content` `content` text;
INSERT INTO `process_safe` VALUES (43, 'scan murphysecScan', 'cd /root/qingscan/code  &&  php think scan murphysecScan>> /tmp/murphysecScan.txt & ', 1, '开源组件漏洞扫描工具', '2022-05-19 16:35:14', 1);
update process_safe set note='subdomain子域名扫描' where id='16';
INSERT INTO `system_config` (`name`, `key`, `value`, `is_delete`) VALUES ('墨菲安全token', 'murphysec_token', 'xxxxxxxxxxxxxxxxxx', 0);
update system_config set name='最大进程数量' where key='maxProcesses';

DROP TABLE IF EXISTS `project_tools`;
CREATE TABLE `project_tools` (
                                 `id` int(10) NOT NULL AUTO_INCREMENT,
                                 `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1app 2code',
                                 `project_id` int(10) NOT NULL DEFAULT '9' COMMENT '项目id',
                                 `tools_name` varchar(50) NOT NULL DEFAULT '' COMMENT '工具名称',
                                 `create_time` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
                                 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `murphysec`;
CREATE TABLE `murphysec` (
                             `id` int(10) NOT NULL AUTO_INCREMENT,
                             `user_id` int(10) NOT NULL DEFAULT '0',
                             `code_id` int(10) DEFAULT '0',
                             `comp_name` varchar(100) NOT NULL DEFAULT '' COMMENT '缺陷组件名称',
                             `version` varchar(50) NOT NULL DEFAULT '' COMMENT '当前版本',
                             `min_fixed_version` varchar(50) NOT NULL COMMENT '最小修复版本',
                             `show_level` tinyint(1) NOT NULL COMMENT '修复建议等级 1强烈建议修复 2建议修复 3可选修复',
                             `language` varchar(20) NOT NULL DEFAULT '' COMMENT '语言',
                             `solutions` text COMMENT '修复方案',
                             `repair_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '修复状态 1未修复 2已修复',
                             `create_time` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
                             PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `murphysec_vuln`;
CREATE TABLE `murphysec_vuln` (
                                  `id` int(10) NOT NULL AUTO_INCREMENT,
                                  `user_id` int(10) NOT NULL,
                                  `code_id` int(10) NOT NULL,
                                  `murphysec_id` int(10) NOT NULL,
                                  `title` varchar(255) NOT NULL DEFAULT '',
                                  `cve_id` varchar(20) NOT NULL DEFAULT '' COMMENT 'CVE编号',
                                  `suggest_level` varchar(20) NOT NULL DEFAULT '' COMMENT '处置建议',
                                  `poc` tinyint(1) NOT NULL DEFAULT '0',
                                  `description` text COMMENT '漏洞描述',
                                  `affected_version` varchar(255) NOT NULL DEFAULT '' COMMENT '影响版本',
                                  `min_fixed_version` varchar(100) NOT NULL DEFAULT '' COMMENT '最小修复版本',
                                  `solutions` text COMMENT '修复建议',
                                  `influence` int(3) NOT NULL DEFAULT '0' COMMENT '影响指数',
                                  `level` varchar(10) NOT NULL DEFAULT '' COMMENT '漏洞级别',
                                  `vuln_path` text,
                                  `publish_time` int(11) NOT NULL COMMENT '发布时间',
                                  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
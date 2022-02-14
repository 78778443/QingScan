INSERT INTO `QingScan`.`auth_rule` (`href`, `title`, `is_delete`, `is_open_auth`, `pid`, `sort`, `created_at`, `menu_status`, `update_time`, `level`, `delete_time`, `icon_url`) VALUES ('github_keyword_monitor_notice/index', 'github关键词监控结果', 0, 1, 35, 8, 1642852575, 1, 0, 2, 0, '');

INSERT INTO `QingScan`.`auth_rule` (`href`, `title`, `is_delete`, `is_open_auth`, `pid`, `sort`, `created_at`, `menu_status`, `update_time`, `level`, `delete_time`, `icon_url`) VALUES ('backup/index', '数据备份', 0, 1, 23, 8, 1642854435, 1, 0, 2, 0, '');

INSERT INTO `QingScan`.`auth_rule` (`href`, `title`, `is_delete`, `is_open_auth`, `pid`, `sort`, `created_at`, `menu_status`, `update_time`, `level`, `delete_time`, `icon_url`) VALUES ('user_log/index', '登录日志', 0, 1, 23, 9, 1643018839, 1, 0, 2, 0, '');

ALTER TABLE `QingScan`.`github_keyword_monitor_notice` ADD COLUMN `app_id` int(11) NOT NULL DEFAULT 0 AFTER `app_id`;

ALTER TABLE `QingScan`.`github_keyword_monitor` ADD COLUMN `app_id` int(11) NOT NULL DEFAULT 0 AFTER `app_id`;

CREATE TABLE `user_log` (
                            `id` int(10) NOT NULL AUTO_INCREMENT,
                            `username` char(30) NOT NULL DEFAULT '',
                            `create_time` datetime NOT NULL,
                            `content` varchar(255) NOT NULL DEFAULT '' COMMENT '详情',
                            `type` varchar(50) NOT NULL DEFAULT '' COMMENT '操作类型',
                            `ip` varchar(50) NOT NULL DEFAULT '',
                            PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=106 DEFAULT CHARSET=utf8mb4;

INSERT INTO `QingScan`.`auth_rule` (`href`, `title`, `is_delete`, `is_open_auth`, `pid`, `sort`, `created_at`, `menu_status`, `update_time`, `level`, `delete_time`, `icon_url`) VALUES ('zhiwen/index', '指纹列表', 0, 1, 35, 11, 1643338608, 1, 0, 2, 0, '');

INSERT INTO `QingScan`.`system_config` (`name`, `key`, `value`, `is_delete`) VALUES ('暂停扫描', 'maxProcesses', '1', 0);

INSERT INTO `qingscan`.`auth_rule` (`href`, `title`, `is_delete`, `is_open_auth`, `pid`, `sort`, `created_at`, `menu_status`, `update_time`, `level`, `delete_time`, `icon_url`) VALUES ('', '插件中心', 0, 1, 0, 8, 1642257783, 1, 0, 1, 0, '');
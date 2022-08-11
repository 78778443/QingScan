INSERT INTO `auth_rule` (`href`, `title`, `is_delete`, `is_open_auth`, `pid`, `sort`, `created_at`, `menu_status`, `update_time`, `level`, `delete_time`, `icon_url`) VALUES ('config/clear_cache', '清除缓存', 0, 1, 23, 10, 1660199916, 1, 0, 2, 0, '');
ALTER TABLE `host` ADD COLUMN `unauthorize_scan_time` datetime(0) NOT NULL DEFAULT '2000-01-01 00:00:00' COMMENT '未授权扫描时间';

DROP TABLE IF EXISTS `host_unauthorized`;
CREATE TABLE `host_unauthorized` (
                                     `id` int(10) NOT NULL AUTO_INCREMENT,
                                     `host_id` int(10) NOT NULL,
                                     `ip` char(20) CHARACTER SET latin1 NOT NULL DEFAULT '',
                                     `port` int(5) NOT NULL DEFAULT '0',
                                     `text` varchar(255) NOT NULL DEFAULT '',
                                     `user_id` int(10) NOT NULL DEFAULT '0',
                                     `is_delete` tinyint(1) NOT NULL DEFAULT '0',
                                     `status` tinyint(1) NOT NULL DEFAULT '1',
                                     `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                     PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
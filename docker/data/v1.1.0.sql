SET FOREIGN_KEY_CHECKS=0;

ALTER TABLE `QingScan`.`awvs_vuln` ADD COLUMN `app_id` int(11) NULL DEFAULT 0 AFTER `user_id`;

ALTER TABLE `QingScan`.`log` MODIFY COLUMN `content` varchar(5000) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL AFTER `id`;

CREATE TABLE `QingScan`.`plugin_scan_log`  (
                                               `id` int(10) NOT NULL AUTO_INCREMENT,
                                               `app_id` int(10) NOT NULL,
                                               `plugin_id` int(10) NOT NULL,
                                               `user_id` int(10) NOT NULL DEFAULT 0,
                                               `content` varchar(5000) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL COMMENT '扫描结果内容',
                                               `create_time` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0),
                                               `check_status` tinyint(1) NOT NULL COMMENT '审核状态',
                                               `plugin_name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL COMMENT '插件名称',
                                               `scan_type` int(255) NULL DEFAULT NULL COMMENT '0 app 1 host 2 code  3 url',
                                               `log_type` int(11) NULL DEFAULT 0 COMMENT '进度  0 开始扫描   1 完成   2 失败',
                                               PRIMARY KEY (`id`) USING BTREE,
                                               UNIQUE INDEX `un_id`(`app_id`, `plugin_name`, `log_type`, `scan_type`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin COMMENT = '自定义插件扫描结果' ROW_FORMAT = Compact;

CREATE TABLE `QingScan`.`plugin_store`  (
                                            `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '自增id',
                                            `status` tinyint(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态;1:开启;0:禁用',
                                            `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '插件安装时间',
                                            `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '插件标识名,英文字母(惟一)',
                                            `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '插件名称',
                                            `version` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '插件版本号',
                                            `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '插件描述',
                                            `code` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '兑换码',
                                            PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '插件表' ROW_FORMAT = Compact;

ALTER TABLE `QingScan`.`urls` MODIFY COLUMN `header` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL AFTER `create_time`;

DROP TABLE `QingScan`.`plugin_result`;

SET FOREIGN_KEY_CHECKS=1;
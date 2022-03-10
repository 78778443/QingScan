SET FOREIGN_KEY_CHECKS=0;

ALTER TABLE `QingScan`.`app` MODIFY COLUMN `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT '' AFTER `is_delete`;

ALTER TABLE `QingScan`.`app` MODIFY COLUMN `password` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT '' AFTER `username`;

ALTER TABLE `QingScan`.`code` ADD COLUMN `create_time` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) AFTER `webshell_scan_time`;

ALTER TABLE `QingScan`.`code_composer` COLLATE = utf8mb4_general_ci;

ALTER TABLE `QingScan`.`code_composer` ADD COLUMN `conflict` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL AFTER `require`;

ALTER TABLE `QingScan`.`code_composer` ADD COLUMN `suggest` varchar(2550) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL AFTER `require_dev`;

ALTER TABLE `QingScan`.`code_composer` ADD COLUMN `extra` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL AFTER `type`;

ALTER TABLE `QingScan`.`code_composer` ADD COLUMN `funding` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL AFTER `keywords`;

ALTER TABLE `QingScan`.`code_composer` MODIFY COLUMN `id` int(11) NOT NULL FIRST;

ALTER TABLE `QingScan`.`code_composer` MODIFY COLUMN `name` varchar(2048) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL AFTER `id`;

ALTER TABLE `QingScan`.`code_composer` MODIFY COLUMN `version` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL AFTER `name`;

ALTER TABLE `QingScan`.`code_composer` MODIFY COLUMN `source` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL AFTER `version`;

ALTER TABLE `QingScan`.`code_composer` MODIFY COLUMN `dist` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL AFTER `source`;

ALTER TABLE `QingScan`.`code_composer` MODIFY COLUMN `require` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL AFTER `dist`;

ALTER TABLE `QingScan`.`code_composer` MODIFY COLUMN `require_dev` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL AFTER `conflict`;

ALTER TABLE `QingScan`.`code_composer` MODIFY COLUMN `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL AFTER `suggest`;

ALTER TABLE `QingScan`.`code_composer` MODIFY COLUMN `autoload` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL AFTER `extra`;

ALTER TABLE `QingScan`.`code_composer` MODIFY COLUMN `notification_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL AFTER `autoload`;

ALTER TABLE `QingScan`.`code_composer` MODIFY COLUMN `license` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL AFTER `notification_url`;

ALTER TABLE `QingScan`.`code_composer` MODIFY COLUMN `authors` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL AFTER `license`;

ALTER TABLE `QingScan`.`code_composer` MODIFY COLUMN `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL AFTER `authors`;

ALTER TABLE `QingScan`.`code_composer` MODIFY COLUMN `keywords` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL AFTER `description`;

ALTER TABLE `QingScan`.`code_composer` MODIFY COLUMN `time` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL AFTER `funding`;

ALTER TABLE `QingScan`.`code_composer` MODIFY COLUMN `code_id` int(11) NULL DEFAULT NULL AFTER `time`;

ALTER TABLE `QingScan`.`code_composer` MODIFY COLUMN `user_id` int(11) NULL DEFAULT NULL AFTER `code_id`;

ALTER TABLE `QingScan`.`code_composer` MODIFY COLUMN `create_time` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) AFTER `user_id`;

ALTER TABLE `QingScan`.`code_composer` MODIFY COLUMN `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `QingScan`.`code_composer` DROP COLUMN `homepage`;

ALTER TABLE `QingScan`.`fortify` ADD COLUMN `code_id` int(11) NULL DEFAULT NULL AFTER `Source`;

ALTER TABLE `QingScan`.`fortify` DROP COLUMN `project_id`;

ALTER TABLE `QingScan`.`plugin_store` MODIFY COLUMN `create_time` datetime(0) NULL DEFAULT NULL COMMENT '插件安装时间' AFTER `status`;

SET FOREIGN_KEY_CHECKS=1;
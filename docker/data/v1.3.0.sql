SET FOREIGN_KEY_CHECKS=0;

ALTER TABLE `QingScan`.`host_port` ADD COLUMN `app_id` int(11) NOT NULL DEFAULT 0 AFTER `user_id`;

ALTER TABLE `QingScan`.`plugin_store` MODIFY COLUMN `status` tinyint(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态 1 开启  0 禁用' AFTER `id`;

SET FOREIGN_KEY_CHECKS=1;
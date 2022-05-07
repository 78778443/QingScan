ALTER TABLE `QingScan`.`code_composer` MODIFY COLUMN `dist` text;

ALTER TABLE `QingScan`.`code_composer` MODIFY COLUMN `authors` text;

ALTER TABLE `QingScan`.`code_composer` MODIFY COLUMN `funding` text;

ALTER TABLE `QingScan`.`code_composer` MODIFY COLUMN `require_dev` text;

ALTER TABLE `QingScan`.`code_composer` MODIFY COLUMN `keywords` text;

ALTER TABLE `QingScan`.`code_composer` MODIFY COLUMN `require` text;

ALTER TABLE `code` ADD COLUMN `is_online` int(10) NOT NULL DEFAULT 1 COMMENT '1线上   2本地';

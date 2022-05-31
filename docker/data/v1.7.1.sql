ALTER TABLE `fortify` ADD COLUMN `update_time` datetime(0) NOT NULL DEFAULT '2000-01-01 00:00:00';
ALTER TABLE `mobsfscan` ADD COLUMN `update_time` datetime(0) NOT NULL DEFAULT '2000-01-01 00:00:00';
ALTER TABLE `semgrep` ADD COLUMN `update_time` datetime(0) NOT NULL DEFAULT '2000-01-01 00:00:00';
ALTER TABLE `murphysec` ADD COLUMN `update_time` datetime(0) NOT NULL DEFAULT '2000-01-01 00:00:00';
update process_safe set status='0' where id='43';
UPDATE `auth_rule` SET `href` = 'fortify/index', `title` = 'Fortify' WHERE `auth_rule_id` = 16;
UPDATE `auth_rule` SET `href` = 'semgrep/index', `title` = 'SemGrep' WHERE `auth_rule_id` = 18;
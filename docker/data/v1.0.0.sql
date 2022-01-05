SET FOREIGN_KEY_CHECKS = 0;

ALTER TABLE `app`
    ADD COLUMN `xray_agent_port` int(10) NOT NULL DEFAULT 0 COMMENT 'xray被动代理端口' AFTER `vulmap_scan_time`;

ALTER TABLE `app`
    ADD COLUMN `agent_time` datetime(0) NOT NULL DEFAULT '2000-01-01 00:00:00' AFTER `xray_agent_port`;

ALTER TABLE `app`
    ADD COLUMN `agent_start_up` int(1) NOT NULL DEFAULT 0 COMMENT 'xray代理是否已启动' AFTER `agent_time`;

CREATE TABLE `system_zanzhu`
(
    `id`      int(11)                                                NOT NULL AUTO_INCREMENT,
    `name`    varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
    `amount`  float                                                  NULL DEFAULT NULL,
    `time`    date                                                   NULL DEFAULT NULL,
    `message` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
    PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_bin
  ROW_FORMAT = Compact;

SET FOREIGN_KEY_CHECKS = 1;
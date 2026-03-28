/*!40101 SET @OLD_CHARACTER_SET_CLIENT = @@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS = @@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION = @@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE = @@TIME_ZONE */;
/*!40103 SET TIME_ZONE = '+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS = @@UNIQUE_CHECKS, UNIQUE_CHECKS = 0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS = @@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS = 0 */;
/*!40101 SET @OLD_SQL_MODE = @@SQL_MODE, SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES = @@SQL_NOTES, SQL_NOTES = 0 */;
DROP TABLE IF EXISTS `llm_analysis`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `llm_analysis`
(
    `id`               int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '分析ID',
    `task_id`          int(10) unsigned NOT NULL,
    `risk_level`       varchar(16)      NOT NULL COMMENT 'critical/high/medium/low/none',
    `critical_count`   int(11)          NOT NULL DEFAULT '0',
    `high_count`       int(11)          NOT NULL DEFAULT '0',
    `medium_count`     int(11)          NOT NULL DEFAULT '0',
    `low_count`        int(11)          NOT NULL DEFAULT '0',
    `analysis_summary` text             NOT NULL COMMENT '分析总结',
    `fix_suggestion`   text             NOT NULL COMMENT '修复建议',
    `llm_model`        varchar(64)               DEFAULT NULL COMMENT '使用模型',
    `create_time`      datetime         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_task_id` (`task_id`),
    CONSTRAINT `fk_analysis_task` FOREIGN KEY (`task_id`) REFERENCES `scan_task` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4 COMMENT ='LLM分析结果表';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `scan_result`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scan_result`
(
    `id`            int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '结果唯一ID',
    `task_id`       int(10) unsigned NOT NULL,
    `vuln_level`    varchar(16)      NOT NULL DEFAULT 'info' COMMENT 'critical/high/medium/low/info',
    `vuln_type`     varchar(64)               DEFAULT NULL COMMENT '漏洞类型',
    `vuln_title`    varchar(255)              DEFAULT NULL COMMENT '漏洞标题',
    `vuln_detail`   text COMMENT '漏洞详情',
    `vuln_request`  text COMMENT '请求包',
    `vuln_response` text COMMENT '响应包',
    `vuln_evidence` text COMMENT '证据',
    `vuln_url`      varchar(512)              DEFAULT NULL COMMENT '漏洞URL',
    `is_fixed`      tinyint(1)       NOT NULL DEFAULT '0' COMMENT '0未修复 1已修复',
    `create_time`   datetime         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_task_id` (`task_id`),
    KEY `idx_vuln_level` (`vuln_level`),
    CONSTRAINT `fk_result_task` FOREIGN KEY (`task_id`) REFERENCES `scan_task` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB
  AUTO_INCREMENT = 48
  DEFAULT CHARSET = utf8mb4 COMMENT ='扫描原始结果表';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `scan_target`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scan_target`
(
    `id`          int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '目标唯一ID',
    `target`      varchar(512)     NOT NULL COMMENT '扫描目标URL/IP/域名',
    `target_type` varchar(16)      NOT NULL DEFAULT 'url' COMMENT '目标类型',
    `status`      tinyint(1)       NOT NULL DEFAULT '1' COMMENT '1有效 0无效',
    `create_time` datetime         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_target` (`target`(255))
) ENGINE = InnoDB
  AUTO_INCREMENT = 6
  DEFAULT CHARSET = utf8mb4 COMMENT ='扫描目标表';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `scan_task`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scan_task`
(
    `id`           int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '任务唯一ID',
    `target_id`    int(10) unsigned NOT NULL,
    `tool_id`      int(10) unsigned NOT NULL,
    `task_status`  varchar(32)      NOT NULL DEFAULT 'pending' COMMENT 'pending/running/success/failed',
    `progress`     tinyint(3) unsigned       DEFAULT '0' COMMENT '进度 0-100',
    `message`      varchar(500)              DEFAULT NULL COMMENT '状态消息',
    `start_time`   datetime                  DEFAULT NULL,
    `end_time`     datetime                  DEFAULT NULL,
    `result_count` int(11)          NOT NULL DEFAULT '0',
    `tool_output`  mediumtext COMMENT '工具执行原始输出',
    `create_time`  datetime         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_target_id` (`target_id`),
    KEY `idx_tool_id` (`tool_id`),
    CONSTRAINT `fk_task_target` FOREIGN KEY (`target_id`) REFERENCES `scan_target` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_task_tool` FOREIGN KEY (`tool_id`) REFERENCES `scan_tool` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB
  AUTO_INCREMENT = 12
  DEFAULT CHARSET = utf8mb4 COMMENT ='扫描任务表';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `scan_tool`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scan_tool`
(
    `id`            int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '工具唯一ID',
    `tool_name`     varchar(64)      NOT NULL COMMENT '工具命令名（如sqlmap、xray）',
    `tool_label`    varchar(64)      NOT NULL COMMENT '工具显示名',
    `tool_type`     varchar(32)      NOT NULL COMMENT '工具类型：sql_inject/xss/fuzz等',
    `command`       text             NOT NULL COMMENT '执行命令模板{target}',
    `start_command` varchar(1000)             DEFAULT NULL COMMENT '启动命令模板',
    `script_code`   text COMMENT '脚本代码',
    `output_parse`  text COMMENT '输出解析规则JSON',
    `is_enabled`    tinyint(1)       NOT NULL DEFAULT '1' COMMENT '1启用 0禁用',
    `description`   text COMMENT '工具描述',
    `create_time`   datetime         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `update_time`   datetime         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_tool_name` (`tool_name`)
) ENGINE = InnoDB
  AUTO_INCREMENT = 14
  DEFAULT CHARSET = utf8mb4 COMMENT ='扫描工具配置表';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE = @OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE = @OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS = @OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS = @OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT = @OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS = @OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION = @OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES = @OLD_SQL_NOTES */;


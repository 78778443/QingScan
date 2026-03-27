-- 扫描工具配置表
CREATE TABLE `scan_tool`
(
    `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '工具唯一ID',
    `tool_name`   VARCHAR(64)  NOT NULL COMMENT '工具命令名（如sqlmap、xray）',
    `tool_label`  VARCHAR(64)  NOT NULL COMMENT '工具显示名',
    `tool_type`   VARCHAR(32)  NOT NULL COMMENT '工具类型：sql_inject/xss/fuzz等',
    `command`     TEXT         NOT NULL COMMENT '执行命令模板{target}',
    `is_enabled`  TINYINT(1)   NOT NULL DEFAULT 1 COMMENT '1启用 0禁用',
    `description` TEXT                  DEFAULT NULL COMMENT '工具描述',
    `create_time` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `update_time` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_tool_name` (`tool_name`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4 COMMENT ='扫描工具配置表';

-- 初始化工具数据（-init 命令使用）
INSERT INTO scan_tool (tool_name, tool_label, tool_type, command, description)
VALUES ('sqlmap', 'SQLMap', 'sql_inject', 'sqlmap -u {target} --batch', 'SQL注入检测工具'),
       ('xray', 'XRay', 'vuln_scan', 'xray webscan --url {target} --html-output', '通用漏洞扫描工具');

-- 扫描目标表
CREATE TABLE `scan_target`
(
    `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '目标唯一ID',
    `target`      VARCHAR(512) NOT NULL COMMENT '扫描目标URL/IP/域名',
    `target_type` VARCHAR(16)  NOT NULL DEFAULT 'url' COMMENT '目标类型',
    `status`      TINYINT(1)   NOT NULL DEFAULT 1 COMMENT '1有效 0无效',
    `create_time` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_target` (`target`(255))
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4 COMMENT ='扫描目标表';

-- 扫描任务表
CREATE TABLE `scan_task`
(
    `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '任务唯一ID',
    `target_id`    INT UNSIGNED NOT NULL,
    `tool_id`      INT UNSIGNED NOT NULL,
    `task_status`  VARCHAR(32)  NOT NULL DEFAULT 'pending' COMMENT 'pending/running/success/failed',
    `start_time`   DATETIME              DEFAULT NULL,
    `end_time`     DATETIME              DEFAULT NULL,
    `result_count` INT          NOT NULL DEFAULT 0,
    `tool_output`  MEDIUMTEXT            DEFAULT NULL COMMENT '工具执行原始输出',
    `create_time`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_target_id` (`target_id`),
    KEY `idx_tool_id` (`tool_id`),
    CONSTRAINT `fk_task_target` FOREIGN KEY (`target_id`) REFERENCES `scan_target` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_task_tool` FOREIGN KEY (`tool_id`) REFERENCES `scan_tool` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4 COMMENT ='扫描任务表';

-- 扫描结果表
CREATE TABLE `scan_result`
(
    `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '结果唯一ID',
    `task_id`     INT UNSIGNED NOT NULL,
    `vuln_level`  VARCHAR(16)  NOT NULL DEFAULT 'info' COMMENT 'critical/high/medium/low/info',
    `vuln_type`   VARCHAR(64)           DEFAULT NULL COMMENT '漏洞类型',
    `vuln_detail` TEXT                  DEFAULT NULL COMMENT '漏洞详情',
    `vuln_url`    VARCHAR(512)          DEFAULT NULL COMMENT '漏洞URL',
    `is_fixed`    TINYINT(1)   NOT NULL DEFAULT 0 COMMENT '0未修复 1已修复',
    `create_time` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_task_id` (`task_id`),
    KEY `idx_vuln_level` (`vuln_level`),
    CONSTRAINT `fk_result_task` FOREIGN KEY (`task_id`) REFERENCES `scan_task` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4 COMMENT ='扫描原始结果表';

-- LLM分析结果表
CREATE TABLE `llm_analysis`
(
    `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '分析ID',
    `task_id`          INT UNSIGNED NOT NULL,
    `risk_level`       VARCHAR(16)  NOT NULL COMMENT 'critical/high/medium/low/none',
    `critical_count`   INT          NOT NULL DEFAULT 0,
    `high_count`       INT          NOT NULL DEFAULT 0,
    `medium_count`     INT          NOT NULL DEFAULT 0,
    `low_count`        INT          NOT NULL DEFAULT 0,
    `analysis_summary` TEXT         NOT NULL COMMENT '分析总结',
    `fix_suggestion`   TEXT         NOT NULL COMMENT '修复建议',
    `llm_model`        VARCHAR(64)           DEFAULT NULL COMMENT '使用模型',
    `create_time`      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_task_id` (`task_id`),
    CONSTRAINT `fk_analysis_task` FOREIGN KEY (`task_id`) REFERENCES `scan_task` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4 COMMENT ='LLM分析结果表';
-- 初始化数据库脚本
-- 创建表结构和默认数据

-- 创建用户表
CREATE TABLE IF NOT EXISTS `user` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nickname` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `status` tinyint DEFAULT 1 COMMENT '1:正常 0:禁用',
  `role` varchar(20) DEFAULT 'user' COMMENT 'admin, user',
  `avatar` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 插入默认管理员用户 (密码: admin123)
INSERT INTO `user` (`username`, `password`, `nickname`, `email`, `status`, `role`) VALUES
('admin', '$2a$10$N9qo8uLOickgx2ZMRZoMy.MQDq3VYD0pKQz5VY5qNQTSVYD1RQJIG', 'Administrator', 'admin@qingscan.local', 1, 'admin');

-- 创建扫描任务表
CREATE TABLE IF NOT EXISTS `task_scan` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `type` varchar(20) NOT NULL COMMENT 'host, web, code',
  `target` varchar(500) NOT NULL,
  `tools` varchar(500) DEFAULT NULL COMMENT '使用的工具',
  `status` tinyint DEFAULT 0 COMMENT '0:待执行 1:执行中 2:完成 3:失败',
  `progress` int DEFAULT 0 COMMENT '0-100',
  `result_count` int DEFAULT 0,
  `user_id` int unsigned NOT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `error_msg` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 创建主机资产表
CREATE TABLE IF NOT EXISTS `asm_host` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `ip` varchar(50) DEFAULT NULL,
  `mac` varchar(50) DEFAULT NULL,
  `hostname` varchar(100) DEFAULT NULL,
  `os` varchar(100) DEFAULT NULL,
  `os_version` varchar(100) DEFAULT NULL,
  `status` tinyint DEFAULT 1 COMMENT '1:存活 0:down',
  `isp` varchar(50) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `province` varchar(50) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `longitude` varchar(50) DEFAULT NULL,
  `latitude` varchar(50) DEFAULT NULL,
  `port_count` int DEFAULT 0,
  `vuln_count` int DEFAULT 0,
  `tags` varchar(255) DEFAULT NULL,
  `remark` varchar(500) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_ip` (`ip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 创建域名表
CREATE TABLE IF NOT EXISTS `asm_domain` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `domain` varchar(100) DEFAULT NULL,
  `type` varchar(20) DEFAULT NULL COMMENT 'primary, subdomain',
  `source` varchar(50) DEFAULT NULL,
  `ip` varchar(100) DEFAULT NULL,
  `cname` varchar(100) DEFAULT NULL,
  `port` varchar(100) DEFAULT NULL,
  `status` tinyint DEFAULT 1,
  `title` varchar(200) DEFAULT NULL,
  `server` varchar(100) DEFAULT NULL,
  `remark` varchar(500) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_domain` (`domain`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 创建端口表
CREATE TABLE IF NOT EXISTS `asm_host_port` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `host_id` int unsigned DEFAULT NULL,
  `host` varchar(50) DEFAULT NULL,
  `port` varchar(10) DEFAULT NULL,
  `protocol` varchar(20) DEFAULT NULL,
  `state` varchar(20) DEFAULT NULL,
  `service` varchar(50) DEFAULT NULL,
  `version` varchar(100) DEFAULT NULL,
  `banner` varchar(500) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_host` (`host`),
  KEY `idx_port` (`port`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 创建URL表
CREATE TABLE IF NOT EXISTS `asm_urls` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `url` varchar(500) DEFAULT NULL,
  `host` varchar(100) DEFAULT NULL,
  `domain` varchar(100) DEFAULT NULL,
  `scheme` varchar(10) DEFAULT NULL,
  `method` varchar(10) DEFAULT NULL,
  `path` varchar(255) DEFAULT NULL,
  `query` varchar(500) DEFAULT NULL,
  `status_code` int DEFAULT NULL,
  `title` varchar(200) DEFAULT NULL,
  `length` int DEFAULT NULL,
  `fingerprint` varchar(100) DEFAULT NULL,
  `source` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_url` (`url`(200)),
  KEY `idx_host` (`host`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 创建漏洞表
CREATE TABLE IF NOT EXISTS `asm_vulnerability_summary` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) DEFAULT NULL,
  `target` varchar(500) DEFAULT NULL COMMENT 'URL或IP',
  `type` varchar(50) DEFAULT NULL COMMENT 'sql,xss,rce等',
  `severity` varchar(20) DEFAULT NULL COMMENT 'critical,high,medium,low,info',
  `status` tinyint DEFAULT 0 COMMENT '0:待确认 1:已确认 2:误报 3:已修复',
  `tool` varchar(50) DEFAULT NULL COMMENT 'nuclei,xray等',
  `poc` varchar(100) DEFAULT NULL,
  `description` text,
  `solution` text,
  `request` text,
  `response` text,
  `remark` varchar(500) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_name` (`name`),
  KEY `idx_type` (`type`),
  KEY `idx_target` (`target`(200))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 创建应用表
CREATE TABLE IF NOT EXISTS `app` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `url` varchar(500) NOT NULL,
  `domain` varchar(100) DEFAULT NULL,
  `ip` varchar(50) DEFAULT NULL,
  `port` int DEFAULT NULL,
  `scheme` varchar(10) DEFAULT NULL,
  `status` tinyint DEFAULT 1 COMMENT '1:启用 0:禁用',
  `fingerprint` varchar(100) DEFAULT NULL,
  `web_server` varchar(100) DEFAULT NULL,
  `framework` varchar(100) DEFAULT NULL,
  `language` varchar(50) DEFAULT NULL,
  `title` varchar(200) DEFAULT NULL,
  `remark` varchar(500) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 创建工具配置表
CREATE TABLE IF NOT EXISTS `project_tools` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `path` varchar(255) DEFAULT NULL,
  `version` varchar(50) DEFAULT NULL,
  `status` tinyint DEFAULT 0 COMMENT '1:已安装 0:未安装',
  `install_cmd` varchar(500) DEFAULT NULL,
  `check_cmd` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 插入工具配置
INSERT INTO `project_tools` (`name`, `status`, `install_cmd`) VALUES
('nmap', 0, 'apt install nmap'),
('nuclei', 0, 'go install github.com/projectdiscovery/nuclei/v3@latest'),
('xray', 0, 'download from github.com/chaitin/xray'),
('sqlmap', 0, 'git clone https://github.com/sqlmapproject/sqlmap.git'),
('dirmap', 0, 'git clone https://github.com/H4ckForJob/dirmap.git'),
('crawlergo', 0, 'go install github.com/9bie/sec/crawlergo@latest'),
('whatweb', 0, 'apt install whatweb'),
('rad', 0, 'go install github.com/chaitin/rad@latest'),
('hydra', 0, 'apt install hydra'),
('semgrep', 0, 'pip install semgrep'),
('vulmap', 0, 'git clone https://github.com/zhzyker/vulmap.git'),
('dismap', 0, 'git clone https://github.com/zhzyker/dismap.git');

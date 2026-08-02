
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `app` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` tinyint(4) DEFAULT '1' COMMENT '状态',
  `name` varchar(150) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '目标名称',
  `url` varchar(100) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '目标URL',
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `crawler_time` datetime DEFAULT '2000-01-01 00:00:00' COMMENT '爬虫时间',
  `awvs_scan_time` datetime DEFAULT '2000-01-01 00:00:00' COMMENT 'Web漏洞扫描时间',
  `subdomain_time` datetime DEFAULT '2000-01-01 00:00:00',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '软删除标记 0未删 1已删',
  `username` varchar(50) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '账号',
  `password` char(32) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '口令',
  `finger_scan_time` datetime DEFAULT '2000-01-01 00:00:00' COMMENT '指纹扫描时间',
  `subdomain_scan_time` datetime DEFAULT '2000-01-01 00:00:00' COMMENT '子域名扫描时间',
  `screenshot_time` datetime DEFAULT '2000-01-01 00:00:00' COMMENT '截图时间',
  `web_vuln_scan_time` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
  `dir_scan_time` datetime DEFAULT '2000-01-01 00:00:00' COMMENT '目录扫描时间',
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `waf_scan_time` datetime NOT NULL DEFAULT '2000-01-01 00:00:00' COMMENT 'WAF扫描时间',
  `jietu_path` varchar(512) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '截图路径',
  `is_intranet` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否内网',
  `gen_vuln_scan_time` datetime DEFAULT '2000-01-01 00:00:00',
  `asset_finger_scan_time` datetime DEFAULT '2000-01-01 00:00:00',
  `spider_scan_time` datetime DEFAULT '2000-01-01 00:00:00',
  `vul_verify_scan_time` datetime DEFAULT '2000-01-01 00:00:00',
  `xray_agent_port` int(11) NOT NULL DEFAULT '0' COMMENT 'xray被动代理端口',
  `agent_time` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
  `agent_start_up` int(11) NOT NULL DEFAULT '0' COMMENT 'xray代理是否已启动',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `un_url` (`url`) USING BTREE,
  KEY `idx_status` (`status`),
  KEY `idx_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin ROW_FORMAT=DYNAMIC COMMENT='扫描目标（网站）';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `app_info` (
  `app_id` int(11) NOT NULL DEFAULT '0',
  `cms` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `server` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `statuscode` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `length` int(11) DEFAULT NULL,
  `code` int(11) NOT NULL DEFAULT '0' COMMENT '状态码',
  `page_title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '网页标题',
  `header` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT '网页header',
  `icon` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '网页ICON',
  `url_screenshot` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT 'url屏幕截图',
  PRIMARY KEY (`app_id`) USING BTREE,
  KEY `idx_app` (`app_id`),
  CONSTRAINT `app_id` FOREIGN KEY (`app_id`) REFERENCES `app` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin ROW_FORMAT=DYNAMIC COMMENT='网站基础信息';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `asm_domain` (
  `id` int(11) NOT NULL,
  `domain` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `idx_domain` (`domain`(64))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin ROW_FORMAT=DYNAMIC COMMENT='域名资产';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `asm_host` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `app_id` int(11) DEFAULT '0' COMMENT '目标ID',
  `domain` varchar(64) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '域名',
  `host` text COLLATE utf8mb4_bin COMMENT '主机',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `isp` varchar(20) COLLATE utf8mb4_bin NOT NULL COMMENT '运营商',
  `country` varchar(50) COLLATE utf8mb4_bin NOT NULL COMMENT '国家',
  `region` varchar(50) COLLATE utf8mb4_bin NOT NULL COMMENT '省份',
  `city` varchar(50) COLLATE utf8mb4_bin NOT NULL COMMENT '城市',
  `area` varchar(50) COLLATE utf8mb4_bin NOT NULL COMMENT '地区',
  `weak_pass_scan_time` datetime NOT NULL DEFAULT '2000-01-01 00:00:00' COMMENT '弱口令扫描时间',
  `port_scan_time` datetime NOT NULL DEFAULT '2000-01-01 00:00:00' COMMENT '端口扫描时间',
  `ip_scan_time` datetime NOT NULL DEFAULT '2000-01-01 00:00:00' COMMENT 'IP扫描时间',
  `is_delete` int(11) NOT NULL DEFAULT '0' COMMENT '软删除标记 0未删 1已删',
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `unauthorize_scan_time` datetime NOT NULL DEFAULT '2000-01-01 00:00:00' COMMENT '未授权扫描时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `un_host` (`domain`) USING BTREE,
  KEY `idx_host` (`host`(64)),
  KEY `idx_domain` (`domain`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin ROW_FORMAT=DYNAMIC COMMENT='主机资产';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `asm_host_assets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `instance_id` varchar(100) COLLATE utf8mb4_bin NOT NULL COMMENT '实例ID',
  `instance_name` varchar(100) COLLATE utf8mb4_bin NOT NULL COMMENT '实例名称',
  `display_name` varchar(100) COLLATE utf8mb4_bin DEFAULT '' COMMENT '显示名称',
  `cloud_platform` varchar(50) COLLATE utf8mb4_bin NOT NULL COMMENT '云平台: huoshan(火山云), tianyi(天翼云), idc(线下IDC)',
  `status` varchar(20) COLLATE utf8mb4_bin NOT NULL COMMENT '实例状态',
  `private_ip` varchar(50) COLLATE utf8mb4_bin NOT NULL COMMENT '私有IP地址',
  `private_ips` json DEFAULT NULL COMMENT '私有IP列表（支持多个IP）',
  `public_ip` varchar(50) COLLATE utf8mb4_bin DEFAULT '' COMMENT '公网IP地址',
  `public_ips` json DEFAULT NULL COMMENT '公网IP列表（支持多个IP）',
  `mac_address` varchar(50) COLLATE utf8mb4_bin DEFAULT '' COMMENT 'MAC地址',
  `os_type` varchar(20) COLLATE utf8mb4_bin NOT NULL COMMENT '操作系统类型',
  `os_name` varchar(100) COLLATE utf8mb4_bin NOT NULL COMMENT '操作系统名称',
  `cpu` int(11) NOT NULL COMMENT 'CPU核心数',
  `memory` int(11) NOT NULL COMMENT '内存大小(MB)',
  `instance_type` varchar(50) COLLATE utf8mb4_bin DEFAULT '' COMMENT '实例类型',
  `vpc_id` varchar(100) COLLATE utf8mb4_bin DEFAULT '' COMMENT 'VPC ID',
  `vpc_name` varchar(100) COLLATE utf8mb4_bin DEFAULT '' COMMENT 'VPC名称',
  `security_groups` text COLLATE utf8mb4_bin COMMENT '安全组(JSON格式)',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `update_time` datetime NOT NULL COMMENT '更新时间',
  `expire_time` datetime DEFAULT NULL COMMENT '到期时间',
  `hids_installed` tinyint(1) DEFAULT '0' COMMENT '是否安装HIDS(0:未安装, 1:已安装)',
  `hids_version` varchar(50) COLLATE utf8mb4_bin DEFAULT '' COMMENT 'HIDS版本',
  `hids_last_check` datetime DEFAULT NULL COMMENT 'HIDS最后检查时间',
  `remark` text COLLATE utf8mb4_bin COMMENT '备注信息',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_instance_id_platform` (`instance_id`,`cloud_platform`),
  KEY `idx_cloud_platform` (`cloud_platform`),
  KEY `idx_status` (`status`),
  KEY `idx_private_ip` (`private_ip`),
  KEY `idx_hids_installed` (`hids_installed`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='主机资产清点（IDC）';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `asm_host_port` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `port` int(11) NOT NULL DEFAULT '0' COMMENT '端口',
  `host` varchar(30) COLLATE utf8mb4_bin NOT NULL DEFAULT '0' COMMENT '主机',
  `type` char(5) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '协议类型',
  `service` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '服务',
  `is_close` tinyint(4) DEFAULT '0' COMMENT '是否关闭',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `os` varchar(30) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '操作系统',
  `html` text COLLATE utf8mb4_bin COMMENT '页面HTML',
  `headers` text COLLATE utf8mb4_bin COMMENT '响应头',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '软删除标记 0未删 1已删',
  `scan_time` datetime NOT NULL DEFAULT '2000-01-01 00:00:00' COMMENT '扫描时间',
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `app_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `un_port` (`host`,`port`,`type`) USING BTREE,
  KEY `idx_host` (`host`),
  KEY `idx_port` (`port`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin ROW_FORMAT=DYNAMIC COMMENT='主机端口资产';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `asm_ip` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(50) COLLATE utf8mb4_bin NOT NULL COMMENT 'IP地址',
  `location` varchar(100) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '地理位置',
  `isp` varchar(50) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '运营商',
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `ip` (`ip`) USING BTREE,
  KEY `idx_ip` (`ip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin ROW_FORMAT=DYNAMIC COMMENT='IP资产';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `asm_ip_port` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `method` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `app_id` int(11) DEFAULT '0' COMMENT '目标ID',
  `url` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `header` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `response_header` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `hash` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `scan_time` datetime DEFAULT '2000-01-01 00:00:00',
  `scheme` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `host` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `path` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `query` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `title` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `keywords` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `description` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `content_type` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `extension` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '软删除标记 0未删 1已删',
  `sqlmap_scan_time` datetime DEFAULT '2000-01-01 00:00:00',
  `id_card` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `email` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `phone` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `icp` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `user_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `un_url` (`hash`) USING BTREE,
  KEY `appid` (`app_id`) USING BTREE,
  CONSTRAINT `asm_ip_port_ibfk_1` FOREIGN KEY (`app_id`) REFERENCES `app` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin ROW_FORMAT=DYNAMIC COMMENT='IP端口资产';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `asm_urls` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `method` varchar(20) COLLATE utf8mb4_bin NOT NULL COMMENT '请求方法',
  `app_id` int(11) DEFAULT '0' COMMENT '目标ID',
  `url` varchar(1024) COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'URL地址',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `header` varchar(1024) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '请求头',
  `content` longtext COLLATE utf8mb4_bin COMMENT '页面内容',
  `response_header` text COLLATE utf8mb4_bin COMMENT '响应头',
  `hash` varchar(32) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '内容哈希',
  `scan_time` datetime DEFAULT '2000-01-01 00:00:00',
  `scheme` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `host` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `path` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `query` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `title` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `keywords` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `description` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `content_type` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `extension` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '软删除标记 0未删 1已删',
  `sql_inject_scan_time` datetime DEFAULT '2000-01-01 00:00:00' COMMENT 'SQL注入扫描时间',
  `id_card` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `email` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `phone` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `icp` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `user_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `un_url` (`hash`) USING BTREE,
  KEY `appid` (`app_id`) USING BTREE,
  CONSTRAINT `appid` FOREIGN KEY (`app_id`) REFERENCES `app` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin ROW_FORMAT=DYNAMIC COMMENT='URL资产';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `asm_work_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_bin NOT NULL COMMENT '工单标题',
  `type` enum('vulnerability','system','other') COLLATE utf8mb4_bin DEFAULT 'vulnerability' COMMENT '工单类型 vulnerability/system/other',
  `content` text COLLATE utf8mb4_bin NOT NULL COMMENT '工单内容',
  `status` enum('pending_dispatch','dispatched','confirmed','fixed_unconfirmed','fixed_confirmed') COLLATE utf8mb4_bin NOT NULL DEFAULT 'pending_dispatch' COMMENT '状态 pending_dispatch/dispatched/confirmed/fixed_unconfirmed/fixed_confirmed',
  `vul_id` int(11) DEFAULT NULL COMMENT '关联漏洞ID',
  `vul_type` varchar(50) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '漏洞类型',
  `feishu_notified` tinyint(1) DEFAULT '0' COMMENT '飞书通知标记',
  `feishu_group_id` varchar(100) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '飞书群ID',
  `created_by` int(11) DEFAULT NULL COMMENT '创建人ID',
  `assigned_to` int(11) DEFAULT NULL COMMENT '指派给',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `security_owner` varchar(50) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '安全owner',
  `business_owner` varchar(50) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '业务owner',
  `confirmer` varchar(50) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '确认人',
  `fixer` varchar(50) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '修复人',
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_type` (`type`),
  KEY `idx_vul_id` (`vul_id`),
  KEY `idx_feishu_notified` (`feishu_notified`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='工单';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `code` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '项目名称',
  `ssh_url` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '仓库地址',
  `desc` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '项目描述',
  `hash` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '代码哈希',
  `scan_time` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
  `sonar_scan_time` datetime NOT NULL DEFAULT '2000-01-01 00:00:00' COMMENT 'Sonar扫描时间',
  `kunlun_scan_time` datetime NOT NULL DEFAULT '2000-01-01 00:00:00' COMMENT '昆仑扫描时间',
  `semgrep_scan_time` datetime NOT NULL DEFAULT '2000-01-01 00:00:00' COMMENT '代码审计时间',
  `pulling_mode` char(10) COLLATE utf8mb4_bin NOT NULL COMMENT '拉取方式',
  `is_private` tinyint(1) NOT NULL DEFAULT '0',
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '用户名',
  `password` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '密码',
  `private_key` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '软删除标记 0未删 1已删',
  `composer_scan_time` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
  `java_scan_time` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
  `python_scan_time` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `star` char(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `webshell_scan_time` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `is_online` int(11) NOT NULL DEFAULT '1' COMMENT '1线上   2本地',
  `mobsfscan_scan_time` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
  `project_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1php 2java 3python 4go 5app 6其他',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `murphysec_scan_time` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `idx_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin ROW_FORMAT=DYNAMIC COMMENT='代码审计项目';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `app` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `time` (`create_time`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin ROW_FORMAT=DYNAMIC COMMENT='系统操作日志';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `plugin_scan_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `app_id` int(11) NOT NULL COMMENT '目标ID',
  `plugin_id` int(11) NOT NULL COMMENT '插件ID',
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `content` varchar(5000) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '扫描结果内容',
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `check_status` tinyint(1) NOT NULL COMMENT '审核状态',
  `plugin_name` varchar(50) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '插件名称',
  `scan_type` int(11) DEFAULT NULL COMMENT '扫描类型',
  `log_type` int(11) DEFAULT '0' COMMENT '日志类型 0开始 1完成 2失败',
  `file_content` varchar(5000) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '文件内容',
  `is_read` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否已读',
  `is_custom` int(11) NOT NULL DEFAULT '1' COMMENT '是否为自定义插件 1否  2是',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `un_id` (`app_id`,`plugin_name`,`log_type`,`scan_type`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin ROW_FORMAT=DYNAMIC COMMENT='插件扫描日志';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `project_tools` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '项目类型 1app 2code',
  `project_id` int(11) NOT NULL DEFAULT '9' COMMENT '项目ID',
  `tools_name` varchar(50) COLLATE utf8mb4_bin NOT NULL COMMENT '工具标识',
  `create_time` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `idx_project` (`project_id`),
  KEY `idx_tools` (`tools_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin ROW_FORMAT=DYNAMIC COMMENT='项目工具授权';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scan_asset_finger` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `app_id` int(11) NOT NULL DEFAULT '0' COMMENT '目标ID',
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `result` text COLLATE utf8mb4_bin COMMENT '指纹结果JSON',
  `create_time` datetime DEFAULT '2000-01-01 00:00:00',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `idx_app` (`app_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin ROW_FORMAT=DYNAMIC COMMENT='资产指纹识别结果';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scan_code_audit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code_id` int(11) NOT NULL DEFAULT '0' COMMENT '代码项目ID',
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `file` varchar(512) COLLATE utf8mb4_bin NOT NULL COMMENT '文件路径',
  `line` int(11) NOT NULL DEFAULT '0' COMMENT '行号',
  `rule_id` varchar(100) COLLATE utf8mb4_bin NOT NULL COMMENT '规则标识',
  `message` varchar(512) COLLATE utf8mb4_bin NOT NULL COMMENT '问题描述',
  `severity` varchar(20) COLLATE utf8mb4_bin NOT NULL DEFAULT 'warning' COMMENT '严重级别 error/warning',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_code` (`code_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='代码审计结果';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scan_dir` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `app_id` int(11) NOT NULL COMMENT '目标ID',
  `code` varchar(10) COLLATE utf8mb4_bin NOT NULL COMMENT '状态码',
  `size` varchar(20) COLLATE utf8mb4_bin NOT NULL COMMENT '响应大小KB',
  `type` varchar(100) COLLATE utf8mb4_bin NOT NULL COMMENT '响应类型',
  `url` varchar(255) COLLATE utf8mb4_bin NOT NULL COMMENT '路径URL',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `user_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `idx_app` (`app_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin ROW_FORMAT=DYNAMIC COMMENT='目录扫描结果';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scan_finger` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `app_id` int(11) NOT NULL COMMENT '目标ID',
  `target` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '目标URL',
  `http_status` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'HTTP状态码',
  `request_config` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '请求配置摘要',
  `plugins` text COLLATE utf8mb4_bin COMMENT '指纹列表JSON',
  `create_time` datetime DEFAULT '2000-01-01 00:00:00' COMMENT '创建时间',
  `poc_scan_time` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
  `user_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `idx_app` (`app_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin ROW_FORMAT=DYNAMIC COMMENT='Web指纹识别结果';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scan_spider` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `app_id` int(11) NOT NULL COMMENT '目标ID',
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `url` text COLLATE utf8mb4_bin COMMENT '抓取URL',
  `method` varchar(20) COLLATE utf8mb4_bin NOT NULL COMMENT '请求方法',
  `accept` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `cache_control` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `cookie` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `referer` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `spider_name` varchar(100) COLLATE utf8mb4_bin NOT NULL COMMENT '爬虫来源',
  `user_agent` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `data` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `source` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `create_time` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `idx_app` (`app_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin ROW_FORMAT=DYNAMIC COMMENT='爬虫抓取结果';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scan_sql_inject` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `urls_id` int(11) NOT NULL COMMENT 'URL资产ID',
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `type` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '注入类型',
  `title` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '标题',
  `payload` text COLLATE utf8mb4_bin COMMENT '注入载荷',
  `app_id` int(11) NOT NULL DEFAULT '0' COMMENT '目标ID',
  `system` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `application` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `dbms` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '数据库类型',
  `user_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `idx_app` (`app_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin ROW_FORMAT=DYNAMIC COMMENT='SQL注入检测结果';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scan_subdomain` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `app_id` int(11) NOT NULL DEFAULT '0' COMMENT '目标ID',
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `subdomain` varchar(255) COLLATE utf8mb4_bin NOT NULL COMMENT '子域名',
  `ip` varchar(50) COLLATE utf8mb4_bin NOT NULL COMMENT 'IP地址',
  `cname` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'CNAME',
  `level` tinyint(4) NOT NULL DEFAULT '3' COMMENT '子域名层级',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '存活状态 1存活 0失效',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_subdomain` (`subdomain`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='子域名枚举结果';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scan_vuln` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `app_id` int(11) NOT NULL DEFAULT '0' COMMENT '目标ID',
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `url` varchar(512) COLLATE utf8mb4_bin NOT NULL COMMENT '漏洞URL',
  `name` varchar(255) COLLATE utf8mb4_bin NOT NULL COMMENT '漏洞名称',
  `severity` varchar(20) COLLATE utf8mb4_bin NOT NULL DEFAULT 'low' COMMENT '危害等级 low/medium/high/critical',
  `payload` text COLLATE utf8mb4_bin COMMENT '检测载荷',
  `description` text COLLATE utf8mb4_bin COMMENT '漏洞描述',
  `source` varchar(50) COLLATE utf8mb4_bin NOT NULL COMMENT '检测引擎来源',
  `check_status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '审核状态 0未审核 1已确认 2已修复',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_app` (`app_id`),
  KEY `idx_severity` (`severity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='统一漏洞检测结果';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scan_waf` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `app_id` int(11) NOT NULL COMMENT '目标ID',
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间',
  `url` varchar(100) COLLATE utf8mb4_bin NOT NULL COMMENT '检测URL',
  `detected` varchar(10) COLLATE utf8mb4_bin NOT NULL COMMENT '是否识别到WAF 0否 1是',
  `firewall` varchar(100) COLLATE utf8mb4_bin NOT NULL COMMENT 'WAF名称',
  `manufacturer` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '厂商',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `idx_app` (`app_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin ROW_FORMAT=DYNAMIC COMMENT='WAF识别结果';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scan_weak_pass` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `host_id` int(11) NOT NULL COMMENT '主机ID',
  `type` char(10) COLLATE utf8mb4_bin NOT NULL DEFAULT 'ssh' COMMENT '服务类型',
  `username` varchar(50) COLLATE utf8mb4_bin NOT NULL COMMENT '用户名',
  `password` varchar(50) COLLATE utf8mb4_bin NOT NULL COMMENT '口令',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间',
  `app_id` int(11) NOT NULL DEFAULT '0' COMMENT '目标ID',
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `idx_host` (`host_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin ROW_FORMAT=DYNAMIC COMMENT='弱口令爆破结果';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `system_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '配置名称',
  `key` varchar(30) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '配置键',
  `value` varchar(512) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '配置值',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `idx_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin ROW_FORMAT=DYNAMIC COMMENT='系统配置';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `task_scan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tool` varchar(50) COLLATE utf8mb4_bin NOT NULL COMMENT '任务类型',
  `ext_info` text COLLATE utf8mb4_bin COMMENT '任务扩展信息',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态',
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `idx_tool` (`tool`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin ROW_FORMAT=DYNAMIC COMMENT='扫描任务';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(20) COLLATE utf8mb4_bin NOT NULL COMMENT '用户名',
  `password` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '密码',
  `salt` varchar(20) COLLATE utf8mb4_bin NOT NULL COMMENT '加密盐',
  `nickname` varchar(20) COLLATE utf8mb4_bin NOT NULL COMMENT '昵称',
  `auth_group_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户组ID',
  `created_at` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `last_login_ip` char(20) COLLATE utf8mb4_bin NOT NULL COMMENT '最后登录IP',
  `last_login_time` int(11) NOT NULL DEFAULT '0' COMMENT '最后登录时间',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '软删除标记 0未删 1已删',
  `sex` tinyint(1) NOT NULL DEFAULT '0' COMMENT '性别',
  `phone` char(11) COLLATE utf8mb4_bin NOT NULL COMMENT '手机号',
  `dd_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '钉钉token',
  `email` char(50) COLLATE utf8mb4_bin NOT NULL COMMENT '邮箱',
  `token` char(32) COLLATE utf8mb4_bin NOT NULL COMMENT '令牌',
  `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '主页url',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `idx_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin ROW_FORMAT=DYNAMIC COMMENT='用户';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` char(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `create_time` datetime NOT NULL,
  `content` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '详情',
  `type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '操作类型',
  `ip` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `idx_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin ROW_FORMAT=DYNAMIC COMMENT='用户操作日志';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;


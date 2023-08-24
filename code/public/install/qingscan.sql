/*
 Navicat Premium Data Transfer

 Source Server         : localhost_3306
 Source Server Type    : MySQL
 Source Server Version : 50726 (5.7.26)
 Source Host           : 127.0.0.1:3306
 Source Schema         : qingscan

 Target Server Type    : MySQL
 Target Server Version : 50726 (5.7.26)
 File Encoding         : 65001

 Date: 24/08/2023 23:25:55
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for app
-- ----------------------------
DROP TABLE IF EXISTS `app`;
CREATE TABLE `app`  (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `status` tinyint(4) NULL DEFAULT 1,
                        `name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                        `url` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                        `create_time` datetime NULL DEFAULT CURRENT_TIMESTAMP,
                        `crawler_time` datetime NULL DEFAULT '2000-01-01 00:00:00',
                        `awvs_scan_time` datetime NULL DEFAULT '2000-01-01 00:00:00',
                        `subdomain_time` datetime NULL DEFAULT '2000-01-01 00:00:00',
                        `is_delete` tinyint(1) NOT NULL DEFAULT 0,
                        `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT '',
                        `password` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT '',
                        `whatweb_scan_time` datetime NULL DEFAULT '2000-01-01 00:00:00',
                        `subdomain_scan_time` datetime NULL DEFAULT '2000-01-01 00:00:00' COMMENT 'OneForAll子域名扫描时间',
                        `screenshot_time` datetime NULL DEFAULT '2000-01-01 00:00:00' COMMENT '截图时间',
                        `xray_scan_time` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
                        `dirmap_scan_time` datetime NULL DEFAULT '2000-01-01 00:00:00',
                        `user_id` int(10) NOT NULL DEFAULT 0,
                        `wafw00f_scan_time` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
                        `jietu_path` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                        `is_intranet` tinyint(1) NOT NULL DEFAULT 0,
                        `nuclei_scan_time` datetime NULL DEFAULT '2000-01-01 00:00:00',
                        `dismap_scan_time` datetime NULL DEFAULT '2000-01-01 00:00:00',
                        `crawlergo_scan_time` datetime NULL DEFAULT '2000-01-01 00:00:00',
                        `vulmap_scan_time` datetime NULL DEFAULT '2000-01-01 00:00:00',
                        `xray_agent_port` int(10) NOT NULL DEFAULT 0 COMMENT 'xray被动代理端口',
                        `agent_time` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
                        `agent_start_up` int(1) NOT NULL DEFAULT 0 COMMENT 'xray代理是否已启动',
                        PRIMARY KEY (`id`) USING BTREE,
                        UNIQUE INDEX `un_url`(`url`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = COMPACT;

-- ----------------------------
-- Table structure for app_crawlergo
-- ----------------------------
DROP TABLE IF EXISTS `app_crawlergo`;
CREATE TABLE `app_crawlergo`  (
                                  `id` int(10) NOT NULL AUTO_INCREMENT,
                                  `app_id` int(10) NOT NULL,
                                  `user_id` int(10) NOT NULL DEFAULT 0,
                                  `url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
                                  `method` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
                                  `accept` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
                                  `cache_control` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
                                  `cookie` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
                                  `referer` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
                                  `spider_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
                                  `user_agent` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
                                  `data` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
                                  `source` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
                                  `create_time` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
                                  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin COMMENT = 'url收集' ROW_FORMAT = COMPACT;

-- ----------------------------
-- Table structure for app_dirmap
-- ----------------------------
DROP TABLE IF EXISTS `app_dirmap`;
CREATE TABLE `app_dirmap`  (
                               `id` int(10) NOT NULL AUTO_INCREMENT,
                               `app_id` int(10) NOT NULL,
                               `code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '状态码',
                               `size` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '大小kb',
                               `type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '类型',
                               `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'url地址',
                               `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                               `user_id` int(10) NOT NULL DEFAULT 0,
                               PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = COMPACT;

-- ----------------------------
-- Table structure for app_dismap
-- ----------------------------
DROP TABLE IF EXISTS `app_dismap`;
CREATE TABLE `app_dismap`  (
                               `id` int(10) NOT NULL AUTO_INCREMENT,
                               `app_id` int(10) NOT NULL DEFAULT 0,
                               `user_id` int(10) NOT NULL DEFAULT 0,
                               `result` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL COMMENT '结果',
                               `create_time` datetime NULL DEFAULT '2000-01-01 00:00:00',
                               PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = COMPACT;

-- ----------------------------
-- Table structure for app_info
-- ----------------------------
DROP TABLE IF EXISTS `app_info`;
CREATE TABLE `app_info`  (
                             `app_id` int(11) NOT NULL DEFAULT 0,
                             `cms` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                             `server` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                             `statuscode` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                             `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                             `length` int(11) NULL DEFAULT NULL,
                             `code` int(3) NOT NULL DEFAULT 0 COMMENT '状态码',
                             `page_title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '网页标题',
                             `header` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL COMMENT '网页header',
                             `icon` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '网页ICON',
                             `url_screenshot` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT 'url屏幕截图',
                             PRIMARY KEY (`app_id`) USING BTREE,
                             CONSTRAINT `app_id` FOREIGN KEY (`app_id`) REFERENCES `app` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = COMPACT;

-- ----------------------------
-- Table structure for app_nuclei
-- ----------------------------
DROP TABLE IF EXISTS `app_nuclei`;
CREATE TABLE `app_nuclei`  (
                               `id` int(10) NOT NULL AUTO_INCREMENT,
                               `app_id` int(10) NOT NULL,
                               `user_id` int(10) NOT NULL,
                               `template` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT '',
                               `template_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT '',
                               `template_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT '',
                               `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT '',
                               `author` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT '' COMMENT '作者',
                               `tags` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT '' COMMENT '标签',
                               `description` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT '' COMMENT '描述',
                               `reference` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT '',
                               `severity` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT '',
                               `type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT '' COMMENT '协议类型',
                               `host` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT '',
                               `matched_at` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT '',
                               `extracted_results` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT '',
                               `ip` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT '',
                               `create_time` datetime NULL DEFAULT NULL,
                               `curl_command` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                               `status` tinyint(1) NOT NULL DEFAULT 0,
                               PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 106 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = COMPACT;

-- ----------------------------
-- Table structure for app_vulmap
-- ----------------------------
DROP TABLE IF EXISTS `app_vulmap`;
CREATE TABLE `app_vulmap`  (
                               `id` int(10) NOT NULL AUTO_INCREMENT,
                               `app_id` int(10) NOT NULL,
                               `user_id` int(10) NOT NULL,
                               `author` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '作者',
                               `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '描述',
                               `host` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '主机',
                               `port` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '端口',
                               `param` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '参数',
                               `request` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
                               `payload` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
                               `response` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
                               `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
                               `plugin` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '漏洞',
                               `target` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
                               `vuln_class` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '漏洞名称',
                               `create_time` int(10) NOT NULL DEFAULT 0,
                               PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin COMMENT = '漏洞扫描' ROW_FORMAT = COMPACT;

-- ----------------------------
-- Table structure for app_wafw00f
-- ----------------------------
DROP TABLE IF EXISTS `app_wafw00f`;
CREATE TABLE `app_wafw00f`  (
                                `id` int(10) NOT NULL AUTO_INCREMENT,
                                `app_id` int(10) NOT NULL,
                                `user_id` int(10) NOT NULL DEFAULT 0,
                                `create_time` datetime NULL DEFAULT NULL,
                                `url` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
                                `detected` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
                                `firewall` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'waf防火墙名称',
                                `manufacturer` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '厂商',
                                PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin COMMENT = 'waf指纹识别' ROW_FORMAT = COMPACT;

-- ----------------------------
-- Table structure for app_whatweb
-- ----------------------------
DROP TABLE IF EXISTS `app_whatweb`;
CREATE TABLE `app_whatweb`  (
                                `id` int(10) NOT NULL AUTO_INCREMENT,
                                `app_id` int(10) NOT NULL,
                                `target` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                                `http_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                                `request_config` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                                `plugins` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
                                `create_time` datetime NULL DEFAULT '2000-01-01 00:00:00',
                                `poc_scan_time` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
                                `user_id` int(10) NOT NULL DEFAULT 0,
                                PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin COMMENT = 'web指纹识别' ROW_FORMAT = COMPACT;

-- ----------------------------
-- Table structure for app_whatweb_poc
-- ----------------------------
DROP TABLE IF EXISTS `app_whatweb_poc`;
CREATE TABLE `app_whatweb_poc`  (
                                    `id` int(10) NOT NULL AUTO_INCREMENT,
                                    `whatweb_id` int(10) NOT NULL,
                                    `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
                                    `app_id` int(10) NOT NULL,
                                    `key` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
                                    `value` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
                                    `result` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
                                    `create_time` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
                                    `user_id` int(10) NOT NULL,
                                    PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = COMPACT;

-- ----------------------------
-- Table structure for app_xray_agent_port
-- ----------------------------
DROP TABLE IF EXISTS `app_xray_agent_port`;
CREATE TABLE `app_xray_agent_port`  (
                                        `id` int(10) NOT NULL AUTO_INCREMENT,
                                        `app_id` int(10) NOT NULL,
                                        `xray_agent_prot` int(10) NOT NULL COMMENT '代理端口',
                                        `create_time` datetime NULL DEFAULT '2000-01-01 00:00:00',
                                        `start_up` int(1) NOT NULL DEFAULT 0 COMMENT '是否已启动',
                                        `is_get_result` int(1) NOT NULL DEFAULT 0 COMMENT '是否已获取结果',
                                        PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin COMMENT = '本地代理' ROW_FORMAT = COMPACT;

-- ----------------------------
-- Table structure for asm_domain
-- ----------------------------
DROP TABLE IF EXISTS `asm_domain`;
CREATE TABLE `asm_domain`  (
                               `id` int(11) NOT NULL,
                               `domain` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                               PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for asm_host
-- ----------------------------
DROP TABLE IF EXISTS `asm_host`;
CREATE TABLE `asm_host`  (
                             `id` int(11) NOT NULL AUTO_INCREMENT,
                             `app_id` int(11) NULL DEFAULT 0,
                             `domain` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                             `host` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
                             `status` tinyint(4) NOT NULL DEFAULT 1,
                             `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                             `isp` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '运营商',
                             `country` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '国家',
                             `region` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '省份',
                             `city` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '市',
                             `area` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '地区',
                             `hydra_scan_time` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
                             `port_scan_time` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
                             `ip_scan_time` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
                             `is_delete` int(4) NOT NULL DEFAULT 0,
                             `user_id` int(10) NOT NULL DEFAULT 0,
                             `unauthorize_scan_time` datetime NOT NULL DEFAULT '2000-01-01 00:00:00' COMMENT '未授权扫描时间',
                             PRIMARY KEY (`id`) USING BTREE,
                             UNIQUE INDEX `un_host`(`domain`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = COMPACT;

-- ----------------------------
-- Table structure for asm_host_port
-- ----------------------------
DROP TABLE IF EXISTS `asm_host_port`;
CREATE TABLE `asm_host_port`  (
                                  `id` int(11) NOT NULL AUTO_INCREMENT,
                                  `port` int(11) NOT NULL DEFAULT 0,
                                  `host` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '0',
                                  `type` char(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                                  `service` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                                  `is_close` tinyint(4) NULL DEFAULT 0 COMMENT '是否关闭',
                                  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                  `update_time` datetime NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
                                  `os` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL COMMENT '操作系统',
                                  `html` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
                                  `headers` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
                                  `is_delete` tinyint(1) NOT NULL DEFAULT 0,
                                  `scan_time` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
                                  `user_id` int(10) NOT NULL DEFAULT 0,
                                  `app_id` int(11) NOT NULL DEFAULT 0,
                                  PRIMARY KEY (`id`) USING BTREE,
                                  UNIQUE INDEX `un_port`(`host`, `port`, `type`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = COMPACT;

-- ----------------------------
-- Table structure for asm_ip_port
-- ----------------------------
DROP TABLE IF EXISTS `asm_ip_port`;
CREATE TABLE `asm_ip_port`  (
                                `id` int(11) NOT NULL AUTO_INCREMENT,
                                `method` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
                                `app_id` int(11) NULL DEFAULT 0,
                                `url` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                                `status` tinyint(4) NOT NULL DEFAULT 1,
                                `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                `header` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                                `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
                                `response_header` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
                                `hash` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                                `scan_time` datetime NULL DEFAULT '2000-01-01 00:00:00',
                                `scheme` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                                `host` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                                `path` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                                `query` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                                `title` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                                `keywords` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                                `description` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                                `content_type` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                                `extension` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                                `is_delete` tinyint(1) NOT NULL DEFAULT 0,
                                `sqlmap_scan_time` datetime NULL DEFAULT '2000-01-01 00:00:00',
                                `id_card` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
                                `email` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
                                `phone` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
                                `icp` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
                                `user_id` int(10) NOT NULL DEFAULT 0,
                                PRIMARY KEY (`id`) USING BTREE,
                                UNIQUE INDEX `un_url`(`hash`) USING BTREE,
                                INDEX `appid`(`app_id`) USING BTREE,
                                CONSTRAINT `asm_ip_port_ibfk_1` FOREIGN KEY (`app_id`) REFERENCES `app` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = COMPACT;

-- ----------------------------
-- Table structure for asm_sub_domain
-- ----------------------------
DROP TABLE IF EXISTS `asm_sub_domain`;
CREATE TABLE `asm_sub_domain`  (
                                   `id` int(11) NOT NULL,
                                   `domain` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                                   PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for asm_urls
-- ----------------------------
DROP TABLE IF EXISTS `asm_urls`;
CREATE TABLE `asm_urls`  (
                             `id` int(11) NOT NULL AUTO_INCREMENT,
                             `method` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
                             `app_id` int(11) NULL DEFAULT 0,
                             `url` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                             `status` tinyint(4) NOT NULL DEFAULT 1,
                             `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                             `header` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                             `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
                             `response_header` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
                             `hash` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                             `scan_time` datetime NULL DEFAULT '2000-01-01 00:00:00',
                             `scheme` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                             `host` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                             `path` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                             `query` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                             `title` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                             `keywords` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                             `description` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                             `content_type` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                             `extension` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                             `is_delete` tinyint(1) NOT NULL DEFAULT 0,
                             `sqlmap_scan_time` datetime NULL DEFAULT '2000-01-01 00:00:00',
                             `id_card` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
                             `email` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
                             `phone` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
                             `icp` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
                             `user_id` int(10) NOT NULL DEFAULT 0,
                             PRIMARY KEY (`id`) USING BTREE,
                             UNIQUE INDEX `un_url`(`hash`) USING BTREE,
                             INDEX `appid`(`app_id`) USING BTREE,
                             CONSTRAINT `appid` FOREIGN KEY (`app_id`) REFERENCES `app` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = COMPACT;

-- ----------------------------
-- Table structure for auth_group
-- ----------------------------
DROP TABLE IF EXISTS `auth_group`;
CREATE TABLE `auth_group`  (
                               `auth_group_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '用户组表',
                               `title` char(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'title:用户组中文名称',
                               `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '为1正常，为0禁用',
                               `rules` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT 'rules：用户组拥有的规则id， 多个规则\",\"隔开',
                               `update_time` int(10) NOT NULL DEFAULT 0 COMMENT '修改时间',
                               `is_delete` tinyint(1) NOT NULL DEFAULT 0,
                               `created_at` int(10) NOT NULL DEFAULT 0 COMMENT '添加时间',
                               PRIMARY KEY (`auth_group_id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 10 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for auth_rule
-- ----------------------------
DROP TABLE IF EXISTS `auth_rule`;
CREATE TABLE `auth_rule`  (
                              `auth_rule_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '自增id',
                              `href` char(127) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '地址   控制器/方法',
                              `title` char(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '名称',
                              `is_delete` tinyint(1) NOT NULL DEFAULT 0 COMMENT '删除状态  1删除  0正常',
                              `is_open_auth` tinyint(2) NOT NULL DEFAULT 1 COMMENT '是否验证权限',
                              `pid` int(5) NOT NULL DEFAULT 0 COMMENT '父栏目ID',
                              `sort` int(11) NOT NULL DEFAULT 0 COMMENT '排序',
                              `created_at` int(11) NOT NULL DEFAULT 0 COMMENT '添加时间',
                              `menu_status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '菜单状态  1显示  0隐藏',
                              `update_time` int(10) NOT NULL DEFAULT 0 COMMENT '更新时间',
                              `level` tinyint(4) NOT NULL DEFAULT 1 COMMENT '等级  1顶级，一级，2.二级，3.三级',
                              `delete_time` int(10) NOT NULL DEFAULT 0,
                              `icon_url` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '图标样式',
                              PRIMARY KEY (`auth_rule_id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 402 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '权限列表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for awvs_app
-- ----------------------------
DROP TABLE IF EXISTS `awvs_app`;
CREATE TABLE `awvs_app`  (
                             `id` int(11) NOT NULL AUTO_INCREMENT,
                             `app_id` int(11) NULL DEFAULT NULL,
                             `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                             `criticality` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                             `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                             `fqdn` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                             `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                             `domain` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                             `target_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                             `target_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                             `user_id` int(10) NOT NULL,
                             PRIMARY KEY (`id`) USING BTREE,
                             UNIQUE INDEX `un_target`(`target_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = COMPACT;

-- ----------------------------
-- Table structure for awvs_vuln
-- ----------------------------
DROP TABLE IF EXISTS `awvs_vuln`;
CREATE TABLE `awvs_vuln`  (
                              `id` int(11) NOT NULL AUTO_INCREMENT,
                              `affects_detail` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                              `affects_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                              `app` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                              `confidence` int(11) NULL DEFAULT NULL,
                              `criticality` int(11) NULL DEFAULT NULL,
                              `last_seen` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                              `loc_id` int(11) NULL DEFAULT NULL,
                              `severity` int(11) NULL DEFAULT NULL,
                              `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                              `tags` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                              `target_id` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                              `vt_created` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                              `vt_id` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                              `vt_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                              `vt_updated` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                              `vuln_id` bigint(32) NULL DEFAULT NULL,
                              `cvss2` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                              `cvss_score` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                              `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                              `details` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                              `highlights` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                              `impact` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                              `long_description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                              `recommendation` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                              `references` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                              `request` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
                              `response_info` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                              `source` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                              `create_time` datetime NULL DEFAULT CURRENT_TIMESTAMP,
                              `check_status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '审核状态',
                              `is_delete` tinyint(1) NOT NULL DEFAULT 0,
                              `user_id` int(10) NOT NULL DEFAULT 0,
                              `app_id` int(11) NULL DEFAULT 0,
                              PRIMARY KEY (`id`) USING BTREE,
                              UNIQUE INDEX `un_vuln_id`(`vuln_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = COMPACT;

-- ----------------------------
-- Table structure for bug
-- ----------------------------
DROP TABLE IF EXISTS `bug`;
CREATE TABLE `bug`  (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `status` tinyint(4) NULL DEFAULT 1,
                        `name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                        `create_time` datetime NULL DEFAULT CURRENT_TIMESTAMP,
                        `detail` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL COMMENT '漏洞详情',
                        `category` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                        `host` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                        `check_status` tinyint(1) NOT NULL DEFAULT 0,
                        PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = COMPACT;

-- ----------------------------
-- Table structure for code
-- ----------------------------
DROP TABLE IF EXISTS `code`;
CREATE TABLE `code`  (
                         `id` int(11) NOT NULL AUTO_INCREMENT,
                         `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                         `ssh_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                         `desc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                         `hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                         `scan_time` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
                         `sonar_scan_time` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
                         `kunlun_scan_time` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
                         `semgrep_scan_time` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
                         `pulling_mode` char(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '拉取方式（支持SSH、HTTPS）',
                         `is_private` tinyint(1) NOT NULL DEFAULT 0,
                         `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '用户名',
                         `password` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '密码',
                         `private_key` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
                         `is_delete` tinyint(1) NOT NULL DEFAULT 0,
                         `composer_scan_time` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
                         `java_scan_time` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
                         `python_scan_time` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
                         `user_id` int(10) NOT NULL DEFAULT 0,
                         `star` char(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
                         `webshell_scan_time` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
                         `create_time` datetime NULL DEFAULT CURRENT_TIMESTAMP,
                         `is_online` int(10) NOT NULL DEFAULT 1 COMMENT '1线上   2本地',
                         `mobsfscan_scan_time` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
                         `project_type` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1php 2java 3python 4go 5app 6其他',
                         `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1启用 2禁用',
                         `murphysec_scan_time` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
                         PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = COMPACT;

-- ----------------------------
-- Table structure for code_check
-- ----------------------------
DROP TABLE IF EXISTS `code_check`;
CREATE TABLE `code_check`  (
                               `id` int(11) NOT NULL AUTO_INCREMENT,
                               `app_id` int(11) NULL DEFAULT 0,
                               `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
                               `status` tinyint(4) NULL DEFAULT 1,
                               `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                               `files` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
                               `author` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                               `project_hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                               `version` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                               `project_id` int(11) NULL DEFAULT NULL,
                               PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = COMPACT;

-- ----------------------------
-- Table structure for code_check_notice
-- ----------------------------
DROP TABLE IF EXISTS `code_check_notice`;
CREATE TABLE `code_check_notice`  (
                                      `id` int(11) NOT NULL AUTO_INCREMENT,
                                      `check_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                                      `end` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                                      `extra` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
                                      `path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                                      `start` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                                      `version` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                                      PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = COMPACT;

-- ----------------------------
-- Table structure for code_composer
-- ----------------------------
DROP TABLE IF EXISTS `code_composer`;
CREATE TABLE `code_composer`  (
                                  `id` int(11) NOT NULL AUTO_INCREMENT,
                                  `name` varchar(2048) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                                  `version` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                                  `source` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                                  `dist` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
                                  `require` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
                                  `conflict` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                                  `require_dev` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
                                  `suggest` varchar(2550) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                                  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                                  `extra` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                                  `autoload` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                                  `notification_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                                  `license` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                                  `authors` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
                                  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                                  `keywords` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
                                  `funding` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
                                  `time` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                                  `code_id` int(11) NULL DEFAULT NULL,
                                  `user_id` int(11) NULL DEFAULT NULL,
                                  `create_time` datetime NULL DEFAULT CURRENT_TIMESTAMP,
                                  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = COMPACT;

-- ----------------------------
-- Table structure for code_hook
-- ----------------------------
DROP TABLE IF EXISTS `code_hook`;
CREATE TABLE `code_hook`  (
                              `id` int(11) NOT NULL AUTO_INCREMENT,
                              `create_time` datetime NULL DEFAULT NULL,
                              `update_time` datetime NULL DEFAULT NULL,
                              `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                              `path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                              `gitlab_id` int(11) NULL DEFAULT NULL,
                              `project` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                              PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = COMPACT;

-- ----------------------------
-- Table structure for code_java
-- ----------------------------
DROP TABLE IF EXISTS `code_java`;
CREATE TABLE `code_java`  (
                              `id` int(10) NOT NULL AUTO_INCREMENT,
                              `code_id` int(10) NOT NULL,
                              `modelVersion` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                              `groupId` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                              `artifactId` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                              `version` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                              `modules` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
                              `packaging` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                              `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                              `comment` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                              `url` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                              `properties` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
                              `dependencies` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
                              `build` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
                              `create_time` datetime NULL DEFAULT NULL,
                              `user_id` int(10) NOT NULL DEFAULT 0,
                              PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = COMPACT;

-- ----------------------------
-- Table structure for code_python
-- ----------------------------
DROP TABLE IF EXISTS `code_python`;
CREATE TABLE `code_python`  (
                                `id` int(10) NOT NULL AUTO_INCREMENT,
                                `code_id` int(10) NOT NULL,
                                `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
                                `create_time` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
                                `user_id` int(10) NOT NULL DEFAULT 0,
                                PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = COMPACT;

-- ----------------------------
-- Table structure for code_webshell
-- ----------------------------
DROP TABLE IF EXISTS `code_webshell`;
CREATE TABLE `code_webshell`  (
                                  `id` int(10) NOT NULL AUTO_INCREMENT,
                                  `code_id` int(10) NOT NULL,
                                  `create_time` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
                                  `check_status` int(1) NOT NULL DEFAULT 0 COMMENT '审核状态',
                                  `user_id` int(10) NOT NULL DEFAULT 0,
                                  `type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '类型',
                                  `filename` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '文件路径',
                                  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = COMPACT;

-- ----------------------------
-- Table structure for config
-- ----------------------------
DROP TABLE IF EXISTS `config`;
CREATE TABLE `config`  (
                           `id` int(11) NOT NULL AUTO_INCREMENT,
                           `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL COMMENT '配置名称',
                           `status` tinyint(4) NULL DEFAULT 1 COMMENT '状态',
                           `type` tinyint(4) NULL DEFAULT 1 COMMENT '类型',
                           `data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL COMMENT '配置内容',
                           `create_time` datetime NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                           PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = COMPACT;

-- ----------------------------
-- Table structure for fortify
-- ----------------------------
DROP TABLE IF EXISTS `fortify`;
CREATE TABLE `fortify`  (
                            `id` int(11) NOT NULL AUTO_INCREMENT,
                            `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                            `Category` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                            `Folder` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                            `Kingdom` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                            `Abstract` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
                            `Friority` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                            `Primary` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
                            `Source` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
                            `code_id` int(11) NULL DEFAULT NULL,
                            `comment` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                            `check_status` int(5) NULL DEFAULT 0 COMMENT '0 未处理   1 有效漏洞  2 无效漏洞',
                            `Source_filename` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                            `Primary_filename` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                            `hash` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                            `is_delete` tinyint(1) NOT NULL DEFAULT 0,
                            `user_id` int(10) NOT NULL DEFAULT 0,
                            `update_time` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
                            PRIMARY KEY (`id`) USING BTREE,
                            UNIQUE INDEX `un_hash`(`hash`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = COMPACT;

-- ----------------------------
-- Table structure for github_keyword_monitor
-- ----------------------------
DROP TABLE IF EXISTS `github_keyword_monitor`;
CREATE TABLE `github_keyword_monitor`  (
                                           `id` int(10) NOT NULL AUTO_INCREMENT,
                                           `app_id` int(10) NOT NULL DEFAULT 0,
                                           `user_id` int(10) NOT NULL DEFAULT 0,
                                           `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '关键字',
                                           `create_time` datetime NULL DEFAULT '2000-01-01 00:00:00',
                                           `update_time` datetime NULL DEFAULT '2000-01-01 00:00:00',
                                           `scan_time` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
                                           PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin COMMENT = 'github关键字设置' ROW_FORMAT = COMPACT;

-- ----------------------------
-- Table structure for github_keyword_monitor_notice
-- ----------------------------
DROP TABLE IF EXISTS `github_keyword_monitor_notice`;
CREATE TABLE `github_keyword_monitor_notice`  (
                                                  `id` int(10) NOT NULL AUTO_INCREMENT,
                                                  `parent_id` int(10) NOT NULL DEFAULT 0 COMMENT '关键字表id',
                                                  `app_id` int(10) NOT NULL DEFAULT 0,
                                                  `user_id` int(10) NOT NULL DEFAULT 0,
                                                  `keyword` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
                                                  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
                                                  `path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
                                                  `html_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
                                                  `create_time` datetime NULL DEFAULT NULL,
                                                  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin COMMENT = 'github关键字监控结果' ROW_FORMAT = COMPACT;

-- ----------------------------
-- Table structure for github_notice
-- ----------------------------
DROP TABLE IF EXISTS `github_notice`;
CREATE TABLE `github_notice`  (
                                  `id` int(10) NOT NULL AUTO_INCREMENT,
                                  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
                                  `level` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
                                  `cve_id` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
                                  `cwes` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
                                  `cvss_score` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
                                  `github_release_date` datetime NULL DEFAULT NULL,
                                  `references` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL COMMENT '参考资料',
                                  `desc` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
                                  `package` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
                                  `hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                                  `create_time` datetime NULL DEFAULT '2000-01-01 00:00:00',
                                  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = COMPACT;

-- ----------------------------
-- Table structure for group
-- ----------------------------
DROP TABLE IF EXISTS `group`;
CREATE TABLE `group`  (
                          `id` int(11) NOT NULL AUTO_INCREMENT,
                          PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = COMPACT;

-- ----------------------------
-- Table structure for host_hydra_scan_details
-- ----------------------------
DROP TABLE IF EXISTS `host_hydra_scan_details`;
CREATE TABLE `host_hydra_scan_details`  (
                                            `id` int(10) NOT NULL AUTO_INCREMENT,
                                            `host_id` int(10) NOT NULL,
                                            `type` char(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT 'ssh' COMMENT '类型  如：ssh、mysql等',
                                            `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
                                            `password` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
                                            `create_time` datetime NULL DEFAULT NULL,
                                            `app_id` int(10) NOT NULL DEFAULT 0,
                                            `user_id` int(10) NOT NULL,
                                            PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = COMPACT;

-- ----------------------------
-- Table structure for host_unauthorized
-- ----------------------------
DROP TABLE IF EXISTS `host_unauthorized`;
CREATE TABLE `host_unauthorized`  (
                                      `id` int(10) NOT NULL AUTO_INCREMENT,
                                      `host_id` int(10) NOT NULL,
                                      `ip` char(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '',
                                      `port` int(5) NOT NULL DEFAULT 0,
                                      `text` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
                                      `user_id` int(10) NOT NULL DEFAULT 0,
                                      `is_delete` tinyint(1) NOT NULL DEFAULT 0,
                                      `status` tinyint(1) NOT NULL DEFAULT 1,
                                      `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                      PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for log
-- ----------------------------
DROP TABLE IF EXISTS `log`;
CREATE TABLE `log`  (
                        `id` int(10) NOT NULL AUTO_INCREMENT,
                        `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
                        `app` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
                        `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                        PRIMARY KEY (`id`) USING BTREE,
                        INDEX `time`(`create_time`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = COMPACT;

-- ----------------------------
-- Table structure for mobsfscan
-- ----------------------------
DROP TABLE IF EXISTS `mobsfscan`;
CREATE TABLE `mobsfscan`  (
                              `id` int(10) NOT NULL AUTO_INCREMENT,
                              `code_id` int(10) NOT NULL DEFAULT 0,
                              `user_id` int(10) NOT NULL DEFAULT 0,
                              `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
                              `files` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
                              `cwe` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
                              `description` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
                              `input_case` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
                              `masvs` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
                              `owasp_mobile` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
                              `reference` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
                              `severity` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
                              `create_time` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
                              `check_status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0未审核  1有效漏洞  2无效漏洞',
                              `update_time` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
                              PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for murphysec
-- ----------------------------
DROP TABLE IF EXISTS `murphysec`;
CREATE TABLE `murphysec`  (
                              `id` int(10) NOT NULL AUTO_INCREMENT,
                              `user_id` int(10) NOT NULL DEFAULT 0,
                              `code_id` int(10) NULL DEFAULT 0,
                              `comp_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '缺陷组件名称',
                              `version` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '当前版本',
                              `min_fixed_version` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '最小修复版本',
                              `show_level` tinyint(1) NOT NULL COMMENT '修复建议等级 1强烈建议修复 2建议修复 3可选修复',
                              `language` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '语言',
                              `solutions` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '修复方案',
                              `repair_status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '修复状态 1未修复 2已修复',
                              `create_time` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
                              `update_time` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
                              PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for murphysec_vuln
-- ----------------------------
DROP TABLE IF EXISTS `murphysec_vuln`;
CREATE TABLE `murphysec_vuln`  (
                                   `id` int(10) NOT NULL AUTO_INCREMENT,
                                   `user_id` int(10) NOT NULL,
                                   `code_id` int(10) NOT NULL,
                                   `murphysec_id` int(10) NOT NULL,
                                   `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
                                   `cve_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT 'CVE编号',
                                   `suggest_level` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '处置建议',
                                   `poc` tinyint(1) NOT NULL DEFAULT 0,
                                   `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '漏洞描述',
                                   `affected_version` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '影响版本',
                                   `min_fixed_version` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '最小修复版本',
                                   `solutions` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '修复建议',
                                   `influence` int(3) NOT NULL DEFAULT 0 COMMENT '影响指数',
                                   `level` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '漏洞级别',
                                   `vuln_path` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
                                   `publish_time` int(11) NOT NULL COMMENT '发布时间',
                                   PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for node
-- ----------------------------
DROP TABLE IF EXISTS `node`;
CREATE TABLE `node`  (
                         `id` int(11) NOT NULL AUTO_INCREMENT,
                         `userid` int(10) NOT NULL DEFAULT 0,
                         `status` tinyint(1) NOT NULL DEFAULT 0,
                         `hostname` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
                         `create_time` datetime NULL DEFAULT NULL,
                         PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = COMPACT;

-- ----------------------------
-- Table structure for one_for_all
-- ----------------------------
DROP TABLE IF EXISTS `one_for_all`;
CREATE TABLE `one_for_all`  (
                                `id` int(10) NOT NULL AUTO_INCREMENT,
                                `app_id` int(10) NOT NULL DEFAULT 0,
                                `alive` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT '',
                                `request` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT '',
                                `resolve` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT '',
                                `url` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                                `subdomain` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                                `port` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT '',
                                `level` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT '',
                                `cname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                                `ip` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                                `public` char(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT '',
                                `cdn` char(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT '',
                                `status` char(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT '',
                                `reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                                `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                                `banner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                                `header` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                                `history` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                                `response` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                                `ip_times` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                                `cname_times` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                                `ttl` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                                `cidr` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                                `asn` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                                `org` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                                `addr` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                                `isp` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                                `resolver` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                                `module` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                                `source` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                                `elapse` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                                `find` char(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT '',
                                `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                `user_id` int(10) NOT NULL DEFAULT 0,
                                PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = COMPACT;

-- ----------------------------
-- Table structure for plugin
-- ----------------------------
DROP TABLE IF EXISTS `plugin`;
CREATE TABLE `plugin`  (
                           `id` int(10) NOT NULL AUTO_INCREMENT,
                           `user_id` int(10) NOT NULL DEFAULT 0,
                           `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '插件名称',
                           `cmd` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '插件执行命令',
                           `result_file` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL COMMENT '结果文件存放位置',
                           `create_time` datetime NULL DEFAULT '2000-01-01 00:00:00' COMMENT '添加时间',
                           `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0禁用  1启用',
                           `is_delete` tinyint(1) NOT NULL DEFAULT 0,
                           `result_type` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT '' COMMENT 'json、csv、txt',
                           `update_time` datetime NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
                           `tool_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL COMMENT '工具存放位置',
                           `scan_type` int(4) NULL DEFAULT 0 COMMENT '0 app 1 host 2 code  3 url',
                           `type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1执行插件扫描   2结果分析',
                           PRIMARY KEY (`id`) USING BTREE,
                           UNIQUE INDEX `un_name`(`name`, `scan_type`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin COMMENT = '自定义插件' ROW_FORMAT = COMPACT;

-- ----------------------------
-- Table structure for plugin_scan_log
-- ----------------------------
DROP TABLE IF EXISTS `plugin_scan_log`;
CREATE TABLE `plugin_scan_log`  (
                                    `id` int(10) NOT NULL AUTO_INCREMENT,
                                    `app_id` int(10) NOT NULL,
                                    `plugin_id` int(10) NOT NULL,
                                    `user_id` int(10) NOT NULL DEFAULT 0,
                                    `content` varchar(5000) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL COMMENT '扫描结果内容',
                                    `create_time` datetime NULL DEFAULT CURRENT_TIMESTAMP,
                                    `check_status` tinyint(1) NOT NULL COMMENT '审核状态',
                                    `plugin_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL COMMENT '插件名称',
                                    `scan_type` int(255) NULL DEFAULT NULL COMMENT '0 app 1 host 2 code  3 url',
                                    `log_type` int(11) NULL DEFAULT 0 COMMENT '进度  0 开始扫描   1 完成   2 失败',
                                    `file_content` varchar(5000) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                                    `is_read` tinyint(1) NOT NULL DEFAULT 1 COMMENT '结果是否已读取   1未读  2已读取',
                                    `is_custom` int(10) NOT NULL DEFAULT 1 COMMENT '是否为自定义插件 1否  2是',
                                    PRIMARY KEY (`id`) USING BTREE,
                                    UNIQUE INDEX `un_id`(`app_id`, `plugin_name`, `log_type`, `scan_type`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin COMMENT = '自定义插件扫描结果' ROW_FORMAT = COMPACT;

-- ----------------------------
-- Table structure for plugin_store
-- ----------------------------
DROP TABLE IF EXISTS `plugin_store`;
CREATE TABLE `plugin_store`  (
                                 `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '自增id',
                                 `status` tinyint(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态 1 开启  0 禁用',
                                 `create_time` datetime NULL DEFAULT NULL COMMENT '插件安装时间',
                                 `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '插件标识名,英文字母(惟一)',
                                 `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '插件名称',
                                 `version` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '插件版本号',
                                 `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '插件描述',
                                 `code` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '兑换码',
                                 PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '插件表' ROW_FORMAT = COMPACT;

-- ----------------------------
-- Table structure for pocs_file
-- ----------------------------
DROP TABLE IF EXISTS `pocs_file`;
CREATE TABLE `pocs_file`  (
                              `id` int(11) NOT NULL AUTO_INCREMENT,
                              `cve_num` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                              `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                              `create_time` datetime NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
                              `status` tinyint(1) NOT NULL DEFAULT 1,
                              `tool` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 pocsuite3 1 xray 2 其他',
                              `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT 'POC内容',
                              `vul_id` int(11) NULL DEFAULT NULL,
                              PRIMARY KEY (`id`) USING BTREE,
                              UNIQUE INDEX `cve_poc`(`cve_num`, `name`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = COMPACT;

-- ----------------------------
-- Table structure for pocsuite3
-- ----------------------------
DROP TABLE IF EXISTS `pocsuite3`;
CREATE TABLE `pocsuite3`  (
                              `id` int(11) NOT NULL AUTO_INCREMENT,
                              `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                              `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                              `ssv_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                              `cms` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                              `version` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                              `is_max` int(255) NULL DEFAULT 0,
                              `tel` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL COMMENT '电话号码',
                              `regaddress` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL COMMENT '公司注册地址',
                              `ip` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL COMMENT 'IP地址',
                              `CompanyName` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL COMMENT '公司名字',
                              `SiteLicense` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                              `CompanyType` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                              `regcapital` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL COMMENT '注册资金',
                              `is_delete` tinyint(1) NOT NULL DEFAULT 0,
                              `user_id` int(10) NOT NULL DEFAULT 0,
                              PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = COMPACT;

-- ----------------------------
-- Table structure for process_safe
-- ----------------------------
DROP TABLE IF EXISTS `process_safe`;
CREATE TABLE `process_safe`  (
                                 `id` int(11) NOT NULL AUTO_INCREMENT,
                                 `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                                 `value` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                                 `status` int(4) NOT NULL DEFAULT 1 COMMENT '0 失效   1启用',
                                 `note` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL COMMENT '描述',
                                 `update_time` datetime NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
                                 `type` int(11) NULL DEFAULT 0 COMMENT '工具分类  0  黑盒扫描   1  白盒审计  2  专项扫描  3  系统其他',
                                 PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 45 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = COMPACT;

-- ----------------------------
-- Table structure for project_tools
-- ----------------------------
DROP TABLE IF EXISTS `project_tools`;
CREATE TABLE `project_tools`  (
                                  `id` int(10) NOT NULL AUTO_INCREMENT,
                                  `type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1app 2code',
                                  `project_id` int(10) NOT NULL DEFAULT 9 COMMENT '项目id',
                                  `tools_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '工具名称',
                                  `create_time` datetime NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
                                  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for proxy
-- ----------------------------
DROP TABLE IF EXISTS `proxy`;
CREATE TABLE `proxy`  (
                          `id` int(10) NOT NULL AUTO_INCREMENT,
                          `host` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'ip地址',
                          `port` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '端口号',
                          `status` int(4) NOT NULL DEFAULT 1 COMMENT '1 有效  0 无效',
                          `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                          PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin COMMENT = '代理表' ROW_FORMAT = COMPACT;

-- ----------------------------
-- Table structure for semgrep
-- ----------------------------
DROP TABLE IF EXISTS `semgrep`;
CREATE TABLE `semgrep`  (
                            `id` int(11) NOT NULL AUTO_INCREMENT,
                            `check_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                            `code_id` int(11) NULL DEFAULT NULL COMMENT '项目ID',
                            `create_time` datetime NULL DEFAULT CURRENT_TIMESTAMP,
                            `end_col` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                            `end_line` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL COMMENT '代码行号',
                            `end_offset` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                            `extra_is_ignored` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                            `extra_lines` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
                            `extra_message` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                            `extra_metadata` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
                            `extra_metavars` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
                            `extra_severity` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL COMMENT '危险等级',
                            `path` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL COMMENT '污染来源',
                            `start_col` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                            `start_line` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                            `start_offset` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                            `check_status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '审核状态',
                            `is_delete` tinyint(1) NOT NULL DEFAULT 0,
                            `user_id` int(10) NOT NULL DEFAULT 0,
                            `update_time` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
                            PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = COMPACT;

-- ----------------------------
-- Table structure for svn_project
-- ----------------------------
DROP TABLE IF EXISTS `svn_project`;
CREATE TABLE `svn_project`  (
                                `id` int(11) NOT NULL AUTO_INCREMENT,
                                `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                                `command` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                                `scan_time` datetime NULL DEFAULT '2000-01-01 14:14:14',
                                PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = COMPACT;

-- ----------------------------
-- Table structure for system_config
-- ----------------------------
DROP TABLE IF EXISTS `system_config`;
CREATE TABLE `system_config`  (
                                  `id` int(11) NOT NULL AUTO_INCREMENT,
                                  `name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL COMMENT '配置名称 如：百度token',
                                  `key` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                                  `value` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                                  `is_delete` tinyint(1) NOT NULL DEFAULT 0,
                                  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 9 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = COMPACT;

-- ----------------------------
-- Table structure for system_zanzhu
-- ----------------------------
DROP TABLE IF EXISTS `system_zanzhu`;
CREATE TABLE `system_zanzhu`  (
                                  `id` int(11) NOT NULL AUTO_INCREMENT,
                                  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                                  `amount` float NULL DEFAULT NULL,
                                  `time` date NULL DEFAULT NULL,
                                  `message` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                                  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 11 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = COMPACT;

-- ----------------------------
-- Table structure for task_host_scan
-- ----------------------------
DROP TABLE IF EXISTS `task_host_scan`;
CREATE TABLE `task_host_scan`  (
                                   `id` int(11) NOT NULL AUTO_INCREMENT,
                                   `app_id` int(11) NULL DEFAULT NULL,
                                   `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                                   `status` tinyint(4) NOT NULL DEFAULT 0,
                                   `create_time` datetime NULL DEFAULT CURRENT_TIMESTAMP,
                                   `update_time` datetime NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
                                   PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = COMPACT;

-- ----------------------------
-- Table structure for task_url_crawler
-- ----------------------------
DROP TABLE IF EXISTS `task_url_crawler`;
CREATE TABLE `task_url_crawler`  (
                                     `id` int(11) NOT NULL AUTO_INCREMENT,
                                     `app_id` int(11) NULL DEFAULT NULL,
                                     `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                                     `status` tinyint(4) NOT NULL DEFAULT 0,
                                     `create_time` datetime NULL DEFAULT CURRENT_TIMESTAMP,
                                     `update_time` datetime NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
                                     PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = COMPACT;

-- ----------------------------
-- Table structure for text
-- ----------------------------
DROP TABLE IF EXISTS `text`;
CREATE TABLE `text`  (
                         `hash` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
                         `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
                         `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                         PRIMARY KEY (`hash`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = COMPACT;

-- ----------------------------
-- Table structure for urls_sqlmap
-- ----------------------------
DROP TABLE IF EXISTS `urls_sqlmap`;
CREATE TABLE `urls_sqlmap`  (
                                `id` int(10) NOT NULL AUTO_INCREMENT,
                                `urls_id` int(10) NOT NULL,
                                `create_time` datetime NULL DEFAULT CURRENT_TIMESTAMP,
                                `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL COMMENT '注入类型',
                                `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                                `payload` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
                                `app_id` int(10) NOT NULL DEFAULT 0,
                                `system` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                                `application` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                                `dbms` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                                `user_id` int(10) NOT NULL DEFAULT 0,
                                PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = COMPACT;

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user`  (
                         `id` int(10) NOT NULL AUTO_INCREMENT,
                         `username` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '帐号',
                         `password` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '密码',
                         `salt` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '密码盐值',
                         `nickname` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '昵称',
                         `auth_group_id` int(10) NOT NULL DEFAULT 0 COMMENT '用户组id',
                         `created_at` int(10) NOT NULL DEFAULT 0 COMMENT '添加时间',
                         `last_login_ip` char(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '最后登录ip地址',
                         `last_login_time` int(10) NOT NULL DEFAULT 0 COMMENT '最后登陆时间',
                         `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '状态 1正常  2禁用',
                         `update_time` int(10) NOT NULL DEFAULT 0 COMMENT '修改时间',
                         `is_delete` tinyint(1) NOT NULL DEFAULT 0,
                         `sex` tinyint(1) NOT NULL DEFAULT 0 COMMENT '性别',
                         `phone` char(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '手机号码',
                         `dd_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '钉钉token',
                         `email` char(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '邮箱',
                         `token` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
                         `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '主页url',
                         PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = COMPACT;

-- ----------------------------
-- Table structure for user_log
-- ----------------------------
DROP TABLE IF EXISTS `user_log`;
CREATE TABLE `user_log`  (
                             `id` int(10) NOT NULL AUTO_INCREMENT,
                             `username` char(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
                             `create_time` datetime NOT NULL,
                             `content` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '详情',
                             `type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '操作类型',
                             `ip` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
                             PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = COMPACT;

-- ----------------------------
-- Table structure for vul_target
-- ----------------------------
DROP TABLE IF EXISTS `vul_target`;
CREATE TABLE `vul_target`  (
                               `id` int(11) NOT NULL AUTO_INCREMENT,
                               `addr` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                               `ip` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                               `port` int(10) NULL DEFAULT NULL,
                               `query` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                               `create_time` datetime NULL DEFAULT CURRENT_TIMESTAMP,
                               `is_vul` int(11) NOT NULL DEFAULT 0 COMMENT '是否存在漏洞 0 位置 1 存在 2 不存在',
                               `vul_id` int(11) NULL DEFAULT NULL COMMENT '缺陷ID',
                               `user_id` int(10) NOT NULL DEFAULT 0,
                               PRIMARY KEY (`id`) USING BTREE,
                               UNIQUE INDEX `un_addr`(`ip`, `port`, `vul_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = COMPACT;

-- ----------------------------
-- Table structure for vulnerable
-- ----------------------------
DROP TABLE IF EXISTS `vulnerable`;
CREATE TABLE `vulnerable`  (
                               `id` int(11) NOT NULL AUTO_INCREMENT,
                               `nature` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                               `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                               `vul_num` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                               `cve_num` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                               `cnvd_num` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                               `cnnvd_num` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                               `src_num` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                               `vul_level` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                               `vul_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                               `cwe` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                               `vul_cvss` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                               `cvss_vector` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                               `open_time` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                               `vul_repair_time` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                               `vul_source` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                               `temp_plan` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                               `temp_plan_s3` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                               `formal_plan` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                               `patch_s3` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                               `patch_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                               `patch_use_func` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                               `cpe` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                               `product_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                               `product_open` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                               `product_store` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                               `store_website` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                               `assem_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                               `affect_ver` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                               `ver_open_date` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                               `sub_update_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                               `git_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                               `git_commit_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                               `git_fixed_commit_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                               `product_cate` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                               `product_field` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                               `product_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                               `fofa_max` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                               `fofa_con` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                               `created_at` datetime NULL DEFAULT NULL,
                               `updated_at` datetime NULL DEFAULT NULL,
                               `user_id` int(10) NULL DEFAULT NULL,
                               `is_pass` int(10) NULL DEFAULT NULL,
                               `user_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                               `is_sub_attack` int(10) NULL DEFAULT NULL,
                               `temp_plan_s3_hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                               `patch_s3_hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                               `is_pass_attack` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                               `auditor` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                               `cause` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                               `scan_time` datetime NULL DEFAULT '2000-01-01 00:00:00',
                               `is_poc` int(10) NULL DEFAULT 0 COMMENT '是否有POC',
                               `is_delete` tinyint(1) NOT NULL DEFAULT 0,
                               `target_scan_time` datetime NULL DEFAULT NULL,
                               PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = COMPACT;

-- ----------------------------
-- Table structure for xray
-- ----------------------------
DROP TABLE IF EXISTS `xray`;
CREATE TABLE `xray`  (
                         `id` int(11) NOT NULL AUTO_INCREMENT,
                         `app_id` int(11) NULL DEFAULT NULL,
                         `create_time` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                         `detail` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
                         `plugin` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                         `target` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                         `check_status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '审核状态',
                         `hazard_level` tinyint(1) NOT NULL DEFAULT 0 COMMENT '危险等级',
                         `url_source` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT 'url来源',
                         `is_delete` tinyint(1) NOT NULL DEFAULT 0,
                         `user_id` int(10) NOT NULL DEFAULT 0,
                         PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = COMPACT;

-- ----------------------------
-- Table structure for zhiwen
-- ----------------------------
DROP TABLE IF EXISTS `zhiwen`;
CREATE TABLE `zhiwen`  (
                           `id` int(10) NOT NULL AUTO_INCREMENT,
                           `add_by` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                           `add_time` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                           `filters` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                           `keyword` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                           `md5` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                           `supplier` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                           `tags` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                           `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                           PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = COMPACT;

SET FOREIGN_KEY_CHECKS = 1;

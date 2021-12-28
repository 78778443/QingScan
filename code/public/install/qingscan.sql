/*
 Navicat Premium Data Transfer

 Target Server Type    : MySQL
 Target Server Version : 50651
 File Encoding         : 65001

 Date: 17/12/2021 11:09:38
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
                        `create_time` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0),
                        `crawler_time` datetime(0) NULL DEFAULT '2000-01-01 00:00:00',
                        `awvs_scan_time` datetime(0) NULL DEFAULT '2000-01-01 00:00:00',
                        `subdomain_time` datetime(0) NULL DEFAULT '2000-01-01 00:00:00',
                        `is_delete` tinyint(1) NOT NULL DEFAULT 0,
                        `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
                        `password` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
                        `whatweb_scan_time` datetime(0) NULL DEFAULT '2000-01-01 00:00:00',
                        `subdomain_scan_time` datetime(0) NULL DEFAULT '2000-01-01 00:00:00' COMMENT 'OneForAll子域名扫描时间',
                        `screenshot_time` datetime(0) NULL DEFAULT '2000-01-01 00:00:00' COMMENT '截图时间',
                        `xray_scan_time` datetime(0) NOT NULL DEFAULT '2000-01-01 00:00:00',
                        `dirmap_scan_time` datetime(0) NULL DEFAULT '2000-01-01 00:00:00',
                        `user_id` int(10) NOT NULL DEFAULT 0,
                        `wafw00f_scan_time` datetime(0) NOT NULL DEFAULT '2000-01-01 00:00:00',
                        `jietu_path` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                        `is_intranet` tinyint(1) NOT NULL DEFAULT 0,
                        `nuclei_scan_time` datetime(0) NULL DEFAULT '2000-01-01 00:00:00',
                        `dismap_scan_time` datetime(0) NULL DEFAULT '2000-01-01 00:00:00',
                        `crawlergo_scan_time` datetime(0) NULL DEFAULT '2000-01-01 00:00:00',
                        `vulmap_scan_time` datetime(0) NULL DEFAULT '2000-01-01 00:00:00',
                        PRIMARY KEY (`id`) USING BTREE,
                        UNIQUE INDEX `un_url`(`url`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2084 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = Compact;

-- ----------------------------
-- Records of app
-- ----------------------------

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
                                  `create_time` datetime(0) NOT NULL DEFAULT '2000-01-01 00:00:00',
                                  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1372 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin COMMENT = 'url收集' ROW_FORMAT = Compact;


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
                               `create_time` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
                               `user_id` int(10) NOT NULL DEFAULT 0,
                               PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = Compact;

-- ----------------------------
-- Records of app_dirmap
-- ----------------------------

-- ----------------------------
-- Table structure for app_dismap
-- ----------------------------
DROP TABLE IF EXISTS `app_dismap`;
CREATE TABLE `app_dismap`  (
                               `id` int(10) NOT NULL AUTO_INCREMENT,
                               `app_id` int(10) NOT NULL DEFAULT 0,
                               `user_id` int(10) NOT NULL DEFAULT 0,
                               `result` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL COMMENT '结果',
                               `create_time` datetime(0) NULL DEFAULT '2000-01-01 00:00:00',
                               PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = Compact;

-- ----------------------------
-- Records of app_dismap
-- ----------------------------

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
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = Compact;

-- ----------------------------
-- Records of app_info
-- ----------------------------

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
                               `create_time` datetime(0) NULL DEFAULT NULL,
                               `curl_command` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                               `status` tinyint(1) NOT NULL DEFAULT 0,
                               PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 106 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = Compact;


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
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin COMMENT = '漏洞扫描' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of app_vulmap
-- ----------------------------

-- ----------------------------
-- Table structure for app_wafw00f
-- ----------------------------
DROP TABLE IF EXISTS `app_wafw00f`;
CREATE TABLE `app_wafw00f`  (
                                `id` int(10) NOT NULL AUTO_INCREMENT,
                                `app_id` int(10) NOT NULL,
                                `user_id` int(10) NOT NULL DEFAULT 0,
                                `create_time` datetime(0) NULL DEFAULT NULL,
                                `url` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
                                `detected` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
                                `firewall` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'waf防火墙名称',
                                `manufacturer` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '厂商',
                                PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin COMMENT = 'waf指纹识别' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of app_wafw00f
-- ----------------------------

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
                                `create_time` datetime(0) NULL DEFAULT '2000-01-01 00:00:00',
                                `poc_scan_time` datetime(0) NOT NULL DEFAULT '2000-01-01 00:00:00',
                                `user_id` int(10) NOT NULL DEFAULT 0,
                                PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin COMMENT = 'web指纹识别' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of app_whatweb
-- ----------------------------

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
                                    `create_time` datetime(0) NOT NULL DEFAULT '2000-01-01 00:00:00',
                                    `user_id` int(10) NOT NULL,
                                    PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = Compact;

-- ----------------------------
-- Records of app_whatweb_poc
-- ----------------------------

-- ----------------------------
-- Table structure for app_xray_agent_port
-- ----------------------------
DROP TABLE IF EXISTS `app_xray_agent_port`;
CREATE TABLE `app_xray_agent_port`  (
                                        `id` int(10) NOT NULL AUTO_INCREMENT,
                                        `app_id` int(10) NOT NULL,
                                        `xray_agent_prot` int(10) NOT NULL COMMENT '代理端口',
                                        `create_time` datetime(0) NULL DEFAULT '2000-01-01 00:00:00',
                                        `start_up` int(1) NOT NULL DEFAULT 0 COMMENT '是否已启动',
                                        `is_get_result` int(1) NOT NULL DEFAULT 0 COMMENT '是否已获取结果',
                                        PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin COMMENT = '本地代理' ROW_FORMAT = Compact;


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
) ENGINE = MyISAM AUTO_INCREMENT = 10 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of auth_group
-- ----------------------------
INSERT INTO `auth_group` VALUES (5, '管理员', 1, '0,7,8,9,10,11,12,13,14,15,16,17,18,22,19,5,6,20,21,1,2,3,4,23,24,', 1635927813, 0, 1635754703);
INSERT INTO `auth_group` VALUES (6, '测试', 1, NULL, 1635755403, 1, 1635754726);
INSERT INTO `auth_group` VALUES (7, '白盒审计', 1, '0,7,43,48,45,14,15,16,17,18,22,19,', 1635927890, 0, 1635754803);
INSERT INTO `auth_group` VALUES (8, '黑盒测试', 1, '0,8,9,10,11,12,13,', 1635927865, 0, 1635757244);
INSERT INTO `auth_group` VALUES (9, '缺陷利用', 1, '0,5,6,20,21,', 1635927907, 0, 1635927907);

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
) ENGINE = MyISAM AUTO_INCREMENT = 88 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '权限列表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of auth_rule
-- ----------------------------
INSERT INTO `auth_rule` VALUES (1, '', '权限管理', 0, 1, 0, 6, 1635761804, 1, 1635847067, 1, 0, '');
INSERT INTO `auth_rule` VALUES (2, 'auth/user_list', '用户列表', 0, 1, 1, 1, 1635770190, 1, 0, 2, 0, '');
INSERT INTO `auth_rule` VALUES (3, 'auth/auth_group_list', '角色管理', 0, 1, 1, 2, 1635770263, 1, 1635836209, 2, 0, '');
INSERT INTO `auth_rule` VALUES (4, 'auth/auth_rule', '菜单管理', 0, 1, 1, 2, 1635770293, 1, 1635772788, 2, 0, '');
INSERT INTO `auth_rule` VALUES (5, '', '缺陷利用', 0, 1, 0, 5, 1635770370, 1, 1635847078, 1, 0, '');
INSERT INTO `auth_rule` VALUES (6, 'vulnerable/index', '缺陷列表', 0, 1, 5, 1, 1635770404, 1, 0, 2, 0, '');
INSERT INTO `auth_rule` VALUES (7, 'index/index', '主页', 0, 1, 0, 1, 1635846922, 1, 0, 1, 0, '');
INSERT INTO `auth_rule` VALUES (8, '', '黑盒扫描', 0, 1, 0, 3, 1635846950, 1, 1636889474, 1, 0, '');
INSERT INTO `auth_rule` VALUES (9, 'app/index', '目标管理', 0, 1, 8, 1, 1635846977, 1, 1635846997, 2, 0, '');
INSERT INTO `auth_rule` VALUES (10, 'urls/index', 'URL列表', 0, 1, 8, 2, 1635847015, 1, 1635847046, 2, 0, '');
INSERT INTO `auth_rule` VALUES (11, 'xray/index', 'Xray列表', 0, 1, 8, 3, 1635847110, 1, 1635847244, 2, 0, '');
INSERT INTO `auth_rule` VALUES (12, 'bug/awvs', 'AWVS列表', 0, 1, 8, 4, 1635847267, 1, 0, 2, 0, '');
INSERT INTO `auth_rule` VALUES (13, 'hostPort/index', 'Nmap列表', 0, 1, 35, 5, 1635847285, 1, 0, 2, 0, '');
INSERT INTO `auth_rule` VALUES (14, '', '白盒审计', 0, 1, 0, 4, 1635847305, 1, 1635847336, 1, 0, '');
INSERT INTO `auth_rule` VALUES (15, 'code/index', '项目列表', 0, 1, 14, 1, 1635847323, 1, 0, 2, 0, '');
INSERT INTO `auth_rule` VALUES (16, 'code/bug_list', 'Fortify', 0, 1, 14, 2, 1635847369, 1, 0, 2, 0, '');
INSERT INTO `auth_rule` VALUES (17, 'code/kunlun_list', 'KunLun-M', 0, 1, 14, 3, 1635847384, 1, 0, 2, 0, '');
INSERT INTO `auth_rule` VALUES (18, 'code/semgrep_list', 'SemGrep', 0, 1, 14, 4, 1635847399, 1, 0, 2, 0, '');
INSERT INTO `auth_rule` VALUES (19, 'code/hooks', '安全钩子', 0, 1, 14, 5, 1635847415, 0, 1636038515, 2, 0, '');
INSERT INTO `auth_rule` VALUES (20, 'vulnerable/pocsuite', 'POC列表', 1, 1, 5, 2, 1635847469, 1, 0, 2, 1639645594, '');
INSERT INTO `auth_rule` VALUES (21, 'vulnerable/pocsuite', '漏洞实例', 0, 1, 5, 3, 1635847483, 1, 1639645645, 2, 0, '');
INSERT INTO `auth_rule` VALUES (22, 'code/info', '代码指纹', 1, 1, 14, 5, 1635862942, 1, 0, 2, 1639645853, '');
INSERT INTO `auth_rule` VALUES (23, '', '系统设置', 0, 1, 0, 7, 1635923641, 1, 0, 1, 0, '');
INSERT INTO `auth_rule` VALUES (24, 'config/index', '配置管理', 0, 1, 23, 1, 1635924094, 1, 1635924116, 2, 0, '');
INSERT INTO `auth_rule` VALUES (25, '', 'HIDS', 0, 0, 0, 8, 1636025698, 0, 1638105317, 1, 0, '');
INSERT INTO `auth_rule` VALUES (26, 'elkeid/index', '节点列表', 0, 1, 25, 1, 1636025719, 1, 1638105291, 2, 0, '');
INSERT INTO `auth_rule` VALUES (27, 'one_for_all/index', 'OneForAll列表', 0, 1, 35, 4, 1636889435, 1, 1636889488, 2, 0, '');
INSERT INTO `auth_rule` VALUES (28, 'hydra/index', 'hydra列表', 0, 1, 35, 4, 1636890050, 1, 1636890149, 2, 0, '');
INSERT INTO `auth_rule` VALUES (29, 'dirmap/index', 'DirMap列表', 0, 1, 35, 4, 1636891951, 1, 1636892246, 2, 0, '');
INSERT INTO `auth_rule` VALUES (30, 'github_notice/index', 'github公告列表', 0, 1, 35, 4, 1636892471, 1, 1636892489, 2, 0, '');
INSERT INTO `auth_rule` VALUES (31, 'whatweb/index', 'whatWeb列表', 0, 1, 35, 4, 1636893189, 1, 1636893227, 2, 0, '');
INSERT INTO `auth_rule` VALUES (32, 'sqlmap/index', 'SqlMap列表', 0, 1, 8, 4, 1636951427, 1, 0, 2, 0, '');
INSERT INTO `auth_rule` VALUES (33, 'process_safe/index', '守护进程管理', 0, 1, 23, 2, 1637043150, 1, 0, 2, 0, '');
INSERT INTO `auth_rule` VALUES (34, 'pocs_file/index', 'POC脚本', 0, 1, 5, 2, 1637049514, 1, 1639645626, 2, 0, '');
INSERT INTO `auth_rule` VALUES (35, '', '信息收集', 0, 1, 0, 2, 0, 1, 0, 1, 0, '');
INSERT INTO `auth_rule` VALUES (36, 'host/index', '主机列表', 0, 1, 35, 6, 0, 1, 0, 2, 0, '');
INSERT INTO `auth_rule` VALUES (37, 'log/index', '日志管理', 0, 1, 23, 3, 1637155116, 1, 0, 2, 0, '');
INSERT INTO `auth_rule` VALUES (38, 'code_python/index', 'python依赖库', 0, 1, 14, 5, 1637244411, 1, 1639645841, 2, 0, '');
INSERT INTO `auth_rule` VALUES (39, 'code_composer/index', 'composer列表', 0, 1, 14, 6, 1637302940, 1, 1637303558, 2, 0, '');
INSERT INTO `auth_rule` VALUES (40, 'code_java/index', 'java依赖库', 0, 1, 14, 7, 1637308555, 1, 0, 2, 0, '');
INSERT INTO `auth_rule` VALUES (41, 'proxy/index', '代理列表', 0, 1, 23, 4, 1637911963, 1, 0, 2, 0, '');
INSERT INTO `auth_rule` VALUES (42, 'node/index', '节点列表', 0, 1, 23, 5, 1638105532, 1, 1638105795, 2, 0, '');
INSERT INTO `auth_rule` VALUES (43, '', '未知路径', 0, 0, 7, 0, 1638342233, 0, 1639295338, 2, 0, '');
INSERT INTO `auth_rule` VALUES (44, 'proxy/edit', '未知', 0, 1, 43, 0, 1638343418, 1, 0, 3, 0, '');
INSERT INTO `auth_rule` VALUES (45, 'code/edit_modal', '未知', 0, 1, 43, 0, 1638343510, 1, 0, 3, 0, '');
INSERT INTO `auth_rule` VALUES (46, 'auth/auth_group_access', '未知', 0, 1, 43, 0, 1638343754, 1, 0, 3, 0, '');
INSERT INTO `auth_rule` VALUES (47, 'auth/auth_group_setaccess', '未知', 0, 1, 43, 0, 1638344253, 1, 0, 3, 0, '');
INSERT INTO `auth_rule` VALUES (48, 'code/code_del', '未知', 0, 1, 43, 0, 1638344366, 1, 0, 3, 0, '');
INSERT INTO `auth_rule` VALUES (49, 'auth/rule_add', '未知', 0, 1, 43, 0, 1638362288, 1, 0, 3, 0, '');
INSERT INTO `auth_rule` VALUES (50, 'github_keyword_monitor/index', 'github关键词', 0, 1, 35, 8, 1638362324, 1, 0, 2, 0, '');
INSERT INTO `auth_rule` VALUES (51, 'github_keyword_monitor/add', '未知', 0, 1, 43, 0, 1638368417, 1, 0, 3, 0, '');
INSERT INTO `auth_rule` VALUES (52, 'github_keyword_monitor/edit', '未知', 0, 1, 43, 0, 1638368619, 1, 0, 3, 0, '');
INSERT INTO `auth_rule` VALUES (53, 'github_keyword_monitor/del', '未知', 0, 1, 43, 0, 1638368666, 1, 0, 3, 0, '');
INSERT INTO `auth_rule` VALUES (54, 'auth/user_info', '未知', 0, 1, 43, 0, 1638380427, 1, 0, 3, 0, '');
INSERT INTO `auth_rule` VALUES (55, 'auth/user_password', '未知', 0, 1, 43, 0, 1638380437, 1, 0, 3, 0, '');
INSERT INTO `auth_rule` VALUES (56, 'to_examine/auth_rule_status', '未知', 0, 1, 43, 0, 1638429476, 1, 0, 3, 0, '');
INSERT INTO `auth_rule` VALUES (58, 'app/icon', '未知', 0, 1, 43, 0, 1638431873, 1, 0, 3, 0, '');
INSERT INTO `auth_rule` VALUES (57, 'to_examine/auth_rule_auth', '未知', 0, 1, 43, 0, 1638429711, 1, 0, 3, 0, '');
INSERT INTO `auth_rule` VALUES (59, 'auth/rule_edit', '未知', 0, 1, 43, 0, 1638459598, 1, 0, 3, 0, '');
INSERT INTO `auth_rule` VALUES (60, 'pocs_file/edit', '未知', 0, 1, 43, 0, 1638510461, 1, 0, 3, 0, '');
INSERT INTO `auth_rule` VALUES (61, 'app/details', '未知', 0, 1, 43, 0, 1639295322, 1, 0, 3, 0, '');
INSERT INTO `auth_rule` VALUES (62, 'host_port/index', '未知', 0, 1, 43, 0, 1639295357, 1, 0, 3, 0, '');
INSERT INTO `auth_rule` VALUES (63, 'app/_add', '未知', 0, 1, 43, 0, 1639296987, 1, 0, 3, 0, '');
INSERT INTO `auth_rule` VALUES (64, 'to_examine/process_safe', '未知', 0, 1, 43, 0, 1639297003, 1, 0, 3, 0, '');
INSERT INTO `auth_rule` VALUES (65, 'vulnerable/details', '未知', 0, 1, 43, 0, 1639319159, 1, 0, 3, 0, '');
INSERT INTO `auth_rule` VALUES (66, 'xray/details', '未知', 0, 1, 43, 0, 1639319205, 1, 0, 3, 0, '');
INSERT INTO `auth_rule` VALUES (67, 'app_nuclei/index', 'nuclei列表', 0, 1, 35, 10, 1639398366, 1, 1639398437, 2, 0, '');
INSERT INTO `auth_rule` VALUES (68, 'process_safe/add', '未知', 0, 1, 43, 0, 1639400135, 1, 0, 3, 0, '');
INSERT INTO `auth_rule` VALUES (69, 'plugin/index', '自定义插件', 0, 1, 8, 7, 1639473641, 1, 0, 2, 0, '');
INSERT INTO `auth_rule` VALUES (70, 'plugin/add', '未知', 0, 1, 43, 0, 1639476048, 1, 0, 3, 0, '');
INSERT INTO `auth_rule` VALUES (71, 'plugin/edit', '未知', 0, 1, 43, 0, 1639486178, 1, 0, 3, 0, '');
INSERT INTO `auth_rule` VALUES (72, 'plugin/del', '未知', 0, 1, 43, 0, 1639486402, 1, 0, 3, 0, '');
INSERT INTO `auth_rule` VALUES (73, 'plugin_result/index', '自定义插件结果', 0, 1, 8, 8, 1639486937, 1, 1639486965, 2, 0, '');
INSERT INTO `auth_rule` VALUES (74, 'to_examine/plugin_result', '未知', 0, 1, 43, 0, 1639487953, 1, 0, 3, 0, '');
INSERT INTO `auth_rule` VALUES (75, 'app/qingkong', '未知', 0, 1, 43, 0, 1639562898, 1, 0, 3, 0, '');
INSERT INTO `auth_rule` VALUES (76, 'plugin_result/details', '未知', 0, 1, 43, 0, 1639569692, 1, 0, 3, 0, '');
INSERT INTO `auth_rule` VALUES (77, 'process_safe/edit', '未知', 0, 1, 43, 0, 1639569752, 1, 0, 3, 0, '');
INSERT INTO `auth_rule` VALUES (78, 'app_crawlergo/index', 'crawlergo列表', 0, 1, 35, 10, 1639575650, 1, 1639575860, 2, 0, '');
INSERT INTO `auth_rule` VALUES (79, 'config/add', '未知', 0, 1, 43, 0, 1639641723, 1, 0, 3, 0, '');
INSERT INTO `auth_rule` VALUES (80, 'auth/rule_del', '未知', 0, 1, 43, 0, 1639645594, 1, 0, 3, 0, '');
INSERT INTO `auth_rule` VALUES (81, 'process_safe/show_process', '未知', 0, 1, 43, 0, 1639646166, 1, 0, 3, 0, '');
INSERT INTO `auth_rule` VALUES (82, 'code/info', '未知', 0, 1, 43, 0, 1639647121, 1, 0, 3, 0, '');
INSERT INTO `auth_rule` VALUES (83, 'process_safe/kill', '未知', 0, 1, 43, 0, 1639648669, 1, 0, 3, 0, '');
INSERT INTO `auth_rule` VALUES (84, 'pocs_file/add', '未知', 0, 1, 43, 0, 1639648978, 1, 0, 3, 0, '');
INSERT INTO `auth_rule` VALUES (85, 'app/start_agent', '未知', 0, 1, 43, 0, 1639649367, 1, 0, 3, 0, '');
INSERT INTO `auth_rule` VALUES (86, 'pocs_file/details', '未知', 0, 1, 43, 0, 1639649632, 1, 0, 3, 0, '');
INSERT INTO `auth_rule` VALUES (87, 'vulnerable/edit', '未知', 0, 1, 43, 0, 1639652554, 1, 0, 3, 0, '');
INSERT INTO `auth_rule` VALUES (88, 'vulmap/index', 'vulmap扫描', 0, 1, 8, 0, 1639913634, 1, 1639913692, 2, 0, '');
INSERT INTO `auth_rule` VALUES (90, 'auth/user_del', '未知', 0, 1, 43, 0, 1640525743, 1, 0, 3, 0, '');
INSERT INTO `auth_rule` VALUES (91, 'auth/auth_group_edit', '未知', 0, 1, 43, 0, 1640526087, 1, 0, 3, 0, '');
INSERT INTO `auth_rule` VALUES (92, 'app/del', '未知', 0, 1, 43, 0, 1640526227, 1, 0, 3, 0, '');
INSERT INTO `auth_rule` VALUES (93, 'dirmap/details', '未知', 0, 1, 43, 0, 1640532135, 1, 0, 3, 0, '');
INSERT INTO `auth_rule` VALUES (94, 'to_examine/kunlun', '未知', 0, 1, 43, 0, 1640572467, 1, 0, 3, 0, '');
INSERT INTO `auth_rule` VALUES (95, 'vul_target/index', '缺陷目标收集', 0, 1, 5, 2, 1640594192, 1, 1640594387, 2, 0, '');

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
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = Compact;

-- ----------------------------
-- Records of awvs_app
-- ----------------------------

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
                              `create_time` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0),
                              `check_status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '审核状态',
                              `is_delete` tinyint(1) NOT NULL DEFAULT 0,
                              `user_id` int(10) NOT NULL DEFAULT 0,
                              PRIMARY KEY (`id`) USING BTREE,
                              UNIQUE INDEX `un_vuln_id`(`vuln_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = Compact;

-- ----------------------------
-- Records of awvs_vuln
-- ----------------------------

-- ----------------------------
-- Table structure for bug
-- ----------------------------
DROP TABLE IF EXISTS `bug`;
CREATE TABLE `bug`  (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `status` tinyint(4) NULL DEFAULT 1,
                        `name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                        `create_time` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0),
                        `detail` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL COMMENT '漏洞详情',
                        `category` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                        `host` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                        `check_status` tinyint(1) NOT NULL DEFAULT 0,
                        PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = Compact;

-- ----------------------------
-- Records of bug
-- ----------------------------

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
                         `scan_time` datetime(0) NOT NULL DEFAULT '2000-01-01 00:00:00',
                         `sonar_scan_time` datetime(0) NOT NULL DEFAULT '2000-01-01 00:00:00',
                         `kunlun_scan_time` datetime(0) NOT NULL DEFAULT '2000-01-01 00:00:00',
                         `semgrep_scan_time` datetime(0) NOT NULL DEFAULT '2000-01-01 00:00:00',
                         `pulling_mode` char(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '拉取方式（支持SSH、HTTPS）',
                         `is_private` tinyint(1) NOT NULL DEFAULT 0,
                         `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '用户名',
                         `password` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '密码',
                         `private_key` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
                         `is_delete` tinyint(1) NOT NULL DEFAULT 0,
                         `composer_scan_time` datetime(0) NOT NULL DEFAULT '2000-01-01 00:00:00',
                         `java_scan_time` datetime(0) NOT NULL DEFAULT '2000-01-01 00:00:00',
                         `python_scan_time` datetime(0) NOT NULL DEFAULT '2000-01-01 00:00:00',
                         `user_id` int(10) NOT NULL DEFAULT 0,
                         `star` char(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
                         `webshell_scan_time` datetime(0) NOT NULL DEFAULT '2000-01-01 00:00:00',
                         PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = Compact;

-- ----------------------------
-- Records of code
-- ----------------------------

-- ----------------------------
-- Table structure for code_check
-- ----------------------------
DROP TABLE IF EXISTS `code_check`;
CREATE TABLE `code_check`  (
                               `id` int(11) NOT NULL AUTO_INCREMENT,
                               `app_id` int(11) NULL DEFAULT 0,
                               `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
                               `status` tinyint(4) NULL DEFAULT 1,
                               `create_time` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
                               `files` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
                               `author` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                               `project_hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                               `version` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                               `project_id` int(11) NULL DEFAULT NULL,
                               PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = Compact;

-- ----------------------------
-- Records of code_check
-- ----------------------------

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
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of code_check_notice
-- ----------------------------

-- ----------------------------
-- Table structure for code_composer
-- ----------------------------
DROP TABLE IF EXISTS `code_composer`;
CREATE TABLE `code_composer`  (
                                  `id` int(10) NOT NULL AUTO_INCREMENT,
                                  `code_id` int(10) NOT NULL,
                                  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
                                  `version` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
                                  `source` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
                                  `dist` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
                                  `require` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
                                  `require_dev` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
                                  `type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
                                  `autoload` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
                                  `notification_url` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
                                  `license` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
                                  `authors` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
                                  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
                                  `homepage` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
                                  `keywords` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
                                  `time` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                                  `create_time` datetime(0) NULL DEFAULT NULL,
                                  `user_id` int(10) NOT NULL DEFAULT 0,
                                  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = Compact;

-- ----------------------------
-- Records of code_composer
-- ----------------------------

-- ----------------------------
-- Table structure for code_hook
-- ----------------------------
DROP TABLE IF EXISTS `code_hook`;
CREATE TABLE `code_hook`  (
                              `id` int(11) NOT NULL AUTO_INCREMENT,
                              `create_time` datetime(0) NULL DEFAULT NULL,
                              `update_time` datetime(0) NULL DEFAULT NULL,
                              `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                              `path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                              `gitlab_id` int(11) NULL DEFAULT NULL,
                              `project` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                              PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of code_hook
-- ----------------------------

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
                              `create_time` datetime(0) NULL DEFAULT NULL,
                              `user_id` int(10) NOT NULL DEFAULT 0,
                              PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = Compact;

-- ----------------------------
-- Records of code_java
-- ----------------------------

-- ----------------------------
-- Table structure for code_python
-- ----------------------------
DROP TABLE IF EXISTS `code_python`;
CREATE TABLE `code_python`  (
                                `id` int(10) NOT NULL AUTO_INCREMENT,
                                `code_id` int(10) NOT NULL,
                                `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
                                `create_time` datetime(0) NOT NULL DEFAULT '2000-01-01 00:00:00',
                                `user_id` int(10) NOT NULL DEFAULT 0,
                                PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = Compact;

-- ----------------------------
-- Records of code_python
-- ----------------------------

-- ----------------------------
-- Table structure for code_webshell
-- ----------------------------
DROP TABLE IF EXISTS `code_webshell`;
CREATE TABLE `code_webshell`  (
                                  `id` int(10) NOT NULL AUTO_INCREMENT,
                                  `code_id` int(10) NOT NULL,
                                  `create_time` datetime(0) NOT NULL DEFAULT '2000-01-01 00:00:00',
                                  `check_status` int(1) NOT NULL DEFAULT 0 COMMENT '审核状态',
                                  `user_id` int(10) NOT NULL DEFAULT 0,
                                  `type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '类型',
                                  `filename` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '文件路径',
                                  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = Compact;

-- ----------------------------
-- Records of code_webshell
-- ----------------------------

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
                           `create_time` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0),
                           PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = Compact;

-- ----------------------------
-- Records of config
-- ----------------------------

-- ----------------------------
-- Table structure for fortify
-- ----------------------------
DROP TABLE IF EXISTS `fortify`;
CREATE TABLE `fortify`  (
                            `id` int(11) NOT NULL AUTO_INCREMENT,
                            `create_time` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
                            `Category` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                            `Folder` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                            `Kingdom` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                            `Abstract` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
                            `Friority` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                            `Primary` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
                            `Source` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
                            `project_id` int(11) NULL DEFAULT NULL,
                            `comment` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                            `check_status` int(5) NULL DEFAULT 0 COMMENT '0 未处理   1 有效漏洞  2 无效漏洞',
                            `Source_filename` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                            `Primary_filename` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                            `hash` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                            `is_delete` tinyint(1) NOT NULL DEFAULT 0,
                            `user_id` int(10) NOT NULL DEFAULT 0,
                            PRIMARY KEY (`id`) USING BTREE,
                            UNIQUE INDEX `un_hash`(`hash`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = Compact;

-- ----------------------------
-- Records of fortify
-- ----------------------------

-- ----------------------------
-- Table structure for github_keyword_monitor
-- ----------------------------
DROP TABLE IF EXISTS `github_keyword_monitor`;
CREATE TABLE `github_keyword_monitor`  (
                                           `id` int(10) NOT NULL AUTO_INCREMENT,
                                           `user_id` int(10) NOT NULL DEFAULT 0,
                                           `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '关键字',
                                           `create_time` datetime(0) NULL DEFAULT '2000-01-01 00:00:00',
                                           `update_time` datetime(0) NULL DEFAULT '2000-01-01 00:00:00',
                                           `scan_time` datetime(0) NOT NULL DEFAULT '2000-01-01 00:00:00',
                                           PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin COMMENT = 'github关键字设置' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of github_keyword_monitor
-- ----------------------------

-- ----------------------------
-- Table structure for github_keyword_monitor_notice
-- ----------------------------
DROP TABLE IF EXISTS `github_keyword_monitor_notice`;
CREATE TABLE `github_keyword_monitor_notice`  (
                                                  `id` int(10) NOT NULL AUTO_INCREMENT,
                                                  `parent_id` int(10) NOT NULL DEFAULT 0 COMMENT '关键字表id',
                                                  `user_id` int(10) NOT NULL DEFAULT 0,
                                                  `keyword` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
                                                  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
                                                  `path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
                                                  `html_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
                                                  `create_time` datetime(0) NULL DEFAULT NULL,
                                                  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin COMMENT = 'github关键字监控结果' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of github_keyword_monitor_notice
-- ----------------------------

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
                                  `github_release_date` datetime(0) NULL DEFAULT NULL,
                                  `references` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL COMMENT '参考资料',
                                  `desc` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
                                  `package` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
                                  `hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                                  `create_time` datetime(0) NULL DEFAULT '2000-01-01 00:00:00',
                                  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = Compact;

-- ----------------------------
-- Records of github_notice
-- ----------------------------

-- ----------------------------
-- Table structure for group
-- ----------------------------
DROP TABLE IF EXISTS `group`;
CREATE TABLE `group`  (
                          `id` int(11) NOT NULL AUTO_INCREMENT,
                          PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = Compact;

-- ----------------------------
-- Records of group
-- ----------------------------

-- ----------------------------
-- Table structure for host
-- ----------------------------
DROP TABLE IF EXISTS `host`;
CREATE TABLE `host`  (
                         `id` int(11) NOT NULL AUTO_INCREMENT,
                         `app_id` int(11) NULL DEFAULT 0,
                         `domain` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                         `host` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
                         `status` tinyint(4) NOT NULL DEFAULT 1,
                         `create_time` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
                         `isp` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '运营商',
                         `country` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '国家',
                         `region` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '省份',
                         `city` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '市',
                         `area` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '地区',
                         `hydra_scan_time` datetime(0) NOT NULL DEFAULT '2000-01-01 00:00:00',
                         `port_scan_time` datetime(0) NOT NULL DEFAULT '2000-01-01 00:00:00',
                         `ip_scan_time` datetime(0) NOT NULL DEFAULT '2000-01-01 00:00:00',
                         `is_delete` int(4) NOT NULL DEFAULT 0,
                         `user_id` int(10) NOT NULL DEFAULT 0,
                         PRIMARY KEY (`id`) USING BTREE,
                         UNIQUE INDEX `un_host`(`domain`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = Compact;

-- ----------------------------
-- Records of host
-- ----------------------------

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
                                            `create_time` datetime(0) NULL DEFAULT NULL,
                                            `app_id` int(10) NOT NULL DEFAULT 0,
                                            `user_id` int(10) NOT NULL,
                                            PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = Compact;

-- ----------------------------
-- Records of host_hydra_scan_details
-- ----------------------------

-- ----------------------------
-- Table structure for host_port
-- ----------------------------
DROP TABLE IF EXISTS `host_port`;
CREATE TABLE `host_port`  (
                              `id` int(11) NOT NULL AUTO_INCREMENT,
                              `port` int(11) NOT NULL DEFAULT 0,
                              `host` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '0',
                              `type` char(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                              `service` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                              `is_close` tinyint(4) NULL DEFAULT 0 COMMENT '是否关闭',
                              `create_time` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
                              `update_time` datetime(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
                              `os` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL COMMENT '操作系统',
                              `html` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
                              `headers` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
                              `is_delete` tinyint(1) NOT NULL DEFAULT 0,
                              `scan_time` datetime(0) NOT NULL DEFAULT '2000-01-01 00:00:00',
                              `user_id` int(10) NOT NULL DEFAULT 0,
                              PRIMARY KEY (`id`) USING BTREE,
                              UNIQUE INDEX `un_port`(`host`, `port`, `type`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = Compact;

-- ----------------------------
-- Records of host_port
-- ----------------------------

-- ----------------------------
-- Table structure for log
-- ----------------------------
DROP TABLE IF EXISTS `log`;
CREATE TABLE `log`  (
                        `id` int(10) NOT NULL AUTO_INCREMENT,
                        `content` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
                        `app` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
                        `create_time` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
                        PRIMARY KEY (`id`) USING BTREE,
                        INDEX `time`(`create_time`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = Compact;

-- ----------------------------
-- Records of log
-- ----------------------------

-- ----------------------------
-- Table structure for node
-- ----------------------------
DROP TABLE IF EXISTS `node`;
CREATE TABLE `node`  (
                         `id` int(11) NOT NULL AUTO_INCREMENT,
                         `userid` int(10) NOT NULL DEFAULT 0,
                         `status` tinyint(1) NOT NULL DEFAULT 0,
                         `hostname` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
                         `create_time` datetime(0) NULL DEFAULT NULL,
                         PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = Compact;

-- ----------------------------
-- Records of node
-- ----------------------------

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
                                `create_time` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
                                `user_id` int(10) NOT NULL DEFAULT 0,
                                PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = Compact;

-- ----------------------------
-- Records of one_for_all
-- ----------------------------

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
                           `create_time` datetime(0) NULL DEFAULT '2000-01-01 00:00:00' COMMENT '添加时间',
                           `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0禁用  1启用',
                           `is_delete` tinyint(1) NOT NULL DEFAULT 0,
                           `result_type` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT '' COMMENT 'json、csv、txt',
                           `update_time` datetime(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
                           `tool_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL COMMENT '工具存放位置',
                           `scan_type` int(4) NULL DEFAULT 0 COMMENT '0 app 1 host 2 code  3 url',
                           PRIMARY KEY (`id`) USING BTREE,
                           UNIQUE INDEX `un_name`(`name`, `scan_type`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin COMMENT = '自定义插件' ROW_FORMAT = Compact;


-- ----------------------------
-- Table structure for plugin_result
-- ----------------------------
DROP TABLE IF EXISTS `plugin_result`;
CREATE TABLE `plugin_result`  (
                                  `id` int(10) NOT NULL AUTO_INCREMENT,
                                  `app_id` int(10) NOT NULL,
                                  `plugin_id` int(10) NOT NULL,
                                  `user_id` int(10) NOT NULL DEFAULT 0,
                                  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL COMMENT '扫描结果内容',
                                  `create_time` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0),
                                  `check_status` tinyint(1) NOT NULL COMMENT '审核状态',
                                  `plugin_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL COMMENT '插件名称',
                                  `scan_type` int(255) NULL DEFAULT NULL COMMENT '0 app 1 host 2 code  3 url',
                                  PRIMARY KEY (`id`) USING BTREE,
                                  UNIQUE INDEX `un_id`(`app_id`, `user_id`, `plugin_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 8 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin COMMENT = '自定义插件扫描结果' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of plugin_result
-- ----------------------------

-- ----------------------------
-- Table structure for pocs_file
-- ----------------------------
DROP TABLE IF EXISTS `pocs_file`;
CREATE TABLE `pocs_file`  (
                              `id` int(11) NOT NULL AUTO_INCREMENT,
                              `cve_num` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                              `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                              `create_time` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT '创建时间',
                              `status` tinyint(1) NOT NULL DEFAULT 1,
                              `tool` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 pocsuite3 1 xray 2 其他',
                              `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT 'POC内容',
                              `vul_id` int(11) NULL DEFAULT NULL,
                              PRIMARY KEY (`id`) USING BTREE,
                              UNIQUE INDEX `cve_poc`(`cve_num`, `name`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of pocs_file
-- ----------------------------

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
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = Compact;

-- ----------------------------
-- Records of pocsuite3
-- ----------------------------

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
                                 `update_time` datetime(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
                                 PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 42 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = Compact;

-- ----------------------------
-- Records of process_safe
-- ----------------------------
INSERT INTO `process_safe` VALUES (2, 'scan xray', 'cd /root/qingscan/code  &&  php think scan xray  >> /tmp/xray.txt & ', 0, 'xray扫描', '2021-12-24 17:09:07');
INSERT INTO `process_safe` VALUES (3, 'scan awvs', 'cd /root/qingscan/code  &&  php think scan awvs  >> /tmp/awvs.txt & ', 0, 'awvs扫描', '2021-12-17 13:25:20');
INSERT INTO `process_safe` VALUES (4, 'scan rad', 'cd /root/qingscan/code  &&  php think scan rad  >> /tmp/rad.txt & ', 0, 'rad爬虫', '2021-12-24 18:00:42');
INSERT INTO `process_safe` VALUES (5, 'scan host', 'cd /root/qingscan/code  &&  php think scan host  >> /tmp/host.txt & ', 0, '将黑盒目标添加到主机扫描', '2021-12-24 17:31:02');
INSERT INTO `process_safe` VALUES (6, 'scan port', 'cd /root/qingscan/code  &&  php think scan port  >> /tmp/port.txt & ', 0, '端口发现', '2021-12-24 17:31:45');
INSERT INTO `process_safe` VALUES (7, 'scan nmap', 'cd /root/qingscan/code  &&  php think scan nmap  >> /tmp/nmap.txt & ', 0, '端口服务识别', '2021-12-24 17:32:07');
INSERT INTO `process_safe` VALUES (8, 'scan fortify', 'cd /root/qingscan/code  &&  php think scan fortify  >> /tmp/fortify.txt & ', 0, 'fortify代码审计', '2021-12-17 13:25:58');
INSERT INTO `process_safe` VALUES (9, 'scan kunlun', 'cd /root/qingscan/code  &&  php think scan kunlun  >> /tmp/kunlun.txt & ', 0, 'kunlun代码审计', '2021-12-27 00:00:06');
INSERT INTO `process_safe` VALUES (10, 'scan semgrep', 'cd /root/qingscan/code  &&  php think scan semgrep  >> /tmp/semgrep.txt & ', 0, 'semgrep代码审计', '2021-12-26 22:11:10');
INSERT INTO `process_safe` VALUES (13, 'scan google', 'cd /root/qingscan/code  &&  php think scan google >> /tmp/google.txt & ', 0, '获取黑盒目标页面基本信息', '2021-12-24 17:42:16');
INSERT INTO `process_safe` VALUES (14, 'scan upadteRegion', 'cd /root/qingscan/code  &&  php think scan upadteRegion >> /tmp/upadteRegion.txt & ', 0, '更新IP的基本信息', '2021-12-24 17:43:05');
INSERT INTO `process_safe` VALUES (15, 'scan whatweb', 'cd /root/qingscan/code  &&  php think scan whatweb >> /tmp/whatweb.txt & ', 1, 'what指纹识别', '2021-12-27 21:49:06');
INSERT INTO `process_safe` VALUES (16, 'scan subdomainScan', 'cd /root/qingscan/code  &&  php think scan subdomainScan >> /tmp/subdomainScan.txt & ', 0, '使用fofa发现子域名', '2021-12-26 22:13:59');
INSERT INTO `process_safe` VALUES (17, 'scan hydra', 'cd /root/qingscan/code  &&  php think scan hydra >> /tmp/hydra.txt & ', 0, 'hydra主机爆破', '2021-12-17 13:27:40');
INSERT INTO `process_safe` VALUES (18, 'scan sqlmapScan', 'cd /root/qingscan/code  &&  php think scan sqlmapScan >> /tmp/sqlmapScan.txt & ', 0, 'sqlmap扫描URL', '2021-12-24 18:00:46');
INSERT INTO `process_safe` VALUES (20, 'scan fofa', 'cd /root/qingscan/code  &&  php think scan fofa >> /tmp/fofa.txt & ', 0, 'fofa收集缺陷站点', '2021-12-17 13:28:05');
INSERT INTO `process_safe` VALUES (21, 'scan dirmapScan', 'cd /root/qingscan/code  &&  php think scan dirmapScan >> /tmp/dirmapScan.txt & ', 0, '扫描黑盒目标后台', '2021-12-26 20:53:54');
INSERT INTO `process_safe` VALUES (22, 'scan getNotice', 'cd /root/qingscan/code  &&  php think scan getNotice >> /tmp/getNotice.txt & ', 0, '获取GitHub漏洞公告', '2021-12-17 13:28:21');
INSERT INTO `process_safe` VALUES (23, 'scan backup', 'cd /root/qingscan/code  &&  php think scan backup>> /tmp/backup.txt & ', 0, '数据库备份', '2021-12-26 20:45:07');
INSERT INTO `process_safe` VALUES (24, 'scan getProjectComposer', 'cd /root/qingscan/code  &&  php think scan getProjectComposer>> /tmp/composer.txt & ', 0, '获取composer组件', '2021-12-26 18:24:50');
INSERT INTO `process_safe` VALUES (25, 'scan code_python', 'cd /root/qingscan/code  &&  php think scan code_python>> /tmp/code_python.txt & ', 0, '获取python组件', '2021-12-26 18:24:49');
INSERT INTO `process_safe` VALUES (26, 'scan code_java', 'cd /root/qingscan/code  &&  php think scan code_java>> /tmp/code_java.txt & ', 0, '获取java组件', '2021-12-26 18:24:50');
INSERT INTO `process_safe` VALUES (27, 'scan giteeProject', 'cd /root/qingscan/code  &&  php think scan giteeProject>> /tmp/giteeProject.txt & ', 0, '获取码云项目', '2021-12-26 20:43:08');
INSERT INTO `process_safe` VALUES (28, 'scan freeAgent', 'cd /root/qingscan/code  &&  php think scan freeAgent>> /tmp/freeAgent.txt & ', 0, '获取免费代理', '2021-12-26 20:42:32');
INSERT INTO `process_safe` VALUES (29, 'scan github_keyword_monitor', 'cd /root/qingscan/think  &&  php think scan github_keyword_monitor>> /tmp/github_keyword_monitor.txt & ', 0, 'github关键字监控', NULL);
INSERT INTO `process_safe` VALUES (30, 'scan whatwebPocTest', 'cd /root/qingscan/think  &&  php think scan whatwebPocTest>> /tmp/whatwebPocTest.txt & ', 0, 'whatweb组件识别poc验证', NULL);
INSERT INTO `process_safe` VALUES (31, 'scan xrayAgentResult', 'cd /root/qingscan/think  &&  php think scan xrayAgentResult>> /tmp/xrayAgentResult.txt & ', 0, '获取xray代理模式结果数据', '2021-12-26 22:03:22');
INSERT INTO `process_safe` VALUES (32, 'scan startXrayAgent', 'cd /root/qingscan/think  &&  php think scan startXrayAgent>> /tmp/startXrayAgent.txt & ', 0, '启动xray代理模式', '2021-12-26 21:26:17');
INSERT INTO `process_safe` VALUES (33, 'scan code_webshell_scan', 'cd /root/qingscan/think  &&  php think scan code_webshell_scan>> /tmp/code_webshell_scan.txt & ', 0, '河马webshell检测', '2021-12-26 18:35:55');
INSERT INTO `process_safe` VALUES (35, 'scan wafw00fScan', 'cd /root/qingscan/think  &&  php think scan wafw00fScan>> /tmp/wafw00fScan.txt & ', 0, 'waf指纹识别', '2021-12-26 23:36:15');
INSERT INTO `process_safe` VALUES (37, 'scan nucleiScan', 'cd /root/qingscan/think  &&  php think scan nucleiScan>> /tmp/nucleiScan.txt & ', 0, 'nuclei扫描', '2021-12-27 21:50:54');
INSERT INTO `process_safe` VALUES (38, 'scan vulmapPocTest', 'cd /root/qingscan/think  &&  php think scan vulmapPocTest>> /tmp/vulmapPocTest.txt & ', 0, 'vulmap漏洞扫描POC测试', '2021-12-26 21:14:43');
INSERT INTO `process_safe` VALUES (39, 'scan dismapScan', 'cd /root/qingscan/think  &&  php think scan dismapScan>> /tmp/dismapScan.txt & ', 0, 'dismap指纹识别', '2021-12-26 23:44:40');
INSERT INTO `process_safe` VALUES (40, 'scan plugin_safe', 'cd /root/qingscan/think  &&  php think scan plugin_safe>> /tmp/plugin_safe.txt & ', 0, '自定义工具守护', '2021-12-27 21:48:54');
INSERT INTO `process_safe` VALUES (41, 'scan crawlergoScan', 'cd /root/qingscan/think  &&  php think scan crawlergoScan>> /tmp/crawlergoScan.txt & ', 0, 'crawlergo爬虫URL收集', '2021-12-26 20:25:04');

-- ----------------------------
-- Table structure for proxy
-- ----------------------------
DROP TABLE IF EXISTS `proxy`;
CREATE TABLE `proxy`  (
                          `id` int(10) NOT NULL AUTO_INCREMENT,
                          `host` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'ip地址',
                          `port` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '端口号',
                          `status` int(4) NOT NULL DEFAULT 1 COMMENT '1 有效  0 无效',
                          `create_time` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
                          PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 40 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin COMMENT = '代理表' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of proxy
-- ----------------------------

-- ----------------------------
-- Table structure for semgrep
-- ----------------------------
DROP TABLE IF EXISTS `semgrep`;
CREATE TABLE `semgrep`  (
                            `id` int(11) NOT NULL AUTO_INCREMENT,
                            `check_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                            `code_id` int(11) NULL DEFAULT NULL COMMENT '项目ID',
                            `create_time` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0),
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
                            PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = Compact;

-- ----------------------------
-- Records of semgrep
-- ----------------------------

-- ----------------------------
-- Table structure for svn_project
-- ----------------------------
DROP TABLE IF EXISTS `svn_project`;
CREATE TABLE `svn_project`  (
                                `id` int(11) NOT NULL AUTO_INCREMENT,
                                `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                                `command` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                                `scan_time` datetime(0) NULL DEFAULT '2000-01-01 14:14:14',
                                PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of svn_project
-- ----------------------------

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
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = Compact;

-- ----------------------------
-- Records of system_config
-- ----------------------------
INSERT INTO `system_config` VALUES (1, 'fofa用户名', 'fofa_user', NULL, 0);
INSERT INTO `system_config` VALUES (2, 'fofa密钥', 'fofa_token', NULL, 0);
INSERT INTO `system_config` VALUES (3, '百度ak', 'baidu_ak', 'xxxxxxxx', 0);
INSERT INTO `system_config` VALUES (4, 'github秘钥', 'github_token', 'xxxxxxxxx', 0);

-- ----------------------------
-- Table structure for task_host_scan
-- ----------------------------
DROP TABLE IF EXISTS `task_host_scan`;
CREATE TABLE `task_host_scan`  (
                                   `id` int(11) NOT NULL AUTO_INCREMENT,
                                   `app_id` int(11) NULL DEFAULT NULL,
                                   `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                                   `status` tinyint(4) NOT NULL DEFAULT 0,
                                   `create_time` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0),
                                   `update_time` datetime(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
                                   PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = Compact;

-- ----------------------------
-- Records of task_host_scan
-- ----------------------------

-- ----------------------------
-- Table structure for task_url_crawler
-- ----------------------------
DROP TABLE IF EXISTS `task_url_crawler`;
CREATE TABLE `task_url_crawler`  (
                                     `id` int(11) NOT NULL AUTO_INCREMENT,
                                     `app_id` int(11) NULL DEFAULT NULL,
                                     `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                                     `status` tinyint(4) NOT NULL DEFAULT 0,
                                     `create_time` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0),
                                     `update_time` datetime(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
                                     PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = Compact;

-- ----------------------------
-- Records of task_url_crawler
-- ----------------------------

-- ----------------------------
-- Table structure for text
-- ----------------------------
DROP TABLE IF EXISTS `text`;
CREATE TABLE `text`  (
                         `hash` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
                         `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
                         `create_time` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
                         PRIMARY KEY (`hash`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of text
-- ----------------------------

-- ----------------------------
-- Table structure for urls
-- ----------------------------
DROP TABLE IF EXISTS `urls`;
CREATE TABLE `urls`  (
                         `id` int(11) NOT NULL AUTO_INCREMENT,
                         `method` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
                         `app_id` int(11) NULL DEFAULT 0,
                         `url` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                         `status` tinyint(4) NOT NULL DEFAULT 1,
                         `create_time` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
                         `header` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                         `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
                         `response_header` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
                         `hash` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                         `scan_time` datetime(0) NULL DEFAULT '2000-01-01 00:00:00',
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
                         `sqlmap_scan_time` datetime(0) NULL DEFAULT '2000-01-01 00:00:00',
                         `id_card` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
                         `email` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
                         `phone` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
                         `icp` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
                         `user_id` int(10) NOT NULL DEFAULT 0,
                         PRIMARY KEY (`id`) USING BTREE,
                         UNIQUE INDEX `un_url`(`hash`) USING BTREE,
                         INDEX `appid`(`app_id`) USING BTREE,
                         CONSTRAINT `appid` FOREIGN KEY (`app_id`) REFERENCES `app` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 232 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = Compact;

-- ----------------------------
-- Records of urls
-- ----------------------------

-- ----------------------------
-- Table structure for urls_sqlmap
-- ----------------------------
DROP TABLE IF EXISTS `urls_sqlmap`;
CREATE TABLE `urls_sqlmap`  (
                                `id` int(10) NOT NULL AUTO_INCREMENT,
                                `urls_id` int(10) NOT NULL,
                                `create_time` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0),
                                `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL COMMENT '注入类型',
                                `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                                `payload` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
                                `app_id` int(10) NOT NULL DEFAULT 0,
                                `system` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                                `application` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                                `dbms` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                                `user_id` int(10) NOT NULL DEFAULT 0,
                                PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = Compact;

-- ----------------------------
-- Records of urls_sqlmap
-- ----------------------------

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
) ENGINE = InnoDB AUTO_INCREMENT = 13 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = Compact;

-- ----------------------------
-- Records of user
-- ----------------------------
INSERT INTO `user` VALUES (1, 'test', 'ed04f8ec326fa29e2ebb413729fc92d2', '', '测试', 5, 1635494087, '', 0, 1, 1638004150, 0, 0, '', '', '', '1ca4725c34758183af3fd1f723f07a31', '');

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
                               `created_at` datetime(0) NULL DEFAULT NULL,
                               `updated_at` datetime(0) NULL DEFAULT NULL,
                               `user_id` int(10) NULL DEFAULT NULL,
                               `is_pass` int(10) NULL DEFAULT NULL,
                               `user_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                               `is_sub_attack` int(10) NULL DEFAULT NULL,
                               `temp_plan_s3_hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                               `patch_s3_hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                               `is_pass_attack` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                               `auditor` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                               `cause` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
                               `scan_time` datetime(0) NULL DEFAULT '2000-01-01 00:00:00',
                               `is_poc` int(10) NULL DEFAULT 0 COMMENT '是否有POC',
                               `is_delete` tinyint(1) NOT NULL DEFAULT 0,
                               `target_scan_time` datetime(0) NULL DEFAULT NULL,
                               PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2070 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of vulnerable
-- ----------------------------

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
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = Compact;

-- ----------------------------
-- Records of xray
-- ----------------------------

SET FOREIGN_KEY_CHECKS = 1;

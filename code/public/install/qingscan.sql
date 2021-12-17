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
                               `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT '' COMMENT '描述',
                               `reference` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT '',
                               `severity` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT '',
                               `type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT '' COMMENT '协议类型',
                               `host` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT '',
                               `matched_at` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT '',
                               `extracted_results` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT '',
                               `ip` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT '',
                               `create_time` datetime(0) NULL DEFAULT NULL,
                               `curl_command` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
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
-- Records of app_xray_agent_port
-- ----------------------------
INSERT INTO `app_xray_agent_port` VALUES (1, 2083, 40417, '2021-12-16 18:09:28', 0, 0);

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
                        `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
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
                           `result_type` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT 'json、csv、txt',
                           `update_time` datetime(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
                           PRIMARY KEY (`id`) USING BTREE,
                           UNIQUE INDEX `un_name`(`name`, `user_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin COMMENT = '自定义插件' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of plugin
-- ----------------------------
INSERT INTO `plugin` VALUES (2, 0, 'curl', 'curl  ##URL##', '/tmp', '2021-12-14 08:54:53', 1, 0, 'csv', '2021-12-15 18:58:54');

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
                                  `create_time` datetime(0) NULL DEFAULT '2000-01-01 00:00:00',
                                  `check_status` tinyint(1) NOT NULL COMMENT '审核状态',
                                  `plugin_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL COMMENT '插件名称',
                                  PRIMARY KEY (`id`) USING BTREE,
                                  UNIQUE INDEX `un_id`(`app_id`, `user_id`, `plugin_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 8 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin COMMENT = '自定义插件扫描结果' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of plugin_result
-- ----------------------------
INSERT INTO `plugin_result` VALUES (7, 2083, 2, 0, 'nohup: ignoring input\n  % Total    % Received % Xferd  Average Speed   Time    Time     Time  Current\n                                 Dload  Upload   Total   Spent    Left  Speed\n\r  0     0    0     0    0     0      0      0 --:--:-- --:--:-- --:--:--     0<!DOCTYPE html>\n<html lang=\"zh-cn\">\n<head>\n	<!-- 头部 -->\n	<meta charset=\"utf-8\">\n<meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">\n<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">\n<link rel=\"shortcut icon\" href=\"/favicon.ico\"/>\n<title>轻松阅读 - 互联网开发者的聚集平台</title>\n<meta name=\"keywords\" content=\"php html5 css3 nginx\"/>\n<meta name=\"description\" content=\"专注于分享互联网开发技术文档，前沿科技资讯。\"/>\n<link rel=\"stylesheet\" href=\"/Public/Home/phptalk2.0/uikit/css/uikit.docs.min.css\" />\n<link rel=\"stylesheet\" href=\"/Public/Home/phptalk2.0/css/global.css\" />\n<script src=\"/Public/Home/phptalk2.0/js/jquery.js\"></script>\n	<!-- /头部 -->\n</head>\n<body>\n	\n	<!-- 导航 -->\n	<nav class=\"uk-navbar nav\">\n    \n    <!--社交-->\n    <div class=\"uk-navbar-flip\">\n        <div class=\"current-music\"></div>\n        <a href=\"javascript:;\" title=\"音乐\" class=\"uk-icon-music music-switch music-open\"></a>\n        <a href=\"javascript:;\" class=\"wechat\"><i class=\"uk-icon-wechat\"></i></a>\n        <div class=\"weixin\"><img src=\"/Public/Home/phptalk2.0/images/weixin.jpg\"><br />扫一扫关注微信</div>\n        <a href=\"http://weibo.com/qingsongboy\" target=\"_blank\" title=\"新浪微博\" class=\"weibo\"><i class=\"uk-icon-weibo\"></i></a>\n    </div>\n\n    <!--LOGO-->\n    <div class=\"uk-navbar-content uk-navbar-center uk-h3\"><a href=\"/\" class=\"logo pjax-link\" style=\"line-height:40px;\">轻松阅读</a></small><div class=\"uk-modal-spinner loading-info\"></div></div>\n\n\n</nav>\n<a href=\"javascript:;\" id=\"menu\" class=\"uk-navbar-toggle\"></a>\n<nav id=\"nav-center\">\n    <ul>\n    <li><a href=\"/Article/lists/category/php.html\"  class=\"pjax-link\"><i class=\"uk-icon-code\" style=\"margin-right:10px;width:20px;\"></i>PHP</a></li><li><a href=\"/Article/lists/category/xinde.html\"  class=\"pjax-link\"><i class=\"uk-icon-\" style=\"margin-right:10px;width:20px;\"></i>学习心得</a></li><li><a href=\"/Article/lists/category/linux.html\"  class=\"pjax-link\"><i class=\"uk-icon-apple\" style=\"margin-right:10px;width:20px;\"></i>Mac/Linux</a></li><li><a href=\"/Article/lists/category/web_safe.html\"  class=\"pjax-link\"><i class=\"uk-icon-\" style=\"margin-right:10px;width:20px;\"></i>WEB安全</a></li><li><a href=\"/Article/lists/category/mysql.html\"  class=\"pjax-link\"><i class=\"uk-icon-database\" style=\"margin-right:10px;width:20px;\"></i>数据库</a></li><li><a href=\"/Article/lists/category/work_diary.html\"  class=\"pjax-link\"><i class=\"uk-icon-\" style=\"margin-right:10px;width:20px;\"></i>工作日记</a></li><li><a href=\"/Article/lists/category/html5.html\"  class=\"pjax-link\"><i class=\"uk-icon-html5\" style=\"margin-right:10px;width:20px;\"></i>WEB前端</a></li><li><a href=\"/Article/lists/category/webnews.html\"  class=\"pjax-link\"><i class=\"uk-icon-eye\" style=\"margin-right:10px;width:20px;\"></i>科技人文</a></li><li><a href=\"/Article/lists/category/mobileweb.html\"  class=\"pjax-link\"><i class=\"uk-icon-group\" style=\"margin-right:10px;width:20px;\"></i>移动互联</a></li><li><a href=\"/Article/lists/category/jquery.html\"  class=\"pjax-link\"><i class=\"uk-icon-paper-plane\" style=\"margin-right:10px;width:20px;\"></i>设计/素材</a></li><li><a href=\"/Article/lists/category/toossoft.html\"  class=\"pjax-link\"><i class=\"uk-icon-dropbox\" style=\"margin-right:10px;width:20px;\"></i>工具/APP</a></li>	</ul>\n</nav>\n\n<!-- 音乐 -->\n<div class=\"music-box\">\n    <div class=\"music-box-content\">\n        <a href=\"javascript:;\" class=\"music-switch uk-close music-close\"></a>\n        <h3><i class=\"uk-icon-music\"></i>&nbsp;&nbsp;音乐</h3>\n        <audio preload></audio>\n        <ol id=\"music-ol\">\n           <li><a href=\"javascript:;\" data-src=\"/Uploads/Music/Sting - Fields Of Gold.mp3\">Fields Of Gold - Sting</a></li>\n           <li><a href=\"javascript:;\" data-src=\"/Uploads/Music/INXS - Beautiful Girl.mp3\">Beautiful Girl - INXS</a></li>\n           <li><a href=\"javascript:;\" data-src=\"/Uploads/Music/Hozier - Take Me To Church.mp3\">Take Me To Church - Hozier</a></li>\n           <li><a href=\"javascript:;\" data-src=\"/Uploads/Music/Lady & Bird - Stephanie Says.mp3\">Stephanie Says - Lady & Bird</a></li>\n           <li><a href=\"javascript:;\" data-src=\"/Uploads/Music/John Legend - All of Me.mp3\">All of Me - John Legend</a></li>\n           <li><a href=\"javascript:;\" data-src=\"/Uploads/Music/yisibugua.mp3\">一丝不挂 - 陈奕迅</a></li>\n        </ol>\n    </div>\n</div>\n	<!-- /导航 -->\n\n    <div class=\"uk-grid uk-grid-collapse\">\n        <div id=\"container\" class=\"uk-container-center\">	<script>\n    //初始化\n    global = {};\n    global[\"Home\"] = {};\n    global[\"Home\"][\"Index\"] = {};\n    NS = global[\"Home\"][\"Index\"][\"index\"] = {};\n    NS.pageTitle = \' 轻松阅读 - 互联网开发者的聚集平台\';\n    document.title = NS.pageTitle;\n    $(\"title\").html(NS.pageTitle);\n	</script>\n	<!-- 主体 -->\n	<div id=\"container-mask\"></div>\n	\n    <script src=\"/Public/Home/phptalk2.0/js/pt.slide.js\"></script>\n    <script>$(function(){\n        NS.firstData = $.parseJSON(\'[{\"id\":\"104\",\"uid\":\"1\",\"name\":\"\",\"title\":\"PHP\\u4f7f\\u7528elasticsearch\\u641c\\u7d22\\u5b89\\u88c5\\u53ca\\u5206\\u8bcd\\u65b9\\u6cd5\",\"category_id\":\"43\",\"description\":\"\\u4e3a\\u4ec0\\u4e48\\u4f1a\\u7528\\u5230\\u8fd9\\u4e2aES\\u641c\\u7d22\\uff1f\\u662f\\u56e0\\u4e3a\\u6211\\u5728\\u770b\\u4e4c\\u4e91\\u7684\\u6f0f\\u6d1e\\u6848\\u4f8b\\u5e93\\u65f6\\u5019\\u641c\\u7d22\\u5373\\u4e3a\\u4e0d\\u65b9\\u4fbf\\u6bd4\\u5982\\u8bf4\\u8bf4\\u6211\\u8981\\u641c\\u7d22\\u4e00\\u4e2aSQL\\u6ce8\\u5165\\u90a3mysql\\u5339\\u914d\\u7684\\u65f6\\u5019\\u662flike\\u6a21\\u7cca\\u5339\\u914d\\u641c\\u7d22\",\"root\":\"0\",\"pid\":\"0\",\"model_id\":\"2\",\"type\":\"2\",\"position\":\"0\",\"link_id\":\"0\",\"cover_id\":\"0\",\"display\":\"1\",\"deadline\":\"0\",\"attach\":\"0\",\"view\":\"2461\",\"comment\":\"0\",\"extend\":\"0\",\"level\":\"0\",\"create_time\":\"12:42 \\/ 22\",\"update_time\":\"1508647816\",\"status\":\"1\",\"category\":\"PHP\",\"picurl\":\"\"},{\"id\":\"103\",\"uid\":\"1\",\"name\":\"\",\"title\":\"\\u5982\\u4f55\\u505a\\u597d\\u4e00\\u573a\\u6280\\u672f\\u5206\\u4eab\\uff1f--\\u6280\\u5de7\\u7bc7\",\"category_id\":\"58\",\"description\":\"\\u524d\\u5728\\u4e92\\u8054\\u7f51\\u884c\\u4e1a\\u5404\\u79cd\\u5927\\u4f1a\\u8d8a\\u6765\\u8d8a\\u591a\\uff0c\\u7ebf\\u4e0a\\u6559\\u80b2\\u5e73\\u53f0\\u4e5f\\u5982\\u96e8\\u540e\\u6625\\u7b0b\\u4e00\\u822c\\u6392\\u904d\\u5730\\u5f00\\u82b1\\u3002\\u4f5c\\u4e3a\\u6211\\u4eec\\u4e92\\u8054\\u7f51\\u4ece\\u4e1a\\u8005\\u6765\\u8bf4\\uff0c\\u4ece\\u6700\\u521d\\u7684\\u77e5\\u8bc6\\u8f93\\u5165\\u3001\\u9971\\u548c\\uff0c\\u5230\\u5185\\u5bb9\\u8f93\\u51fa\\uff1b\\u63d0\\u9ad8\\u5206\\u4eab\\u80fd\\u529b\\u663e\\u5f97\\u8d8a\\u53d1\\u91cd\\u8981\\u8d77\\u6765\\uff0c\\u901a\\u8fc7\\u5206\\u4eab\\u4e0d\\u4ec5\\u80fd\\u7ed9\\u81ea\\u5df1\\u589e\\u52a0\\u4e00\\u90e8\\u5206\\u6536\\u5165\",\"root\":\"0\",\"pid\":\"0\",\"model_id\":\"2\",\"type\":\"2\",\"position\":\"0\",\"link_id\":\"0\",\"cover_id\":\"122\",\"display\":\"1\",\"deadline\":\"0\",\"attach\":\"0\",\"view\":\"1974\",\"comment\":\"0\",\"extend\":\"0\",\"level\":\"0\",\"create_time\":\"10:27 \\/ 15\",\"update_time\":\"1505485709\",\"status\":\"1\",\"category\":\"\\u5b66\\u4e60\\u5fc3\\u5f97\",\"picurl\":\"http:\\/\\/7sbrd6.com1.z0.glb.clouddn.com\\/59bbe310f14bd.png\"},{\"id\":\"102\",\"uid\":\"1\",\"name\":\"\",\"title\":\"WEB\\u5b89\\u5168\\u7528\\u6237\\u5bc6\\u7801\\u627e\\u56de\\u591a\\u6848\\u4f8b\\u5b89\\u5168\\u653b\\u9632\\u5b9e\\u6218\\u89e3\\u6790\",\"category_id\":\"56\",\"description\":\"\\u8fd9\\u6b21\\u6587\\u7ae0\\u4ee5wooyun\\u7684\\u5bc6\\u7801\\u627e\\u56de\\u4ee3\\u8868\\u6027\\u6f0f\\u6d1e\\u4f5c\\u4e3a\\u6848\\u4f8b\\u6765\\u8bb2\\u89e3,\\u6f0f\\u6d1e\\u7684\\u63cf\\u8ff0\\u4f1a\\u901a\\u8fc7\\u63d0\\u4ea4\\u6f0f\\u6d1e\\u7684\\u539f\\u63cf\\u8ff0\\u52a0\\u4e0a\\u6211\\u7684\\u7406\\u89e3\\u4e00\\u4e00\\u5217\\u51fa\\uff0c\\u901a\\u8fc7\\u5bc6\\u7801\\u627e\\u56de\\u7684\\u8fc7\\u7a0b\\u63cf\\u8ff0\\uff0c\\u5f97\\u51fa\\u4ece\\u6f0f\\u6d1e\\u7684\\u53d1\\u73b0\\u5230\\u6f0f\\u6d1e\\u7684\\u5206\\u6790\\u3002\",\"root\":\"0\",\"pid\":\"0\",\"model_id\":\"2\",\"type\":\"2\",\"position\":\"0\",\"link_id\":\"0\",\"cover_id\":\"0\",\"display\":\"1\",\"deadline\":\"0\",\"attach\":\"0\",\"view\":\"2076\",\"comment\":\"0\",\"extend\":\"0\",\"level\":\"0\",\"create_time\":\"04:05 \\/ 13\",\"update_time\":\"1502611549\",\"status\":\"1\",\"category\":\"WEB\\u5b89\\u5168\",\"picurl\":\"\"},{\"id\":\"101\",\"uid\":\"1\",\"name\":\"\",\"title\":\"SQLMAP\\u4e2d\\u6587\\u6ce8\\u91ca\\u6587\\u6863\",\"category_id\":\"56\",\"description\":\"sqlmap\\u5b98\\u65b9\\u5e2e\\u52a9\\u662f\\u82f1\\u6587\\u7684,\\u4e0d\\u5229\\u4e8e\\u5927\\u5bb6\\u7684\\u67e5\\u770b,\\u8fd9\\u91cc\\u6574\\u7406\\u4e86\\u4e00\\u4efd\\u7ffb\\u8bd1\\u6587\\u6863,\\u5e0c\\u671b\\u80fd\\u5e2e\\u52a9\\u5230\\u5927\\u5bb6\",\"root\":\"0\",\"pid\":\"0\",\"model_id\":\"2\",\"type\":\"2\",\"position\":\"0\",\"link_id\":\"0\",\"cover_id\":\"0\",\"display\":\"1\",\"deadline\":\"0\",\"attach\":\"0\",\"view\":\"1882\",\"comment\":\"0\",\"extend\":\"0\",\"level\":\"0\",\"create_time\":\"05:56 \\/ 08\",\"update_time\":\"1502186184\",\"status\":\"1\",\"category\":\"WEB\\u5b89\\u5168\",\"picurl\":\"\"},{\"id\":\"100\",\"uid\":\"1\",\"name\":\"\",\"title\":\"PHP\\u4ee3\\u7801\\u5ba1\\u8ba1\\u7ecf\\u9a8c\\u603b\\u7ed3\",\"category_id\":\"56\",\"description\":\"\\u5de5\\u6b32\\u5584\\u5176\\u4e8b\\uff0c\\u5fc5\\u5148\\u5229\\u5176\\u5668. \\u6211\\u4eec\\u505a\\u4ee3\\u7801\\u5ba1\\u8ba1\\u4e4b\\u524d\\u9009\\u597d\\u5de5\\u5177\\u4e5f\\u662f\\u5341\\u5206\\u5fc5\\u8981\\u7684,\\u4e0b\\u9762\\u6211\\u7ed9\\u5927\\u5bb6\\u4ecb\\u7ecd\\u4e24\\u6b3e\\u4ee3\\u7801\\u5ba1\\u8ba1\\u4e2d\\u6bd4\\u8f83\\u597d\\u7528\\u7684\\u5de5\\u5177\",\"root\":\"0\",\"pid\":\"0\",\"model_id\":\"2\",\"type\":\"2\",\"position\":\"0\",\"link_id\":\"0\",\"cover_id\":\"0\",\"display\":\"1\",\"deadline\":\"0\",\"attach\":\"0\",\"view\":\"2365\",\"comment\":\"0\",\"extend\":\"0\",\"level\":\"0\",\"create_time\":\"08:26 \\/ 11\",\"update_time\":\"1499775977\",\"status\":\"1\",\"category\":\"WEB\\u5b89\\u5168\",\"picurl\":\"\"},{\"id\":\"99\",\"uid\":\"1\",\"name\":\"\",\"title\":\"PHP\\u4ee3\\u7801\\u6ce8\\u5165\\u9632\\u8303\\u603b\\u7ed3\",\"category_id\":\"56\",\"description\":\"    \\u4f17\\u6240\\u5468\\u77e5\\u7684preg_replace()\\u51fd\\u6570\\u5bfc\\u81f4\\u7684\\u4ee3\\u7801\\u6ce8\\u5c04\\u3002\\u5f53pattern\\u4e2d\\u5b58\\u5728\\/e\\u6a21\\u5f0f\\u4fee\\u9970\\u7b26\\uff0c\\u5373\\u5141\\u8bb8\\u6267\\u884c\\u4ee3\\u7801\\u3002\\u8fd9\\u91cc\\u6211\\u4eec\\u5206\\u4e09\\u79cd\\u60c5\\u51b5\\u8ba8\\u8bba\\u4e0b\",\"root\":\"0\",\"pid\":\"0\",\"model_id\":\"2\",\"type\":\"2\",\"position\":\"0\",\"link_id\":\"0\",\"cover_id\":\"0\",\"display\":\"1\",\"deadline\":\"0\",\"attach\":\"0\",\"view\":\"1941\",\"comment\":\"0\",\"extend\":\"0\",\"level\":\"0\",\"create_time\":\"12:24 \\/ 03\",\"update_time\":\"1496463852\",\"status\":\"1\",\"category\":\"WEB\\u5b89\\u5168\",\"picurl\":\"\"},{\"id\":\"98\",\"uid\":\"1\",\"name\":\"\",\"title\":\"PHP\\u4ee3\\u7801\\u5ba1\\u8ba1\\u6280\\u5de7\",\"category_id\":\"56\",\"description\":\"\\u4ee3\\u7801\\u5ba1\\u6838\\uff0c\\u662f\\u5bf9\\u5e94\\u7528\\u7a0b\\u5e8f\\u6e90\\u4ee3\\u7801\\u8fdb\\u884c\\u7cfb\\u7edf\\u6027\\u68c0\\u67e5\\u7684\\u5de5\\u4f5c\\u3002\\u5b83\\u7684\\u76ee\\u7684\\u662f\\u4e3a\\u4e86\\u627e\\u5230\\u5e76\\u4e14\\u4fee\\u590d\\u5e94\\u7528\\u7a0b\\u5e8f\\u5728\\u5f00\\u53d1\\u9636\\u6bb5\\u5b58\\u5728\\u7684\\u4e00\\u4e9b\\u6f0f\\u6d1e\\u6216\\u8005\\u7a0b\\u5e8f\\u903b\\u8f91\\u9519\\u8bef\\uff0c\\u907f\\u514d\\u7a0b\\u5e8f\\u6f0f\\u6d1e\\u88ab\\u975e\\u6cd5\\u5229\\u7528\\u7ed9\\u4f01\\u4e1a\\u5e26\\u6765\\u4e0d\\u5fc5\\u8981\\u7684\\u98ce\\u9669\\u3002\",\"root\":\"0\",\"pid\":\"0\",\"model_id\":\"2\",\"type\":\"2\",\"position\":\"0\",\"link_id\":\"0\",\"cover_id\":\"0\",\"display\":\"1\",\"deadline\":\"0\",\"attach\":\"0\",\"view\":\"1986\",\"comment\":\"0\",\"extend\":\"0\",\"level\":\"0\",\"create_time\":\"07:20 \\/ 27\",\"update_time\":\"1495840854\",\"status\":\"1\",\"category\":\"WEB\\u5b89\\u5168\",\"picurl\":\"\"},{\"id\":\"97\",\"uid\":\"1\",\"name\":\"\",\"title\":\"\\u601d\\u7ef4\\u4e09\\u90e8\\u66f2\\uff1aWHAT\\u3001HOW\\u3001WHY\",\"category_id\":\"58\",\"description\":\"\\u6211\\u628a\\u5b66\\u4e60\\u5f52\\u7c7b\\u4e3a\\u4e09\\u4e2a\\u6b65\\u9aa4\\uff1aWhat\\u3001How\\u3001Why\\u3002\\u7ecf\\u8fc7\\u6211\\u5bf9\\u5468\\u56f4\\u540c\\u4e8b\\u548c\\u670b\\u53cb\\u7684\\u89c2\\u5bdf\\uff0c\\u5927\\u90e8\\u5206\\u611f\\u89c9\\u81ea\\u5df1\\u6280\\u672f\\u6ca1\\u6709\\u63d0\\u9ad8\\u7684\\u4eba\\uff0c\\u90fd\\u4ec5\\u4ec5\\u505c\\u7559\\u5728What\\u9636\\u6bb5\\u3002\\u4e0b\\u9762\\u6211\\u628a\\u8fd9\\u4e09\\u4e2a\\u6b65\\u9aa4\\u89e3\\u91ca\\u4e00\\u4e0b\",\"root\":\"0\",\"pid\":\"0\",\"model_id\":\"2\",\"type\":\"2\",\"position\":\"0\",\"link_id\":\"0\",\"cover_id\":\"0\",\"display\":\"1\",\"deadline\":\"0\",\"attach\":\"0\",\"view\":\"1930\",\"comment\":\"0\",\"extend\":\"0\",\"level\":\"0\",\"create_time\":\"07:14 \\/ 27\",\"update_time\":\"1495840735\",\"status\":\"1\",\"category\":\"\\u5b66\\u4e60\\u5fc3\\u5f97\",\"picurl\":\"\"},{\"id\":\"96\",\"uid\":\"1\",\"name\":\"\",\"title\":\"modsecurity \\u5b89\\u88c5\\u6559\\u7a0bnginx\",\"category_id\":\"56\",\"description\":\"modsecurity\\u539f\\u672c\\u662fApache\\u4e0a\\u7684\\u4e00\\u6b3e\\u5f00\\u6e90waf\\uff0c\\u53ef\\u4ee5\\u6709\\u6548\\u7684\\u589e\\u5f3aweb\\u5b89\\u5168\\u6027\\uff0c\\u76ee\\u524d\\u5df2\\u7ecf\\u652f\\u6301nginx\\u548cIIS\\uff0c\\u914d\\u5408nginx\\u7684\\u7075\\u6d3b\\u548c\\u9ad8\\u6548\\uff0c\\u53ef\\u4ee5\\u6253\\u9020\\u6210\\u751f\\u4ea7\\u7ea7\\u7684WAF\\uff0c\\u662f\\u4fdd\\u62a4\\u548c\\u5ba1\\u6838web\\u5b89\\u5168\\u7684\\u5229\\u5668\\u3002\",\"root\":\"0\",\"pid\":\"0\",\"model_id\":\"2\",\"type\":\"2\",\"position\":\"0\",\"link_id\":\"0\",\"cover_id\":\"0\",\"display\":\"1\",\"deadline\":\"0\",\"attach\":\"0\",\"view\":\"2100\",\"comment\":\"0\",\"extend\":\"0\",\"level\":\"0\",\"create_time\":\"12:18 \\/ 11\",\"update_time\":\"1494433176\",\"status\":\"1\",\"category\":\"WEB\\u5b89\\u5168\",\"picurl\":\"\"},{\"id\":\"94\",\"uid\":\"1\",\"name\":\"\",\"title\":\"\\u7f16\\u8f91\\u5668\\u6f0f\\u6d1e\\u603b\\u7ed3\",\"category_id\":\"56\",\"description\":\"\\u51e0\\u4e2a\\u7f16\\u8f91\\u5668\\u6f0f\\u6d1e\\u603b\\u7ed3\",\"root\":\"0\",\"pid\":\"0\",\"model_id\":\"2\",\"type\":\"2\",\"position\":\"0\",\"link_id\":\"0\",\"cover_id\":\"0\",\"display\":\"1\",\"deadline\":\"0\",\"attach\":\"0\",\"view\":\"2288\",\"comment\":\"0\",\"extend\":\"0\",\"level\":\"0\",\"create_time\":\"09:35 \\/ 29\",\"update_time\":\"1490794514\",\"status\":\"1\",\"category\":\"WEB\\u5b89\\u5168\",\"picurl\":\"\"}]\');\n    });</script>\n    <script src=\"/Public/Home/phptalk2.0/js/home.index.index.js\"></script>\n\n\n    <!--banner-->\n    <div class=\"pt_slide uk-margin-large-bottom\">\n        <ul class=\"pt_slide_ul\">\n            <li>\n                    <a href=\"/article/detail/id/82\" class=\"pjax-link\">\n                        <img src=\"http://7sbrd6.com1.z0.glb.clouddn.com/55307387ce57d.jpg\" class=\"border-radius\">\n                        <p>如何构建高扩展性网站？</p>\n                    </a>\n                </li><li>\n                    <a href=\"/article/detail/id/77\" class=\"pjax-link\">\n                        <img src=\"http://7sbrd6.com1.z0.glb.clouddn.com/5526399823f83.jpg\" class=\"border-radius\">\n                        <p>TencentOS还是腾讯连接一切的生力军吗？</p>\n                    </a>\n                </li><li>\n                    <a href=\"/article/detail/id/79\" class=\"pjax-link\">\n                        <img src=\"http://7sbrd6.com1.z0.glb.clouddn.com/55264510ceb76.jpg\" class=\"border-radius\">\n                        <p>开发者首选PHP框架排行 Laravel蝉联第一</p>\n                    </a>\n                </li><li>\n                    <a href=\"/article/detail/id/80\" class=\"pjax-link\">\n                        <img src=\"http://7sbrd6.com1.z0.glb.clouddn.com/552646ec50995.jpg\" class=\"border-radius\">\n                        <p>58到家和小米可能已抢占了 HTML 5 的先机</p>\n                    </a>\n                </li><li>\n                    <a href=\"article/detail/id/76\" class=\"pjax-link\">\n                        <img src=\"http://7sbrd6.com1.z0.glb.clouddn.com/552636e68d942.jpg\" class=\"border-radius\">\n                        <p>我们真的需要一双智能跑鞋吗？</p>\n                    </a>\n                </li>        </ul>\n        <a href=\"javascript:;\" class=\"pt_slide_prev\">〈</a>\n        <a href=\"javascript:;\" class=\"pt_slide_next\">〉</a>\n    </div>\n\n    <!--news list-->\n    <div class=\"border-radius\" style=\"margin:0px;\">\n        <p class=\"uk-h5 title\" style=\"margin:0px;\"><i class=\"uk-icon-th\"></i>&nbsp;&nbsp;近期</p>\n        <div class=\"news\"></div>\n        <a href=\"javascript:;\" id=\"load-more\" class=\"loadStatus\">加载更多</a>\n    </div>\n\n    <!--tpl-->\n    <script id=\"require_pic_tpl\" type=\"text/plain\">\n        <div class=\"news_list uk-clearfix\" data-id=\"{id}\">\n            <div class=\"uk-grid uk-grid-collapse\" data-uk-grid-margin>\n                <div class=\"uk-width-medium-3-10 uk-width-small-3-10 uk-width-3-10\">\n                    <div class=\"uk-panel news_list_img_box\"><a href=\"{picurl}\" data-uk-lightbox=\"{group:\'index-list\'}\" title=\"{title}\"><img src=\"{picurl}\"  class=\"uk-vertical-align-middle\" alt=\"{title}\"></a></div>\n                </div>\n                <div class=\"uk-width-medium-7-10 uk-width-small-7-10 uk-width-7-10\">\n                    <h3><a href=\"/article/detail/id/{id}\" class=\"pjax-link\">{title}</a></h3>\n                    <p class=\"uk-hidden-small\"><a href=\"/article/detail/id/{id}\" class=\"pjax-link\">{description}</a></p>\n                </div>\n            </div>\n        </div>\n    </script>\n    <script id=\"not_require_pic_tpl\" type=\"text/plain\">\n        <div class=\"news_list uk-clearfix\" data-id=\"{id}\">\n            <div class=\"uk-grid uk-grid-collapse\" data-uk-grid-margin>\n                <div class=\"uk-width-medium-1-1\">\n                    <h3 class=\"no-margin\"><a href=\"/article/detail/id/{id}\" class=\"pjax-link\">{title}</a></h3>\n                    <p class=\"no-margin\"><a href=\"/article/detail/id/{id}\" class=\"pjax-link\">{description}</a></p>\n                </div>\n            </div>\n        </div>\n    </script>\n\n	<!-- /主体 -->\n</div>\n    </div>	\n	<!-- 底部 -->\n	<footer>Copyright &copy; 2015 Design By 轻松阅读. <a href=\"http://www.miitbeian.gov.cn\" target=\"_blank\">京ICP备14035553号</a>\n<div style=\"display:none;\">\n	<script type=\"text/javascript\">\n		var cnzz_protocol = ((\"https:\" == document.location.protocol) ? \" https://\" : \" http://\");\n			document.write(unescape(\"%3Cspan id=\'cnzz_stat_icon_1254777365\'%3E%3C/span%3E%3Cscript src=\'\" + cnzz_protocol + \"s95.cnzz.com/stat.php%3Fid%3D1254777365\' type=\'text/javascript\'%3E%3C/script%3E\"));\n	</script>\n</div>\n<!--返回顶部-->\n<a style=\"cursor:pointer\" href=\"javascript:;\" data-uk-smooth-scroll class=\"gotop\"><i class=\"uk-icon-chevron-up\"></i></a></footer>\n<script src=\"/Public/Home/phptalk2.0/js/pjax.js\"></script>\n<script src=\"/Public/Home/phptalk2.0/js/global.js\"></script>\n<script src=\"/Public/Home/phptalk2.0/js/home.index.index.js\"></script>\n<script src=\"/Public/Home/phptalk2.0/uikit/js/uikit.min.js\"></script>\n<script src=\"/Public/Home/phptalk2.0/uikit/js/components/tooltip.min.js\"></script>\n<script src=\"/Public/Home/phptalk2.0/uikit/js/components/lightbox.min.js\"></script>\n<script src=\"/Public/Home/phptalk2.0/js/aud\r100 18101    0 18101    0     0   238k      0 --:--:-- --:--:-- --:--:--  238k\niojs/audio.min.js\"></script>\n<script>var duoshuoQuery = {short_name:\"phptalk\"};</script>\n<script src=\"http://static.duoshuo.com/embed.js\"></script>\n	<!-- /底部 -->\n</body>\n</html>', '2000-01-01 00:00:00', 0, 'curl');

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
INSERT INTO `process_safe` VALUES (2, 'scan xray', 'cd /root/qingscan/code  &&  php think scan xray  >> /tmp/xray.txt & ', 0, '', NULL);
INSERT INTO `process_safe` VALUES (3, 'scan awvs', 'cd /root/qingscan/code  &&  php think scan awvs  >> /tmp/awvs.txt & ', 0, '', NULL);
INSERT INTO `process_safe` VALUES (4, 'scan rad', 'cd /root/qingscan/code  &&  php think scan rad  >> /tmp/rad.txt & ', 0, '', NULL);
INSERT INTO `process_safe` VALUES (5, 'scan host', 'cd /root/qingscan/code  &&  php think scan host  >> /tmp/host.txt & ', 1, '', NULL);
INSERT INTO `process_safe` VALUES (6, 'scan port', 'cd /root/qingscan/code  &&  php think scan port  >> /tmp/port.txt & ', 1, '', NULL);
INSERT INTO `process_safe` VALUES (7, 'scan nmap', 'cd /root/qingscan/code  &&  php think scan nmap  >> /tmp/nmap.txt & ', 0, '', NULL);
INSERT INTO `process_safe` VALUES (8, 'scan fortify', 'cd /root/qingscan/code  &&  php think scan fortify  >> /tmp/fortify.txt & ', 0, '', NULL);
INSERT INTO `process_safe` VALUES (9, 'scan kunlun', 'cd /root/qingscan/code  &&  php think scan kunlun  >> /tmp/kunlun.txt & ', 1, '', NULL);
INSERT INTO `process_safe` VALUES (10, 'scan semgrep', 'cd /root/qingscan/code  &&  php think scan semgrep  >> /tmp/semgrep.txt & ', 1, '', NULL);
INSERT INTO `process_safe` VALUES (11, 'think run', 'cd /root/qingscan/code  &&  php think run  >> /tmp/run.txt & ', 0, '', NULL);
INSERT INTO `process_safe` VALUES (12, 'scan kafka', 'cd /root/qingscan/code  &&  php think scan kafka  >> /tmp/kafka.txt & ', 0, '', NULL);
INSERT INTO `process_safe` VALUES (13, 'scan google', 'cd /root/qingscan/code  &&  php think scan google >> /tmp/google.txt & ', 0, '', '2021-12-14 11:51:56');
INSERT INTO `process_safe` VALUES (14, 'scan upadteRegion', 'cd /root/qingscan/code  &&  php think scan upadteRegion >> /tmp/upadteRegion.txt & ', 0, '', '2021-12-14 11:51:54');
INSERT INTO `process_safe` VALUES (15, 'scan whatweb', 'cd /root/qingscan/code  &&  php think scan whatweb >> /tmp/whatweb.txt & ', 0, '', '2021-12-14 11:51:53');
INSERT INTO `process_safe` VALUES (16, 'scan subdomainScan', 'cd /root/qingscan/code  &&  php think scan subdomainScan >> /tmp/subdomainScan.txt & ', 0, '', NULL);
INSERT INTO `process_safe` VALUES (17, 'scan hydra', 'cd /root/qingscan/code  &&  php think scan hydra >> /tmp/hydra.txt & ', 0, '', NULL);
INSERT INTO `process_safe` VALUES (18, 'scan sqlmapScan', 'cd /root/qingscan/code  &&  php think scan sqlmapScan >> /tmp/sqlmapScan.txt & ', 0, '', NULL);
INSERT INTO `process_safe` VALUES (20, 'scan fofa', 'cd /root/qingscan/code  &&  php think scan fofa >> /tmp/fofa.txt & ', 0, '', NULL);
INSERT INTO `process_safe` VALUES (21, 'scan dirmapScan', 'cd /root/qingscan/code  &&  php think scan dirmapScan >> /tmp/dirmapScan.txt & ', 0, '', NULL);
INSERT INTO `process_safe` VALUES (22, 'scan getNotice', 'cd /root/qingscan/code  &&  php think scan getNotice >> /tmp/getNotice.txt & ', 0, '', NULL);
INSERT INTO `process_safe` VALUES (23, 'scan backup', 'cd /root/qingscan/code  &&  php think scan backup>> /tmp/backup.txt & ', 1, '数据库备份', NULL);
INSERT INTO `process_safe` VALUES (24, 'scan getProjectComposer', 'cd /root/qingscan/code  &&  php think scan getProjectComposer>> /tmp/composer.txt & ', 0, '获取composer组件', '2021-12-14 11:52:02');
INSERT INTO `process_safe` VALUES (25, 'scan code_python', 'cd /root/qingscan/code  &&  php think scan code_python>> /tmp/code_python.txt & ', 0, '获取python组件', '2021-12-14 11:52:04');
INSERT INTO `process_safe` VALUES (26, 'scan code_java', 'cd /root/qingscan/code  &&  php think scan code_java>> /tmp/code_java.txt & ', 0, '获取java组件', '2021-12-14 11:52:04');
INSERT INTO `process_safe` VALUES (27, 'scan giteeProject', 'cd /root/qingscan/code  &&  php think scan giteeProject>> /tmp/giteeProject.txt & ', 0, '获取码云项目', '2021-12-14 11:52:16');
INSERT INTO `process_safe` VALUES (28, 'scan freeAgent', 'cd /root/qingscan/code  &&  php think scan freeAgent>> /tmp/freeAgent.txt & ', 1, '获取免费代理', NULL);
INSERT INTO `process_safe` VALUES (29, 'scan github_keyword_monitor', 'cd /root/qingscan/think  &&  php think scan github_keyword_monitor>> /tmp/github_keyword_monitor.txt & ', 0, 'github关键字监控', NULL);
INSERT INTO `process_safe` VALUES (30, 'scan whatwebPocTest', 'cd /root/qingscan/think  &&  php think scan whatwebPocTest>> /tmp/whatwebPocTest.txt & ', 0, 'whatweb组件识别poc验证', NULL);
INSERT INTO `process_safe` VALUES (31, 'scan xrayAgentResult', 'cd /root/qingscan/think  &&  php think scan xrayAgentResult>> /tmp/xrayAgentResult.txt & ', 0, '获取xray代理模式结果数据', NULL);
INSERT INTO `process_safe` VALUES (32, 'scan startXrayAgent', 'cd /root/qingscan/think  &&  php think scan startXrayAgent>> /tmp/startXrayAgent.txt & ', 0, '启动xray代理模式', NULL);
INSERT INTO `process_safe` VALUES (33, 'scan code_webshell_scan', 'cd /root/qingscan/think  &&  php think scan code_webshell_scan>> /tmp/code_webshell_scan.txt & ', 0, '河马webshell检测', NULL);
INSERT INTO `process_safe` VALUES (35, 'scan wafw00fScan', 'cd /root/qingscan/think  &&  php think scan wafw00fScan>> /tmp/wafw00fScan.txt & ', 0, 'waf指纹识别', NULL);
INSERT INTO `process_safe` VALUES (37, 'scan nucleiScan', 'cd /root/qingscan/think  &&  php think scan nucleiScan>> /tmp/nucleiScan.txt & ', 0, 'nuclei扫描', NULL);
INSERT INTO `process_safe` VALUES (38, 'scan vulmapPocTest', 'cd /root/qingscan/think  &&  php think scan vulmapPocTest>> /tmp/vulmapPocTest.txt & ', 0, 'vulmap漏洞扫描POC测试', NULL);
INSERT INTO `process_safe` VALUES (39, 'scan dismapScan', 'cd /root/qingscan/think  &&  php think scan dismapScan>> /tmp/dismapScan.txt & ', 0, 'dismap指纹识别', NULL);
INSERT INTO `process_safe` VALUES (40, 'scan plugin_safe', 'cd /root/qingscan/think  &&  php think scan plugin_safe>> /tmp/plugin_safe.txt & ', 0, '自定义工具守护', '2021-12-16 14:51:52');
INSERT INTO `process_safe` VALUES (41, 'scan crawlergoScan', 'cd /root/qingscan/think  &&  php think scan crawlergoScan>> /tmp/crawlergoScan.txt & ', 0, 'crawlergo爬虫URL收集', NULL);

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
                         `vulmap_scan_time` datetime(0) NULL DEFAULT '2000-01-01 00:00:00',
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
INSERT INTO `user` VALUES (1, 'test', 'ed04f8ec326fa29e2ebb413729fc92d2', '', '测试', 8, 1635494087, '', 0, 1, 1638004150, 0, 0, '', '', '', '1ca4725c34758183af3fd1f723f07a31', '');
INSERT INTO `user` VALUES (2, 'test1', 'fd5ff2881a30c41fe72a3c04d23db614', '', '测试1', 8, 1635494087, '', 0, 1, 1637157187, 0, 1, '15100000000', 'dfsdfsdfsd', 'admin@admin.com', '', '');
INSERT INTO `user` VALUES (3, 'lj', 'fd5ff2881a30c41fe72a3c04d23db614', '', '辣鸡321', 5, 1635494087, '', 0, 0, 1636115005, 0, 1, '', '', '', '', '');

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
                               PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2070 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of vulnerable
-- ----------------------------
INSERT INTO `vulnerable` VALUES (31, '2', 'Apache Flink 任意文件读取漏洞', '360-202106-443814', 'CVE-2020-17519', NULL, 'CNNVD-202101-271', 'ssvid-99093(Seebug)', '高危', '任意文件读取', 'CWE-552', '7.5', 'CVSS:3.1/AV:N/AC:L/PR:N/UI:N/S:U/C:H/I:N/A:N', '2021-01-05T00:15:00.000Z', '2021-01-05T03:13:00.000Z', 'https://nvd.nist.gov/vuln/detail/CVE-2020-17519', '检查当前Apache Flink版本是否为1.11.0-1.11.2之间，如果是，请立即从官网下载并安装Flink 1.11.3或1.12.0', NULL, '检查当前Apache Flink版本是否为1.11.0-1.11.2之间，如果是，请立即从官网下载并安装Flink 1.11.3或1.12.0', NULL, 'https://www.apache.org/dyn/closer.lua/flink/flink-1.13.2/flink-1.13.2-bin-scala_2.11.tgz', '$ bin/flink stop [--savepointPath :savepointPath] :jobId\n$ bin/flink run -s :savepointPath [:runArgs]', 'cpe:2.3:a:apache:flink:*:*:*:*:*:*:*:*', 'Apache Flink', '是', 'Apache Software Foundation', 'https://apache.org/', 'Apache Flink', '1.11.0, 1.11.1, 1.11.2', '2020-07-05T16:00:00.000Z', 'https://flink.apache.org/downloads.html', 'https://github.com/apache/flink', 'fe3613574f76201a8d55d572a639a4ce7e18a9db', 'b561010b0ee741543c3953306037f00d7a9f0801', '软件', '电力', 'WEB应用程序', '2,382', 'app=\"Apache-Flink\"', '2021-09-15 10:11:45', '2021-09-15 10:11:45', 13, 1, '004滕佑标', 1, NULL, NULL, NULL, '092肖磊', NULL, '2000-01-01 00:00:00', 1, 0);
INSERT INTO `vulnerable` VALUES (36, '2', 'Atlassian Jira 未授权SSRF漏洞验证', '360-202106-021596', 'CVE-2019-8451', NULL, 'CNNVD-201909-556', NULL, '中危', 'SSRF', 'CWE-918', '6.5', 'CVSS:3.1/AV:N/AC:L/PR:N/UI:N/S:U/C:L/I:L/A:N', '2019-08-11T18:44:00.000Z', '2020-04-30T02:47:00.000Z', 'https://nvd.nist.gov/vuln/detail/CVE-2019-8451', '从官方提供的下载页面[Update Jira Software Server | Atlassian](https://www.atlassian.com/software/jira/update)中，下载并安装大于8.4.0版本的安装包。', NULL, '从官方提供的下载页面[Update Jira Software Server | Atlassian](https://www.atlassian.com/software/jira/update)中，下载并安装大于8.4.0版本的安装包。', NULL, 'https://www.atlassian.com/software/jira/download/data-center', '从官方提供的下载页面[Update Jira Software Server | Atlassian](https://www.atlassian.com/software/jira/update)中，下载并安装大于8.4.0版本的安装包。', 'cpe:2.3:a:atlassian:jira:*:*:*:*:*:*:*:*', 'Atlassian', '否', 'Atlassian', 'Atlassian', 'jira', '7.6.0-8.4.0', '2003-05-31T16:00:00.000Z', 'https://www.atlassian.com/software/jira', NULL, NULL, NULL, '软件', '证券', 'WEB应用程序', '82,582', 'app=\"jira\"', '2021-09-02 10:05:59', '2021-09-02 10:05:59', 15, 1, '006石丰赫', 1, NULL, NULL, NULL, 'admin', NULL, '2000-01-01 00:00:00', 0, 0);
INSERT INTO `vulnerable` VALUES (37, '2', 'Atlassian Crowd 远程命令执行漏洞', '360-202106-237675', 'CVE-2019-11580', NULL, 'CNNVD-201905-1031', 'SSV-98016(Seebug)', '严重', '远程代码执行(RCE)', 'CWE-74', '9.8', 'CVSS:3.0/AV:N/AC:L/PR:N/UI:N/S:U/C:H/I:H/A:H', '2019-05-05T20:06:00.000Z', '2021-02-16T21:14:00.000Z', 'https://nvd.nist.gov/vuln/detail/CVE-2019-11580', '可选择以下步骤进行临时防护：\n1.关闭Crowd\n2.查找并删除Crowd的安装目录和数据目录下的所有pdkinstall-plugin jar文件\n3.将Crowd安装目录下的crowd-webapp/WEB-INF/classes/atlassian-bundled-plugins.zip中的pdkinstall-plugin jar文件移除\n      a.先将atlassian-bundled-plugins.zip解压\n      b.删除 pdkinstall-plugin-0.4.jar\n      c.将所以文件重新打包会zip文件\n4.启动Crowd\n5.查看Crowd的安装目录和数据目录下是否还有 pdkinstall-plugin jar文件', NULL, '查看Crowd版本，参照https://confluence.atlassian.com/crowd/crowd-security-advisory-2019-05-22-970260700.html中What You Need to Do的版本对应表进行更新操作。', NULL, 'https://confluence.atlassian.com/crowd/crowd-security-advisory-2019-05-22-970260700.html', '1.从https://www.atlassian.com/software/crowd/download下载3.4.4版本的Crowd和Crowd Data Center，使用自带漏洞修复程序进行修复。\n 2.从https://www.atlassian.com/software/crowd/download-archive下载3.0.5, 3.1.6, 3.2.8和3.3.5版本的Crowd和Crowd Data Center,使用自带漏洞修复程序进行修复。', 'cpe:2.3:a:atlassian:crowd:*:*:*:*:*:*:*:*', 'Crowd', '否', 'Atlassian', 'https://www.atlassian.com/software/crowd', 'Crowd', '2.1.0 <= && < 3.0.5|| 3.1.0 <= && < 3.1.6 || 3.2.0 <=&& < 3.2.8 ||3.3.0 <= && < 3.3.5|| 3.4.0 <= && < 3.4.4', NULL, 'https://www.atlassian.com/software/crowd', NULL, NULL, NULL, '软件', '银行', 'WEB应用程序', '194283', '\"Crowd\"', '2021-09-29 15:02:14', '2021-09-29 15:02:14', 15, 1, '006石丰赫', 1, NULL, NULL, NULL, 'admin', NULL, '2000-01-01 00:00:00', 0, 0);
INSERT INTO `vulnerable` VALUES (38, '2', 'Atlassian Jira 远程命令执行漏洞', '360-202106-203123', 'CVE-2019-11581', NULL, 'CNNVD-201907-701', 'SSV-98021(SeeBug)', '严重', '远程命令执行(RCE)', 'CWE-74', '9.8', 'CVSS:3.0/AV:N/AC:L/PR:N/UI:N/S:U/C:H/I:H/A:H', '2019-07-09T16:00:00.000Z', '2019-09-19T16:00:00.000Z', 'https://nvd.nist.gov/vuln/detail/CVE-2019-11581', '1.禁止访问http://ip:port/secure/ContactAdministrators!default.jspa\n2.关闭联系网站管理员表单功能，具体步骤如下:\n设置 => 系统 => 编辑设置 => 联系管理员表单处选择“关”，然后点击最下面的“更新”保存设置。', NULL, '从https://www.atlassian.com/software/jira/download/data-center，下载安装包，升级到8.2.4等不受漏洞影响的版本。', NULL, 'https://confluence.atlassian.com/jira/jira-security-advisory-2019-07-10-973486595.html', NULL, 'cpe:2.3:a:atlassian:jira:*:*:*:*:*:*:*:*', 'atlassian jira', '否', 'atlassian', 'jira.atlassian.com', 'jira', '4.4.x     5.x.x     6.x.x     7.0.x     7.1.x     7.2.x     7.3.x     7.4.x     7.5.x     7.6.x before 7.6.14 (the fixed version for 7.6.x)     7.7.x     7.8.x     7.9.x     7.10.x     7.11.x     7.12.x', '2003-05-31T16:00:00.000Z', 'https://www.atlassian.com/software/jira', NULL, NULL, NULL, '软件', '民航', 'WEB应用程序', '82,581', 'app=\"jira\"', '2021-09-29 15:03:09', '2021-09-29 15:03:09', 15, 1, '006石丰赫', 1, NULL, NULL, NULL, 'admin', NULL, '2000-01-01 00:00:00', 0, 0);
INSERT INTO `vulnerable` VALUES (39, '2', 'Atlassian Jira 敏感信息泄漏漏洞', '360-202106-498258', 'CVE-2020-14181', NULL, 'CNNVD-202009-1072', 'SSV-98400(SeeBug)', '中危', '信息泄露', 'CWE-200', '5.3', 'CVSS:3.1/AV:N/AC:L/PR:N/UI:N/S:U/C:L/I:N/A:N', '2020-09-15T19:13:00.000Z', '2021-03-24T04:57:00.000Z', 'https://nvd.nist.gov/vuln/detail/CVE-2020-14181', '请从官方下载页面(https://www.atlassian.com/software/jira/update)，选择8.12.0, 7.13.16, 8.5.7等一些不受漏洞影响的版本进行更新。', NULL, '请从官方下载页面(https://www.atlassian.com/software/jira/update)，选择8.12.0, 7.13.16, 8.5.7等一些不受漏洞影响的版本进行更新。', NULL, 'https://jira.atlassian.com/browse/JRASERVER-71560', '从https://confluence.atlassian.com/enterprise/atlassian-enterprise-releases-948227420.html，下载安装已修复漏洞的版本。', 'cpe:2.3:a:atlassian:data_center:*:*:*:*:*:*:*:*', 'Atlassia jira', '否', 'Atlassian', 'https://www.atlassian.com/', 'jira', 'version < 7.13.16 8.0.0 ≤ version < 8.5.7 8.6.0 ≤ version < 8.12.0', '2003-05-31T16:00:00.000Z', 'https://www.atlassian.com/software/jira', NULL, NULL, NULL, '软件', '公积金', 'WEB应用程序', '82,446', 'app=\"jira\"', '2021-09-14 15:14:51', '2021-09-14 15:14:51', 15, 1, '006石丰赫', 1, NULL, NULL, NULL, 'admin', NULL, '2000-01-01 00:00:00', 0, 0);
INSERT INTO `vulnerable` VALUES (40, '2', 'ActiveMQ 信息泄漏漏洞', '360-202106-314315', 'CVE-2017-15709', 'CNVD-2018-04495', 'CNNVD-201710-1017', NULL, '低危', '信息泄露', 'CWE-200', '3.7', 'CVSS:3.0/AV:N/AC:H/PR:N/UI:N/S:U/C:L/I:N/A:N', '2018-02-13T04:06:47.000Z', '2019-10-16T16:00:00.000Z', 'https://nvd.nist.gov/vuln/detail/CVE-2017-15709', '1.ActiveMQs升级到最新版本\n2.限制外部目录权限\n3.设置连接ActiveMQ的用户名和密码', NULL, '1.ActiveMQs升级到最新版本\n2.限制外部目录权限\n3.设置连接ActiveMQ的用户名和密码', NULL, 'http://activemq.apache.org/security-advisories.data/CVE-2017-15709-announcement.txt', '使用支持 TLS 的传输或升级到 Apache ActiveMQ 5.15.3', 'cpe:2.3:a:apache:activemq:*:*:*:*:*:*:*:*', 'Apache ActiveMQ', '是', 'Apache', 'https://www.apache.org/', 'ActiveMQ', '>=5.14.0&&<=5.15.2', '2017-06-27T05:48:00.000Z', 'https://cwiki.apache.org/confluence/display/ACTIVEMQ/ActiveMQ+5.14.0+Release', 'https://github.com/apache/activemq/commit/71cbc652835e8b6b9d17b7a28fc998fce4840a9b', '71cbc652835e8b6b9d17b7a28fc998fce4840a9b', '3e07d459c40f044d5b18f592ebc448f2e677f1da', '软件', '银行', 'WEB应用程序', '11775', 'app=\"APACHE-ActiveMQ\"', '2021-10-14 15:20:52', '2021-10-14 15:20:52', 14, 1, '1', 1, '1', '1', '1', '1', '1', '2000-01-01 00:00:00', 0, 0);
INSERT INTO `vulnerable` VALUES (41, '2', 'Struts2 Jakarta plugin 远程代码执行漏洞', '360-202106-439109', 'CVE-2017-5638', 'CNVD-2017-02474', 'CNNVD-201703-152', 'SSV-92746(seebug)', '高危', '远程代码执行(RCE)', 'CWE-20', '10.0', 'CVSS:3.1/AV:N/AC:L/PR:N/UI:N/S:C/C:H/I:H/A:H', '2017-03-06T16:00:00.000Z', '2017-03-18T16:00:00.000Z', 'https://nvd.nist.gov/vuln/detail/CVE-2017-5638', '修改struts2组件中的default.properties文件，将struts.multipart.parser的值由jakarta更改为pell', NULL, '可参照(https://cwiki.apache.org/confluence/display/WW/Version+Notes+2.3.32)更新至2.3.32版本。\n可参照(https://cwiki.apache.org/confluence/display/WW/Version+Notes+2.5.10.1)更新至2.5.10.1版本。', NULL, 'https://cwiki.apache.org/confluence/display/WW/S2-045', '可参照(https://cwiki.apache.org/confluence/display/WW/Version+Notes+2.3.32)更新至2.3.32版本。\n可参照(https://cwiki.apache.org/confluence/display/WW/Version+Notes+2.5.10.1)更新至2.5.10.1版本。', 'cpe:2.3:a:apache:struts:2.3.5:*:*:*:*:*:*:*', 'struts2', '是', 'Apache软件基金会', 'https://www.apache.org/', 'Jakarta plugin', '>=2.3.5&&<=2.3.31||>=2.5&&<=2.5.10', '2012-10-04T16:00:00.000Z', 'https://cwiki.apache.org/confluence/display/WW/S2-045', 'https://github.com/apache/struts/tree/STRUTS_2_3_32', 'a41f1118fd838bfcac024e94711c8846fcfe87ef', '1ed29d508fc0a3762ad7d16336a71adcf69bd88d', '服务', '银行', 'WEB应用程序', '5616696', 'app=\"Struts2\"', '2021-09-29 15:02:14', '2021-09-29 15:02:14', 24, 1, '1', 1, '1', '1', '1', '1', '1', '2000-01-01 00:00:00', 0, 0);
INSERT INTO `vulnerable` VALUES (42, '2', 'Struts2 showcase 远程命令执行漏洞', '360-202106-024978', 'CVE-2017-9791', NULL, 'CNNVD-201706-928', 'SSV-96270(seebug)', '高危', '远程命令执行(RCE)', 'CWE-20', '7.5', 'CVSS:3.1/AV:N/AC:L/PR:N/UI:N/S:U/C:H/I:H/A:H', '2017-07-06T16:00:00.000Z', '2017-07-09T16:00:00.000Z', 'https://nvd.nist.gov/vuln/detail/CVE-2017-9791', '开发者将消息传递给 ActionMessage 方式时，请使用如下方法： messages.add(\"msg\", new ActionMess age(\"struts1.gangsterAdded\", gform.getName())); 务必不要使用下列方法： messages.add(\"msg\", new ActionMessage(\"Gangster \" + gform.getName() + \" was added\"));', NULL, '开发者将消息传递给 ActionMessage 方式时，请使用如下方法： messages.add(\"msg\", new ActionMess age(\"struts1.gangsterAdded\", gform.getName())); 务必不要使用下列方法： messages.add(\"msg\", new ActionMessage(\"Gangster \" + gform.getName() + \" was added\"));', NULL, 'http://struts.apache.org/docs/s2-048.html', '开发者将消息传递给 ActionMessage 方式时，请使用如下方法： messages.add(\"msg\", new ActionMess age(\"struts1.gangsterAdded\", gform.getName())); 务必不要使用下列方法： messages.add(\"msg\", new ActionMessage(\"Gangster \" + gform.getName() + \" was added\"));', 'cpe:2.3:a:apache:struts:2.3.1:*:*:*:*:*:*:*', 'struts2', '是', 'Apache软件基金会', 'https://www.apache.org/', 'showcase', '2.3.x', '2011-12-08T16:00:00.000Z', 'https://cwiki.apache.org/confluence/display/WW/S2-048', 'https://cwiki.apache.org/confluence/display/WW/S2-048', 'e5009a34202777b53c5dc36e020d0033aa8be027', '376a891aeed6157d5621f9a9101d91be60f57b01', '服务', '银行', 'WEB应用程序', '5616696', 'app=\"Struts2\"', '2021-09-29 15:03:09', '2021-09-29 15:03:09', 24, 1, '1', 1, '1', '1', '1', '1', '1', '2000-01-01 00:00:00', 0, 0);
INSERT INTO `vulnerable` VALUES (43, '2', 'Apache FreeMarker模板FusionAuth远程代码执行漏洞', '360-202106-117330', 'CVE-2020-7799', NULL, 'CNNVD-202001-1238', 'ssvid-98128(seebug)', '高危', '远程代码执行(RCE)', 'CWE-917', '7.2', 'CVSS:3.1/AV:N/AC:L/PR:H/UI:N/S:U/C:H/I:H/A:H', '2019-10-22T16:00:00.000Z', '2019-10-28T16:00:00.000Z', 'https://nvd.nist.gov/vuln/detail/CVE-2020-7799', '请从(https://files.fusionauth.io/products/fusionauth/1.11.0/fusionauth-app-1.11.0.zip)下载1.11.0版本的安装包，并参照https://fusionauth.io/docs/v1/tech/installation-guide/upgrade/进行安装。', NULL, '请从(https://files.fusionauth.io/products/fusionauth/1.11.0/fusionauth-app-1.11.0.zip)下载1.11.0版本的安装包，并参照https://fusionauth.io/docs/v1/tech/installation-guide/upgrade/进行安装。', NULL, 'https://files.fusionauth.io/products/fusionauth/1.11.0/fusionauth-app-1.11.0.zip', '请从(https://files.fusionauth.io/products/fusionauth/1.11.0/fusionauth-app-1.11.0.zip)下载1.11.0版本的安装包，并参照https://fusionauth.io/docs/v1/tech/installation-guide/upgrade/进行安装。', 'cpe:2.3:a:fusionauth:fusionauth:*:*:*:*:*:*:*:*', 'FusionAuth', '是', 'Apache FreeMarker', 'https://fusionauth.io/', 'Apache fusionauth', '<1.11.0', '2019-10-28T16:00:00.000Z', 'https://files.fusionauth.io/products/fusionauth/1.11.0/fusionauth-app-1.11.0.zip', 'https://github.com/FusionAuth/fusionauth-containers', '311415b69dbe92778d5cffd956f1ffbe16923dde', 'bf139b3a2290bc9545775c9eb6e714255304cbb6', '软件', '电力', '网络协议', '1,076', '\"fusionauth\"', '2021-09-29 15:02:14', '2021-09-29 15:02:14', 13, 1, '1', 1, '1', '1', '1', '1', '1', '2000-01-01 00:00:00', 0, 0);

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

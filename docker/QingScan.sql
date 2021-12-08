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
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `un_url`(`url`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2083 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = Compact;

-- ----------------------------
-- Records of app
-- ----------------------------

-- ----------------------------
-- Table structure for app_dirmap
-- ----------------------------
DROP TABLE IF EXISTS `app_dirmap`;
CREATE TABLE `app_dirmap`  (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `app_id` int(10) NOT NULL,
  `code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `size` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `create_time` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  `user_id` int(10) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = Compact;

-- ----------------------------
-- Records of app_dirmap
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
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = Compact;

-- ----------------------------
-- Records of app_whatweb
-- ----------------------------

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
) ENGINE = MyISAM AUTO_INCREMENT = 61 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '权限列表' ROW_FORMAT = Dynamic;

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
INSERT INTO `auth_rule` VALUES (20, 'vulnerable/pocsuite', 'POC列表', 0, 1, 5, 2, 1635847469, 1, 0, 2, 0, '');
INSERT INTO `auth_rule` VALUES (21, 'vulnerable/pocsuite', '漏洞列表', 0, 1, 5, 3, 1635847483, 1, 0, 2, 0, '');
INSERT INTO `auth_rule` VALUES (22, 'code/info', '代码指纹', 0, 1, 14, 5, 1635862942, 1, 0, 2, 0, '');
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
INSERT INTO `auth_rule` VALUES (34, 'pocs_file/index', 'POC测试', 0, 1, 5, 4, 1637049514, 1, 0, 2, 0, '');
INSERT INTO `auth_rule` VALUES (35, '', '信息收集', 0, 1, 0, 2, 0, 1, 0, 1, 0, '');
INSERT INTO `auth_rule` VALUES (36, 'host/index', '主机列表', 0, 1, 35, 6, 0, 1, 0, 2, 0, '');
INSERT INTO `auth_rule` VALUES (37, 'log/index', '日志管理', 0, 1, 23, 3, 1637155116, 1, 0, 2, 0, '');
INSERT INTO `auth_rule` VALUES (38, 'code_python/index', 'python依赖库', 0, 1, 8, 5, 1637244411, 1, 0, 2, 0, '');
INSERT INTO `auth_rule` VALUES (39, 'code_composer/index', 'composer列表', 0, 1, 14, 6, 1637302940, 1, 1637303558, 2, 0, '');
INSERT INTO `auth_rule` VALUES (40, 'code_java/index', 'java依赖库', 0, 1, 14, 7, 1637308555, 1, 0, 2, 0, '');
INSERT INTO `auth_rule` VALUES (41, 'proxy/index', '代理列表', 0, 1, 23, 4, 1637911963, 1, 0, 2, 0, '');
INSERT INTO `auth_rule` VALUES (42, 'node/index', '节点列表', 0, 1, 23, 5, 1638105532, 1, 1638105795, 2, 0, '');
INSERT INTO `auth_rule` VALUES (43, '', '未知', 0, 0, 7, 0, 1638342233, 0, 1638459602, 2, 0, '');
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
  `private_key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `is_delete` tinyint(1) NOT NULL DEFAULT 0,
  `composer_scan_time` datetime(0) NOT NULL DEFAULT '2000-01-01 00:00:00',
  `java_scan_time` datetime(0) NOT NULL DEFAULT '2000-01-01 00:00:00',
  `python_scan_time` datetime(0) NOT NULL DEFAULT '2000-01-01 00:00:00',
  `user_id` int(10) NOT NULL DEFAULT 0,
  `star` char(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
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
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `create_time` datetime(0) NULL DEFAULT '2000-01-01 00:00:00',
  `update_time` datetime(0) NULL DEFAULT '2000-01-01 00:00:00',
  `scan_time` datetime(0) NOT NULL DEFAULT '2000-01-01 00:00:00',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = Compact;

-- ----------------------------
-- Records of github_keyword_monitor
-- ----------------------------

-- ----------------------------
-- Table structure for github_keyword_monitor_notice
-- ----------------------------
DROP TABLE IF EXISTS `github_keyword_monitor_notice`;
CREATE TABLE `github_keyword_monitor_notice`  (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) NOT NULL DEFAULT 0,
  `user_id` int(10) NOT NULL DEFAULT 0,
  `keyword` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `html_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `create_time` datetime(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = Compact;

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
  `references` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
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
  `country` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `region` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `city` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `area` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
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
  `type` char(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT 'ssh',
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
-- Table structure for pocs_file
-- ----------------------------
DROP TABLE IF EXISTS `pocs_file`;
CREATE TABLE `pocs_file`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cve_num` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `poc_file` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
  `create_time` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT '创建时间',
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `filename` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `is_yaml` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `cve_poc`(`cve_num`, `poc_file`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

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
  `note` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 29 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = Compact;

-- ----------------------------
-- Records of process_safe
-- ----------------------------
INSERT INTO `process_safe` VALUES (2, 'scan xray', 'cd /root/qingscan/think  &&  php think scan xray  >> /tmp/xray.txt & ', 0, '');
INSERT INTO `process_safe` VALUES (3, 'scan awvs', 'cd /root/qingscan/think  &&  php think scan awvs  >> /tmp/awvs.txt & ', 0, '');
INSERT INTO `process_safe` VALUES (4, 'scan rad', 'cd /root/qingscan/think  &&  php think scan rad  >> /tmp/rad.txt & ', 0, '');
INSERT INTO `process_safe` VALUES (5, 'scan host', 'cd /root/qingscan/think  &&  php think scan host  >> /tmp/host.txt & ', 1, '');
INSERT INTO `process_safe` VALUES (6, 'scan port', 'cd /root/qingscan/think  &&  php think scan port  >> /tmp/port.txt & ', 1, '');
INSERT INTO `process_safe` VALUES (7, 'scan nmap', 'cd /root/qingscan/think  &&  php think scan nmap  >> /tmp/nmap.txt & ', 0, '');
INSERT INTO `process_safe` VALUES (8, 'scan fortify', 'cd /root/qingscan/think  &&  php think scan fortify  >> /tmp/fortify.txt & ', 0, '');
INSERT INTO `process_safe` VALUES (9, 'scan kunlun', 'cd /root/qingscan/think  &&  php think scan kunlun  >> /tmp/kunlun.txt & ', 1, '');
INSERT INTO `process_safe` VALUES (10, 'scan semgrep', 'cd /root/qingscan/think  &&  php think scan semgrep  >> /tmp/semgrep.txt & ', 1, '');
INSERT INTO `process_safe` VALUES (11, 'think run', 'cd /root/qingscan/think  &&  php think run  >> /tmp/run.txt & ', 0, '');
INSERT INTO `process_safe` VALUES (12, 'scan kafka', 'cd /root/qingscan/think  &&  php think scan kafka  >> /tmp/kafka.txt & ', 0, '');
INSERT INTO `process_safe` VALUES (13, 'scan google', 'cd /root/qingscan/think  &&  php think scan google >> /tmp/google.txt & ', 1, NULL);
INSERT INTO `process_safe` VALUES (14, 'scan upadteRegion', 'cd /root/qingscan/think  &&  php think scan upadteRegion >> /tmp/upadteRegion.txt & ', 1, NULL);
INSERT INTO `process_safe` VALUES (15, 'scan whatweb', 'cd /root/qingscan/think  &&  php think scan whatweb >> /tmp/whatweb.txt & ', 1, NULL);
INSERT INTO `process_safe` VALUES (16, 'scan subdomainScan', 'cd /root/qingscan/think  &&  php think scan subdomainScan >> /tmp/subdomainScan.txt & ', 0, NULL);
INSERT INTO `process_safe` VALUES (17, 'scan hydra', 'cd /root/qingscan/think  &&  php think scan hydra >> /tmp/hydra.txt & ', 0, NULL);
INSERT INTO `process_safe` VALUES (18, 'scan sqlmapScan', 'cd /root/qingscan/think  &&  php think scan sqlmapScan >> /tmp/sqlmapScan.txt & ', 0, NULL);
INSERT INTO `process_safe` VALUES (20, 'scan fofa', 'cd /root/qingscan/think  &&  php think scan fofa >> /tmp/fofa.txt & ', 0, NULL);
INSERT INTO `process_safe` VALUES (21, 'scan dirmapScan', 'cd /root/qingscan/think  &&  php think scan dirmapScan >> /tmp/dirmapScan.txt & ', 0, NULL);
INSERT INTO `process_safe` VALUES (22, 'scan getNotice', 'cd /root/qingscan/think  &&  php think scan getNotice >> /tmp/getNotice.txt & ', 0, '');
INSERT INTO `process_safe` VALUES (23, 'scan backup', 'cd /root/qingscan/think  &&  php think scan backup>> /tmp/backup.txt & ', 1, '数据库备份');
INSERT INTO `process_safe` VALUES (24, 'scan getProjectComposer', 'cd /root/qingscan/think  &&  php think scan getProjectComposer>> /tmp/composer.txt & ', 1, '获取composer组件');
INSERT INTO `process_safe` VALUES (25, 'scan code_python', 'cd /root/qingscan/think  &&  php think scan code_python>> /tmp/code_python.txt & ', 1, '获取python组件');
INSERT INTO `process_safe` VALUES (26, 'scan code_java', 'cd /root/qingscan/think  &&  php think scan code_java>> /tmp/code_java.txt & ', 1, '获取java组件');
INSERT INTO `process_safe` VALUES (27, 'scan giteeProject', 'cd /root/qingscan/think  &&  php think scan giteeProject>> /tmp/giteeProject.txt & ', 1, '获取码云项目');
INSERT INTO `process_safe` VALUES (28, 'scan freeAgent', 'cd /root/qingscan/think  &&  php think scan freeAgent>> /tmp/freeAgent.txt & ', 1, '获取免费代理');

-- ----------------------------
-- Table structure for proxy
-- ----------------------------
DROP TABLE IF EXISTS `proxy`;
CREATE TABLE `proxy`  (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `host` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `port` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `status` int(4) NOT NULL DEFAULT 1 COMMENT '1 有效  0 无效',
  `create_time` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = Compact;

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
  `end_line` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
  `end_offset` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
  `extra_is_ignored` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
  `extra_lines` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
  `extra_message` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
  `extra_metadata` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
  `extra_metavars` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
  `extra_severity` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
  `path` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
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
  `key` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
  `name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
  `value` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
  `is_delete` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = Compact;

-- ----------------------------
-- Records of system_config
-- ----------------------------

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
-- Table structure for tp_user
-- ----------------------------
DROP TABLE IF EXISTS `tp_user`;
CREATE TABLE `tp_user`  (
  `id` mediumint(8) NOT NULL AUTO_INCREMENT,
  `username` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` char(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `gender` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '男',
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` tinyint(1) NOT NULL,
  `create_time` datetime(0) NOT NULL DEFAULT '1970-01-01 00:00:00',
  `update_time` datetime(0) NOT NULL DEFAULT '1970-01-01 00:00:00',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `username`(`username`) USING BTREE,
  UNIQUE INDEX `email`(`email`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 19 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of tp_user
-- ----------------------------
INSERT INTO `tp_user` VALUES (1, '小新', '123456', '男', '123456@qq.com', 1, '1970-01-01 00:00:00', '1970-01-01 00:00:00');
INSERT INTO `tp_user` VALUES (2, '大头', '123', '男', '123@qq.com', 2, '1970-01-01 00:00:00', '1970-01-01 00:00:00');
INSERT INTO `tp_user` VALUES (3, '李丽丽', '12345', '女', '12345@qq.com', 2, '1970-01-01 00:00:00', '1970-01-01 00:00:00');
INSERT INTO `tp_user` VALUES (4, '咪咪', '321', '女', '321@qq.com', 1, '1970-01-01 00:00:00', '1970-01-01 00:00:00');
INSERT INTO `tp_user` VALUES (5, '小热', '543', '女', '543@qq.com', 1, '1970-01-01 00:00:00', '1970-01-01 00:00:00');
INSERT INTO `tp_user` VALUES (6, '大雄', '654', '男', '654@qq.com', 2, '1970-01-01 00:00:00', '1970-01-01 00:00:00');
INSERT INTO `tp_user` VALUES (8, '小新1', '123456', '男', '1234561@qq.com', 1, '1970-01-01 00:00:00', '1970-01-01 00:00:00');
INSERT INTO `tp_user` VALUES (9, '大头1', '123', '男', '1232@qq.com', 2, '1970-01-01 00:00:00', '1970-01-01 00:00:00');
INSERT INTO `tp_user` VALUES (10, '李丽丽1', '12345', '女', '123453@qq.com', 2, '1970-01-01 00:00:00', '1970-01-01 00:00:00');
INSERT INTO `tp_user` VALUES (11, '咪咪1', '321', '女', '3231@qq.com', 1, '1970-01-01 00:00:00', '1970-01-01 00:00:00');
INSERT INTO `tp_user` VALUES (12, '小热1', '543', '女', '5433@qq.com', 1, '1970-01-01 00:00:00', '1970-01-01 00:00:00');
INSERT INTO `tp_user` VALUES (13, '大雄1', '654', '男', '6543@qq.com', 2, '1970-01-01 00:00:00', '1970-01-01 00:00:00');
INSERT INTO `tp_user` VALUES (15, '小新12', '123456', '男', '12345613@qq.com', 1, '1970-01-01 00:00:00', '1970-01-01 00:00:00');
INSERT INTO `tp_user` VALUES (16, '大头12', '123', '男', '12332@qq.com', 2, '1970-01-01 00:00:00', '1970-01-01 00:00:00');
INSERT INTO `tp_user` VALUES (17, '李丽丽12', '12345', '女', '1234353@qq.com', 2, '1970-01-01 00:00:00', '1970-01-01 00:00:00');
INSERT INTO `tp_user` VALUES (18, '咪咪12', '321', '女', '32331@qq.com', 1, '1970-01-01 00:00:00', '1970-01-01 00:00:00');

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
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = Compact;

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
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
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
  `created_at` int(10) NOT NULL DEFAULT 0,
  `last_login_ip` char(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '最后登录ip地址',
  `last_login_time` int(10) NOT NULL DEFAULT 0 COMMENT '最后登陆时间',
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '状态 1正常  2禁用',
  `update_time` int(10) NOT NULL DEFAULT 0 COMMENT '修改时间',
  `is_delete` tinyint(1) NOT NULL DEFAULT 0,
  `sex` tinyint(1) NOT NULL DEFAULT 0,
  `phone` char(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `dd_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `email` char(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `token` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 13 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = Compact;

-- ----------------------------
-- Records of user
-- ----------------------------
INSERT INTO `user` VALUES (1, 'test', 'ed04f8ec326fa29e2ebb413729fc92d2', '', '测试', 8, 1635494087, '', 0, 1, 1638004150, 0, 0, '', '', '', '1ca4725c34758183af3fd1f723f07a31', '');
INSERT INTO `user` VALUES (2, 'test1', 'fd5ff2881a30c41fe72a3c04d23db614', '', '测试1', 8, 1635494087, '', 0, 1, 1637157187, 0, 1, '15100000000', 'dfsdfsdfsd', 'admin@admin.com', '', '');


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
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

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
  `check_status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '审核状态x',
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

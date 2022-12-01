/*
 Navicat Premium Data Transfer

 Source Server         : localhost_33336
 Source Server Type    : MySQL
 Source Server Version : 50736
 Source Host           : localhost:33336
 Source Schema         : QingScan

 Target Server Type    : MySQL
 Target Server Version : 50736
 File Encoding         : 65001

 Date: 01/12/2022 18:17:21
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
  `create_time` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin COMMENT = 'url收集' ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of app_crawlergo
-- ----------------------------

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
  `create_time` datetime NULL DEFAULT '2000-01-01 00:00:00',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = COMPACT;

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
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = COMPACT;

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
  `create_time` datetime NULL DEFAULT NULL,
  `curl_command` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 106 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of app_nuclei
-- ----------------------------

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
  `create_time` datetime NULL DEFAULT NULL,
  `url` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `detected` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `firewall` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'waf防火墙名称',
  `manufacturer` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '厂商',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin COMMENT = 'waf指纹识别' ROW_FORMAT = COMPACT;

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
  `create_time` datetime NULL DEFAULT '2000-01-01 00:00:00',
  `poc_scan_time` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
  `user_id` int(10) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin COMMENT = 'web指纹识别' ROW_FORMAT = COMPACT;

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
  `create_time` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
  `user_id` int(10) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = COMPACT;

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
  `create_time` datetime NULL DEFAULT '2000-01-01 00:00:00',
  `start_up` int(1) NOT NULL DEFAULT 0 COMMENT '是否已启动',
  `is_get_result` int(1) NOT NULL DEFAULT 0 COMMENT '是否已获取结果',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin COMMENT = '本地代理' ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of app_xray_agent_port
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
) ENGINE = MyISAM AUTO_INCREMENT = 10 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of auth_group
-- ----------------------------
INSERT INTO `auth_group` VALUES (5, '管理员', 1, '0,7,8,9,10,11,12,13,14,15,16,17,18,22,19,5,6,20,21,1,2,3,4,23,24,', 1635927813, 0, 1635754703);
INSERT INTO `auth_group` VALUES (6, '测试', 1, NULL, 1635755403, 1, 1635754726);
INSERT INTO `auth_group` VALUES (7, '白盒审计', 1, '0,7,43,48,45,14,15,16,17,18,22,19,', 1635927890, 0, 1635754803);
INSERT INTO `auth_group` VALUES (8, '黑盒测试', 1, '0,7,43,44,45,46,47,48,49,51,52,53,54,55,56,58,57,59,60,61,62,63,64,65,66,68,70,71,72,74,75,76,77,79,80,81,82,83,84,85,86,87,89,90,91,92,93,94,95,96,97,98,99,100,101,102,103,104,105,106,107,108,109,110,111,112,113,114,115,116,117,118,119,120,121,122,123,124,125,126,127,128,129,130,131,132,133,134,135,136,137,138,139,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,206,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,248,249,250,251,252,253,254,255,256,257,258,259,260,261,262,263,264,265,266,267,268,269,270,271,272,273,274,275,276,277,278,279,280,281,282,283,284,285,286,287,288,289,290,291,292,293,294,295,296,297,298,299,300,301,302,303,304,305,306,307,308,309,310,311,312,313,314,315,316,317,318,319,320,321,322,323,324,325,326,327,328,329,330,331,332,333,334,335,336,337,338,339,340,341,342,343,344,345,346,347,348,349,350,351,352,353,354,355,356,357,358,359,360,361,362,363,364,365,366,367,368,369,370,371,372,373,374,375,376,377,378,379,380,381,382,383,384,385,386,387,388,389,390,391,392,393,394,395,396,397,398,399,400,401,402,403,404,405,406,407,408,409,410,411,412,413,414,415,416,417,418,419,420,421,422,423,424,425,426,427,428,429,430,431,432,433,434,435,436,437,438,439,440,441,442,443,444,445,446,447,448,449,450,451,452,453,454,455,456,457,458,459,460,461,462,463,464,465,466,467,468,469,470,471,472,473,474,475,476,477,478,479,480,481,482,483,484,485,486,487,488,489,490,491,492,493,494,495,496,497,498,499,500,501,502,503,504,505,506,507,508,509,510,511,512,513,514,515,516,517,518,519,520,521,522,523,524,525,526,527,528,529,530,531,532,533,534,535,536,537,538,539,540,541,542,543,544,545,546,547,548,549,550,551,552,553,554,555,556,557,558,559,560,561,562,563,564,565,566,567,568,569,570,571,572,573,574,575,576,577,578,579,580,581,582,583,584,585,586,587,588,589,590,591,592,593,594,595,596,597,598,599,600,601,602,603,604,605,606,607,608,609,610,611,612,613,614,615,616,617,618,619,620,621,622,623,624,625,626,627,628,629,630,631,632,633,634,635,636,637,638,639,640,641,642,643,644,645,646,647,648,649,650,651,652,653,654,655,656,657,658,659,660,661,662,663,664,665,666,667,668,669,670,671,672,673,674,675,676,677,678,679,680,681,682,683,684,685,686,687,688,689,690,691,692,693,694,695,696,697,698,699,700,701,702,703,704,705,706,707,708,709,710,711,712,713,714,715,716,717,718,719,720,721,722,723,724,725,726,727,728,729,730,731,732,733,734,735,736,737,738,739,740,741,742,743,744,745,746,747,748,749,750,751,752,753,754,755,756,757,758,759,760,761,762,763,764,765,766,767,768,769,770,771,772,773,774,775,776,777,778,779,780,781,782,783,784,785,786,787,788,789,790,791,792,793,794,795,796,797,798,799,800,801,802,803,804,805,806,807,808,809,810,811,812,813,814,815,816,817,818,819,820,821,822,823,824,825,826,827,828,829,830,831,832,833,834,835,836,837,838,839,840,841,842,843,844,845,846,847,848,849,850,851,852,853,854,855,856,857,858,859,860,861,862,863,864,865,866,867,868,869,870,871,872,873,874,875,876,877,878,879,880,881,882,883,884,885,886,887,888,889,890,891,892,893,894,895,896,897,898,899,900,901,902,903,904,905,906,907,908,909,910,911,912,913,914,915,916,917,918,919,920,921,922,923,924,925,926,927,928,929,930,931,932,933,934,935,936,937,938,939,940,941,942,943,944,945,946,947,948,949,950,951,952,953,954,955,956,957,958,959,960,961,962,963,964,965,966,967,968,969,970,971,972,973,974,975,976,977,978,979,980,981,982,983,984,985,986,987,988,989,990,991,992,993,994,995,996,997,998,999,1000,1001,1002,1003,1004,1005,1006,1007,1008,1009,1010,1011,1012,1013,1014,1015,1016,1017,1018,1019,1020,1021,1022,1023,1024,1025,1026,1027,1028,1029,1030,1031,1032,1033,1034,1035,1036,1037,1038,1039,1040,1041,1042,1043,1044,1045,1046,1047,1048,1049,1050,1051,1052,1053,1054,1055,1056,1057,1058,1059,1060,1061,1062,1063,1064,1065,1066,1067,1068,1069,1070,1071,1072,1073,1074,1075,1076,1077,1078,1079,1080,1081,1082,1083,1084,1085,1086,1087,1088,1089,1090,1091,1092,1093,1094,1095,1096,1097,1098,1099,1100,1101,1102,1103,1104,1105,1106,1107,1108,1109,1110,1111,1112,1113,1114,1115,1116,1117,1118,1677,1678,1679,1680,1681,1682,1683,1684,1685,1686,1687,1688,1689,1690,1691,1692,1693,1694,1695,1696,1697,1698,1699,1700,1701,1702,1703,1704,1705,1706,1707,1708,1709,1710,1711,1712,1713,1714,1715,1716,1717,1718,1719,1720,1721,1722,1723,1724,1725,1726,1727,1728,1729,1730,1731,1732,1733,1734,1735,1736,1737,1738,1739,1740,1741,1742,1743,1744,1745,1746,1747,1748,1749,1750,1751,1752,1753,1754,1755,1756,1757,1758,1759,1760,1761,1762,1763,1764,1765,1766,1767,1768,1769,1770,1771,1772,1773,1774,1775,1776,1777,1778,1779,1780,1781,1782,1783,1784,1785,1786,1787,1788,1789,1790,1791,1792,1793,1794,1795,1796,1797,1798,1799,1800,1801,1802,1803,1804,1805,1806,1807,1808,1809,1810,1811,1812,1813,1814,1815,1816,1817,1818,1819,1820,1821,1822,1823,1824,1825,1826,1827,1828,1829,1830,1831,1832,1833,1834,1835,1836,1837,1838,1839,1840,1841,1842,1843,1844,1845,1846,1847,1848,1849,1850,1851,1852,1853,1854,1855,1856,1857,1858,1859,1860,1861,1862,1863,1864,1865,1866,1867,1868,1869,1870,1871,1872,1873,1874,1875,1876,1877,1878,1879,1880,1881,1882,1883,1884,1885,1886,1887,1888,1889,1890,1891,1892,1893,1894,1895,1896,1897,1898,1899,1900,1901,1902,1903,1904,1905,1906,1907,1908,1909,1910,1911,1912,1913,1914,1915,1916,1917,1918,1919,1920,1921,1922,1923,1924,1925,1926,1927,1928,1929,1930,1931,1932,1933,1934,1935,1936,1937,1938,1939,1940,1941,1942,1943,1944,1945,1946,1947,1948,1949,1950,1951,1952,1953,1954,1955,1956,1957,1958,1959,1960,1961,1962,1963,1964,1965,1966,1967,1968,1969,1970,1971,1972,1973,1974,1975,1976,1977,1978,1979,1980,1981,1982,1983,1984,1985,1986,1987,1988,1989,1990,1991,1992,1993,1994,1995,1996,1997,1998,1999,2000,2001,2002,2003,2004,2005,2006,2007,2008,2009,2010,2011,2012,2013,2014,2015,2016,2017,2018,2019,2020,2021,2022,2023,2024,2025,2026,2027,2028,2029,2030,2031,2032,2033,2034,2035,2036,2037,2038,2039,2040,2041,2042,2043,2044,2045,2046,2047,2048,2049,2050,2051,2052,2053,2054,2055,2056,2057,2058,2059,2060,2061,2062,2063,2064,2065,2066,2067,2068,2069,2070,2071,2072,2073,2074,2075,2076,2077,2078,2079,2080,2081,2082,2083,2084,2085,2086,2087,2088,2089,2090,2091,2092,2093,2094,2095,2096,2097,2098,2099,2100,2101,2102,2103,2104,2105,2106,2107,2108,2109,2110,2111,2112,2113,1119,1120,1121,1122,1123,1124,1125,1126,1127,1128,1129,1130,1131,1132,1133,1134,1135,1136,1137,1138,1139,1140,1141,1142,1143,1144,1145,1146,1147,1148,1149,1150,1151,1152,1153,1154,1155,1156,1157,1158,1159,1160,1161,1162,1163,1164,1165,1166,1167,1168,1169,1170,1171,1172,1173,1174,1175,1176,1177,1178,1179,1180,1181,1182,1183,1184,1185,1186,1187,1188,1189,1190,1191,1192,1193,1194,1195,1196,1197,1198,1199,1200,1201,1202,1203,1204,1205,1206,1207,1208,1209,1210,1211,1212,1213,1214,1215,1216,1217,1218,1219,1220,1221,1222,1223,1224,1225,1226,1227,1228,1229,1230,1231,1232,1233,1234,1235,1236,1237,1238,1239,1240,1241,1242,1243,1244,1245,1246,1247,1248,1249,1250,1251,1252,1253,1254,1255,1256,1257,1258,1259,1260,1261,1262,1263,1264,1265,1266,1267,1268,1269,1270,1271,1272,1273,1274,1275,1276,1277,1278,1279,1280,1281,1282,1283,1284,1285,1286,1287,1288,1289,1290,1291,1292,1293,1294,1295,1296,1297,1298,1299,1300,1301,1302,1303,1304,1305,1306,1307,1308,1309,1310,1311,1312,1313,1314,1315,1316,1317,1318,1319,1320,1321,1322,1323,1324,1325,1326,1327,1328,1329,1330,1331,1332,1333,1334,1335,1336,1337,1338,1339,1340,1341,1342,1343,1344,1345,1346,1347,1348,1349,1350,1351,1352,1353,1354,1355,1356,1357,1358,1359,1360,1361,1362,1363,1364,1365,1366,1367,1368,1369,1370,1371,1372,1373,1374,1375,1376,1377,1378,1379,1380,1381,1382,1383,1384,1385,1386,1387,1388,1389,1390,1391,1392,1393,1394,1395,1396,1397,1398,1399,1400,1401,1402,1403,1404,1405,1406,1407,1408,1409,1410,1411,1412,1413,1414,1415,1416,1417,1418,1419,1420,1421,1422,1423,1424,1425,1426,1427,1428,1429,1430,1431,1432,1433,1434,1435,1436,1437,1438,1439,1440,1441,1442,1443,1444,1445,1446,1447,1448,1449,1450,1451,1452,1453,1454,1455,1456,1457,1458,1459,1460,1461,1462,1463,1464,1465,1466,1467,1468,1469,1470,1471,1472,1473,1474,1475,1476,1477,1478,1479,1480,1481,1482,1483,1484,1485,1486,1487,1488,1489,1490,1491,1492,1493,1494,1495,1496,1497,1498,1499,1500,1501,1502,1503,1504,1505,1506,1507,1508,1509,1510,1511,1512,1513,1514,1515,1516,1517,1518,1519,1520,1521,1522,1523,1524,1525,1526,1527,1528,1529,1530,1531,1532,1533,1534,1535,1536,1537,1538,1539,1540,1541,1542,1543,1544,1545,1546,1547,1548,1549,1550,1551,1552,1553,1554,1555,1556,1557,1558,1559,1560,1561,1562,1563,1564,1565,1566,1567,1568,1569,1570,1571,1572,1573,1574,1575,1576,1577,1578,1579,1580,1581,1582,1583,1584,1585,1586,1587,1588,1589,1590,1591,1592,1593,1594,1595,1596,1597,1598,1599,1600,1601,1602,1603,1604,1605,1606,1607,1608,1609,1610,1611,1612,1613,1614,1615,1616,1617,1618,1619,1620,1621,1622,1623,1624,1625,1626,1627,1628,1629,1630,1631,1632,1633,1634,1635,1636,1637,1638,1639,1640,1641,1642,1643,1644,1645,1646,1647,1648,1649,1650,1651,1652,1653,1654,1655,1656,1657,1658,1659,1660,1661,1662,1663,1664,1665,1666,1667,1668,1669,1670,1671,1672,1673,1674,1675,1676,35,27,28,29,30,31,13,36,50,67,78,8,9,10,11,12,32,69,73,88,14,15,16,17,18,19,38,39,40,5,6,34,21,1,2,3,4,23,24,33,37,41,42,25,26,', 1635927865, 0, 1635757244);
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
) ENGINE = MyISAM AUTO_INCREMENT = 337 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '权限列表' ROW_FORMAT = DYNAMIC;

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
INSERT INTO `auth_rule` VALUES (13, 'host_port/index', 'Nmap列表', 0, 1, 35, 5, 1635847285, 1, 1641030666, 2, 0, '');
INSERT INTO `auth_rule` VALUES (14, '', '白盒审计', 0, 1, 0, 4, 1635847305, 1, 1635847336, 1, 0, '');
INSERT INTO `auth_rule` VALUES (15, 'code/index', '项目列表', 0, 1, 14, 1, 1635847323, 1, 0, 2, 0, '');
INSERT INTO `auth_rule` VALUES (16, 'fortify/index', 'Fortify', 0, 1, 14, 2, 1635847369, 1, 0, 2, 0, '');
INSERT INTO `auth_rule` VALUES (17, 'code/kunlun_list', 'KunLun-M', 0, 1, 14, 3, 1635847384, 0, 0, 2, 0, '');
INSERT INTO `auth_rule` VALUES (18, 'semgrep/index', 'SemGrep', 0, 1, 14, 4, 1635847399, 1, 0, 2, 0, '');
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
INSERT INTO `auth_rule` VALUES (62, 'host_port/index', '未知', 1, 1, 43, 0, 1639295357, 1, 0, 3, 1641030643, '');
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
INSERT INTO `auth_rule` VALUES (88, 'vul_target/index', '收集目标', 0, 1, 5, 2, 1639714868, 1, 1639718395, 2, 0, '');
INSERT INTO `auth_rule` VALUES (89, 'config/edit', '未知', 0, 1, 43, 0, 1639716040, 1, 0, 3, 0, '');
INSERT INTO `auth_rule` VALUES (90, 'vulmap/index', 'vulmap扫描', 0, 1, 8, 9, 1639913634, 1, 1640415179, 2, 0, '');
INSERT INTO `auth_rule` VALUES (91, 'code/_add_code', '未知', 0, 1, 43, 0, 1640002292, 1, 0, 3, 0, '');
INSERT INTO `auth_rule` VALUES (92, 'auth/auth_group_edit', '未知', 0, 1, 43, 0, 1640415694, 1, 0, 3, 0, '');
INSERT INTO `auth_rule` VALUES (93, 'auth/user_add', '未知', 0, 1, 43, 0, 1640415707, 1, 0, 3, 0, '');
INSERT INTO `auth_rule` VALUES (94, 'auth/auth_group_add', '未知', 0, 1, 43, 0, 1640415766, 1, 0, 3, 0, '');
INSERT INTO `auth_rule` VALUES (95, 'vulnerable/add', '未知', 0, 1, 43, 0, 1640416950, 1, 0, 3, 0, '');
INSERT INTO `auth_rule` VALUES (96, 'app/del', '未知', 0, 1, 43, 0, 1640530115, 1, 0, 3, 0, '');
INSERT INTO `auth_rule` VALUES (97, 'app/batch_import', '未知', 0, 1, 43, 0, 1640785580, 1, 0, 3, 0, '');
INSERT INTO `auth_rule` VALUES (98, 'app/downloa_app_template', '未知', 0, 1, 43, 0, 1640866777, 1, 0, 3, 0, '');
INSERT INTO `auth_rule` VALUES (99, 'log/clear_all', '未知', 0, 1, 43, 0, 1640874896, 1, 0, 3, 0, '');
INSERT INTO `auth_rule` VALUES (100, 'config/system_update', '系统更新', 0, 1, 23, 6, 1641030459, 1, 1641043579, 2, 0, '');
INSERT INTO `auth_rule` VALUES (101, 'app/a', '未知', 0, 1, 43, 0, 1641281181, 1, 0, 3, 0, '');
INSERT INTO `auth_rule` VALUES (102, 'plugin_store/index', '插件列表', 0, 1, 23, 7, 1641302196, 1, 0, 2, 0, '');
INSERT INTO `auth_rule` VALUES (103, 'plugin_store/install', '未知', 0, 1, 43, 0, 1641306718, 1, 0, 3, 0, '');
INSERT INTO `auth_rule` VALUES (104, 'plugin_store/uninstall', '未知', 0, 1, 43, 0, 1641366990, 1, 0, 3, 0, '');
INSERT INTO `auth_rule` VALUES (105, 'code/details', '未知', 0, 1, 43, 0, 1641658809, 1, 0, 3, 0, '');
INSERT INTO `auth_rule` VALUES (106, 'code/rescan', '未知', 0, 1, 43, 0, 1641664261, 1, 0, 3, 0, '');
INSERT INTO `auth_rule` VALUES (107, 'code_webshell/index', '河马(WebShell)', 0, 1, 14, 7, 1641671594, 1, 1641671642, 2, 0, '');
INSERT INTO `auth_rule` VALUES (108, 'app/export', '未知', 0, 1, 43, 0, 1641736005, 1, 0, 3, 0, '');
INSERT INTO `auth_rule` VALUES (110, 'app/rescan', '未知', 0, 1, 43, 0, 1642142719, 1, 0, 3, 0, '');
INSERT INTO `auth_rule` VALUES (111, 'zhiwen/index', '指纹列表', 0, 1, 35, 11, 1643338608, 1, 0, 2, 0, '');
INSERT INTO `auth_rule` VALUES (112, 'backup/backup', '未知', 0, 1, 43, 0, 1643091277, 1, 0, 3, 0, '');
INSERT INTO `auth_rule` VALUES (113, 'user_log/index', '登录日志', 0, 1, 23, 9, 1643018839, 1, 0, 2, 0, '');
INSERT INTO `auth_rule` VALUES (114, 'backup/index', '数据备份', 0, 1, 23, 8, 1642854435, 1, 0, 2, 0, '');
INSERT INTO `auth_rule` VALUES (115, 'github_keyword_monitor_notice/index', 'github关键词监控结果', 0, 1, 35, 8, 1642852575, 1, 0, 2, 0, '');
INSERT INTO `auth_rule` VALUES (116, 'mobsfscan/index', 'mobsfscan列表', 0, 1, 14, 4, 1652079904, 1, 1652079930, 2, 0, '');
INSERT INTO `auth_rule` VALUES (117, 'murphysec/index', 'murphysec列表', 0, 1, 14, 4, 1652861447, 1, 1652861478, 2, 0, '');
INSERT INTO `auth_rule` VALUES (333, '', '插件中心', 0, 1, 0, 8, 1642257783, 0, 0, 1, 0, '');
INSERT INTO `auth_rule` VALUES (334, 'config/clear_cache', '清除缓存', 0, 1, 23, 10, 1660199916, 1, 0, 2, 0, '');
INSERT INTO `auth_rule` VALUES (335, 'unauthorized/index', '未授权列表', 0, 1, 35, 4, 1669016435, 1, 1669016502, 2, 0, '');
INSERT INTO `auth_rule` VALUES (336, 'index/upgrade_tips', '未知', 0, 1, 43, 0, 1669882093, 1, 0, 3, 0, '');

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
  `create_time` datetime NULL DEFAULT CURRENT_TIMESTAMP,
  `check_status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '审核状态',
  `is_delete` tinyint(1) NOT NULL DEFAULT 0,
  `user_id` int(10) NOT NULL DEFAULT 0,
  `app_id` int(11) NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `un_vuln_id`(`vuln_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = COMPACT;

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
  `create_time` datetime NULL DEFAULT CURRENT_TIMESTAMP,
  `detail` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL COMMENT '漏洞详情',
  `category` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
  `host` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
  `check_status` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = COMPACT;

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
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `files` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
  `author` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
  `project_hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
  `version` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
  `project_id` int(11) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = COMPACT;

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
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of code_check_notice
-- ----------------------------

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
-- Records of code_composer
-- ----------------------------

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
  `create_time` datetime NULL DEFAULT NULL,
  `user_id` int(10) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = COMPACT;

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
  `create_time` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
  `user_id` int(10) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = COMPACT;

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
  `create_time` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
  `check_status` int(1) NOT NULL DEFAULT 0 COMMENT '审核状态',
  `user_id` int(10) NOT NULL DEFAULT 0,
  `type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '类型',
  `filename` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '文件路径',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = COMPACT;

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
  `create_time` datetime NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of config
-- ----------------------------

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
-- Records of fortify
-- ----------------------------

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
-- Records of github_keyword_monitor
-- ----------------------------

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
  `github_release_date` datetime NULL DEFAULT NULL,
  `references` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL COMMENT '参考资料',
  `desc` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
  `package` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
  `hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
  `create_time` datetime NULL DEFAULT '2000-01-01 00:00:00',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = COMPACT;

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
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = COMPACT;

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
  `create_time` datetime NULL DEFAULT NULL,
  `app_id` int(10) NOT NULL DEFAULT 0,
  `user_id` int(10) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = COMPACT;

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
-- Records of host_port
-- ----------------------------

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
-- Records of host_unauthorized
-- ----------------------------

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
-- Records of log
-- ----------------------------

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
-- Records of mobsfscan
-- ----------------------------

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
-- Records of murphysec
-- ----------------------------

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
-- Records of murphysec_vuln
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
  `create_time` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = COMPACT;

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
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int(10) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = COMPACT;

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
-- Records of plugin
-- ----------------------------

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
-- Records of plugin_scan_log
-- ----------------------------

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
-- Records of plugin_store
-- ----------------------------

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
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = COMPACT;

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
  `update_time` datetime NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `type` int(11) NULL DEFAULT 0 COMMENT '工具分类  0  黑盒扫描   1  白盒审计  2  专项扫描  3  系统其他',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 45 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of process_safe
-- ----------------------------
INSERT INTO `process_safe` VALUES (2, 'scan xray', 'cd /root/qingscan/code  &&  php think scan xray  >> /tmp/xray.txt & ', 0, 'xray扫描', '2022-12-01 16:55:02', 0);
INSERT INTO `process_safe` VALUES (3, 'scan awvs', 'cd /root/qingscan/code  &&  php think scan awvs  >> /tmp/awvs.txt & ', 0, 'awvs扫描', '2022-05-11 18:00:47', 0);
INSERT INTO `process_safe` VALUES (4, 'scan rad', 'cd /root/qingscan/code  &&  php think scan rad  >> /tmp/rad.txt & ', 0, 'rad爬虫', '2022-12-01 16:54:52', 0);
INSERT INTO `process_safe` VALUES (5, 'scan host', 'cd /root/qingscan/code  &&  php think scan host  >> /tmp/host.txt & ', 0, '将黑盒目标添加到主机扫描', '2022-05-11 18:00:50', 0);
INSERT INTO `process_safe` VALUES (6, 'scan port', 'cd /root/qingscan/code  &&  php think scan port  >> /tmp/port.txt & ', 0, '端口发现', '2022-05-11 18:00:50', 0);
INSERT INTO `process_safe` VALUES (7, 'scan nmap', 'cd /root/qingscan/code  &&  php think scan nmap  >> /tmp/nmap.txt & ', 0, '端口服务识别', '2022-05-11 18:00:52', 0);
INSERT INTO `process_safe` VALUES (8, 'scan fortify', 'cd /root/qingscan/code  &&  php think scan fortify  >> /tmp/fortify.txt & ', 0, 'fortify代码审计', '2022-05-11 18:00:54', 1);
INSERT INTO `process_safe` VALUES (9, 'scan kunlun', 'cd /root/qingscan/code  &&  php think scan kunlun  >> /tmp/kunlun.txt & ', 0, 'kunlun代码审计', '2022-05-11 18:00:55', 1);
INSERT INTO `process_safe` VALUES (10, 'scan semgrep', 'cd /root/qingscan/code  &&  php think scan semgrep  >> /tmp/semgrep.txt & ', 0, 'semgrep代码审计', '2022-12-01 16:54:01', 1);
INSERT INTO `process_safe` VALUES (13, 'scan google', 'cd /root/qingscan/code  &&  php think scan google >> /tmp/google.txt & ', 0, '获取黑盒目标页面基本信息', '2022-05-11 18:00:57', 0);
INSERT INTO `process_safe` VALUES (14, 'scan upadteRegion', 'cd /root/qingscan/code  &&  php think scan upadteRegion >> /tmp/upadteRegion.txt & ', 0, '更新IP的基本信息', '2022-05-11 18:00:58', 0);
INSERT INTO `process_safe` VALUES (15, 'scan whatweb', 'cd /root/qingscan/code  &&  php think scan whatweb >> /tmp/whatweb.txt & ', 0, 'what指纹识别', '2022-05-11 18:00:58', 0);
INSERT INTO `process_safe` VALUES (16, 'scan subdomainScan', 'cd /root/qingscan/code  &&  php think scan subdomainScan >> /tmp/subdomainScan.txt & ', 0, 'subdomain子域名扫描', '2022-05-11 18:00:59', 0);
INSERT INTO `process_safe` VALUES (17, 'scan hydra', 'cd /root/qingscan/code  &&  php think scan hydra >> /tmp/hydra.txt & ', 0, 'hydra主机爆破', '2022-05-11 18:01:00', 0);
INSERT INTO `process_safe` VALUES (18, 'scan sqlmapScan', 'cd /root/qingscan/code  &&  php think scan sqlmapScan >> /tmp/sqlmapScan.txt & ', 0, 'sqlmap扫描URL', '2022-12-01 16:51:12', 0);
INSERT INTO `process_safe` VALUES (20, 'scan fofa', 'cd /root/qingscan/code  &&  php think scan fofa >> /tmp/fofa.txt & ', 0, 'fofa收集缺陷站点', '2022-05-11 18:01:02', 0);
INSERT INTO `process_safe` VALUES (21, 'scan dirmapScan', 'cd /root/qingscan/code  &&  php think scan dirmapScan >> /tmp/dirmapScan.txt & ', 0, '扫描黑盒目标后台', '2022-05-11 18:01:03', 0);
INSERT INTO `process_safe` VALUES (22, 'scan getNotice', 'cd /root/qingscan/code  &&  php think scan getNotice >> /tmp/getNotice.txt & ', 0, '获取GitHub漏洞公告', '2022-05-11 18:01:04', 0);
INSERT INTO `process_safe` VALUES (23, 'scan backup', 'cd /root/qingscan/code  &&  php think scan backup>> /tmp/backup.txt & ', 1, '数据库备份', '2022-01-05 23:40:36', 3);
INSERT INTO `process_safe` VALUES (24, 'scan getProjectComposer', 'cd /root/qingscan/code  &&  php think scan getProjectComposer>> /tmp/composer.txt & ', 0, '获取composer组件', '2022-05-11 18:01:06', 1);
INSERT INTO `process_safe` VALUES (25, 'scan code_python', 'cd /root/qingscan/code  &&  php think scan code_python>> /tmp/code_python.txt & ', 0, '获取python组件', '2022-05-11 18:01:09', 1);
INSERT INTO `process_safe` VALUES (26, 'scan code_java', 'cd /root/qingscan/code  &&  php think scan code_java>> /tmp/code_java.txt & ', 0, '获取java组件', '2022-05-11 18:01:11', 1);
INSERT INTO `process_safe` VALUES (27, 'scan giteeProject', 'cd /root/qingscan/code  &&  php think scan giteeProject>> /tmp/giteeProject.txt & ', 0, '获取码云项目', '2022-01-05 23:45:00', 1);
INSERT INTO `process_safe` VALUES (28, 'scan freeAgent', 'cd /root/qingscan/code  &&  php think scan freeAgent>> /tmp/freeAgent.txt & ', 0, '获取免费代理', '2022-05-11 18:01:13', 3);
INSERT INTO `process_safe` VALUES (29, 'scan github_keyword_monitor', 'cd /root/qingscan/code  &&  php think scan github_keyword_monitor>> /tmp/github_keyword_monitor.txt & ', 0, 'github关键字监控', '2022-05-11 18:01:14', 0);
INSERT INTO `process_safe` VALUES (30, 'scan whatwebPocTest', 'cd /root/qingscan/code  &&  php think scan whatwebPocTest>> /tmp/whatwebPocTest.txt & ', 0, 'whatweb组件识别poc验证', '2022-05-11 18:01:14', 0);
INSERT INTO `process_safe` VALUES (31, 'scan xrayAgentResult', 'cd /root/qingscan/code  &&  php think scan xrayAgentResult>> /tmp/xrayAgentResult.txt & ', 0, '获取xray代理模式结果数据', '2022-05-11 18:01:15', 0);
INSERT INTO `process_safe` VALUES (32, 'scan startXrayAgent', 'cd /root/qingscan/code  &&  php think scan startXrayAgent>> /tmp/startXrayAgent.txt & ', 0, '启动xray代理模式', '2022-05-11 18:01:15', 0);
INSERT INTO `process_safe` VALUES (33, 'scan code_webshell_scan', 'cd /root/qingscan/code  &&  php think scan code_webshell_scan>> /tmp/code_webshell_scan.txt & ', 0, '河马webshell检测', '2022-05-11 18:01:16', 1);
INSERT INTO `process_safe` VALUES (35, 'scan wafw00fScan', 'cd /root/qingscan/code  &&  php think scan wafw00fScan>> /tmp/wafw00fScan.txt & ', 0, 'waf指纹识别', '2022-05-11 18:01:16', 0);
INSERT INTO `process_safe` VALUES (37, 'scan nucleiScan', 'cd /root/qingscan/code  &&  php think scan nucleiScan>> /tmp/nucleiScan.txt & ', 0, 'nuclei扫描', '2022-05-11 18:01:16', 0);
INSERT INTO `process_safe` VALUES (38, 'scan vulmapPocTest', 'cd /root/qingscan/code  &&  php think scan vulmapPocTest>> /tmp/vulmapPocTest.txt & ', 0, 'vulmap漏洞扫描POC测试', '2022-05-11 18:01:17', 0);
INSERT INTO `process_safe` VALUES (39, 'scan dismapScan', 'cd /root/qingscan/code  &&  php think scan dismapScan>> /tmp/dismapScan.txt & ', 0, 'dismap指纹识别', '2022-05-11 18:01:18', 0);
INSERT INTO `process_safe` VALUES (40, 'scan plugin_safe', 'cd /root/qingscan/code  &&  php think scan plugin_safe>> /tmp/plugin_safe.txt & ', 0, '自定义工具守护', '2022-01-05 23:44:56', 3);
INSERT INTO `process_safe` VALUES (41, 'scan crawlergoScan', 'cd /root/qingscan/code  &&  php think scan crawlergoScan>> /tmp/crawlergoScan.txt & ', 0, 'crawlergo爬虫URL收集', '2022-05-11 18:01:20', 0);
INSERT INTO `process_safe` VALUES (42, 'scan mobsfscan', 'cd /root/qingscan/code  &&  php think scan mobsfscan>> /tmp/mobsfscan.txt & ', 0, 'mobsfscan代码审计(app)', '2022-05-10 11:09:24', 1);
INSERT INTO `process_safe` VALUES (43, 'scan murphysecScan', 'cd /root/qingscan/code  &&  php think scan murphysecScan>> /tmp/murphysecScan.txt & ', 0, '开源组件漏洞扫描工具', '2022-05-19 16:35:14', 1);
INSERT INTO `process_safe` VALUES (44, 'scan unauthorizeScan', 'cd /root/qingscan/code  &&  php think scan unauthorizeScan>> /tmp/unauthorizeScan.txt & ', 0, '未授权扫描', '2022-11-21 18:11:52', 4);

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
-- Records of project_tools
-- ----------------------------

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
  `scan_time` datetime NULL DEFAULT '2000-01-01 14:14:14',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = COMPACT;

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
) ENGINE = InnoDB AUTO_INCREMENT = 9 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of system_config
-- ----------------------------
INSERT INTO `system_config` VALUES (1, 'fofa用户名', 'fofa_user', NULL, 0);
INSERT INTO `system_config` VALUES (2, 'fofa密钥', 'fofa_token', NULL, 0);
INSERT INTO `system_config` VALUES (3, '百度ak', 'baidu_ak', 'xxxxxxxx', 0);
INSERT INTO `system_config` VALUES (4, 'github秘钥', 'github_token', 'xxxxxxxxx', 0);
INSERT INTO `system_config` VALUES (5, 'awvs地址', 'awvs_url', 'xxxxxxxxxxxxxxxxxx', 0);
INSERT INTO `system_config` VALUES (6, 'awvs密钥', 'awvs_token', 'xxxxxxxxxxxxxxxxxx', 0);
INSERT INTO `system_config` VALUES (7, '最大进程数量', 'maxProcesses', '1', 0);
INSERT INTO `system_config` VALUES (8, '墨菲安全token', 'murphysec_token', 'xxxxxxxxxxxxxxxxxx', 0);

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
-- Records of system_zanzhu
-- ----------------------------
INSERT INTO `system_zanzhu` VALUES (1, 'Tian钧', 5, '2021-12-27', '工具不错 我推荐给兄弟们了');
INSERT INTO `system_zanzhu` VALUES (2, 'Maxx', 10, '2021-12-27', NULL);
INSERT INTO `system_zanzhu` VALUES (3, '茜茜茜呀', 50, '2021-12-31', '谢谢师傅的工具');
INSERT INTO `system_zanzhu` VALUES (4, 'Andy', 5, '2021-12-28', NULL);
INSERT INTO `system_zanzhu` VALUES (5, 'tx', 50, '2021-12-30', NULL);
INSERT INTO `system_zanzhu` VALUES (6, '缘分.', 28.88, '2022-01-03', NULL);
INSERT INTO `system_zanzhu` VALUES (7, '缘分.', 20, '2022-01-03', NULL);
INSERT INTO `system_zanzhu` VALUES (8, '鲸落', 20, '2022-01-06', '黑渊信息安全-鲸落');
INSERT INTO `system_zanzhu` VALUES (9, '小小阳', 10, '2022-01-06', NULL);
INSERT INTO `system_zanzhu` VALUES (10, '小孜然', 10, '2022-01-07', '国家网络安全的发展离不开每个人的努力。');

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
  `create_time` datetime NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = COMPACT;

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
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`hash`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = COMPACT;

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
-- Records of urls
-- ----------------------------

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
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of user
-- ----------------------------
INSERT INTO `user` VALUES (1, 'admin', '2871d22a3e5df21199ed0f2c2320c417', '', '测试', 8, 1635494087, '', 0, 1, 1640704760, 0, 0, '', '', '', '1ca4725c34758183af3fd1f723f07a31', '/');

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
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of user_log
-- ----------------------------

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
-- Records of vul_target
-- ----------------------------

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
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of xray
-- ----------------------------

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

-- ----------------------------
-- Records of zhiwen
-- ----------------------------

SET FOREIGN_KEY_CHECKS = 1;

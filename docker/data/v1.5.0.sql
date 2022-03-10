CREATE TABLE `zhiwen` (
                          `id` int(10) NOT NULL AUTO_INCREMENT,
                          `add_by` varchar(255) DEFAULT NULL,
                          `add_time` varchar(255) DEFAULT NULL,
                          `filters` varchar(255) DEFAULT NULL,
                          `keyword` varchar(255) DEFAULT NULL,
                          `md5` varchar(255) DEFAULT NULL,
                          `supplier` varchar(255) DEFAULT NULL,
                          `tags` varchar(255) DEFAULT NULL,
                          `title` varchar(255) DEFAULT NULL,
                          PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT;

update auth_rule set menu_status=0 where title='插件中心';
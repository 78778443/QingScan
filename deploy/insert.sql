INSERT INTO  `user` (`id`, `username`, `password`, `salt`, `nickname`, `auth_group_id`, `created_at`, `last_login_ip`, `last_login_time`, `status`, `update_time`, `is_delete`, `sex`, `phone`, `dd_token`, `email`, `token`, `url`) VALUES (3, 'admin', '2111', '', '', 0, 0, '', 0, 1, 0, 0, 0, '', '', '', '', '');



-- 系统配置项示例数据
-- 这些是QingScan系统中常用的配置项，您可以根据实际情况修改值
INSERT INTO `system_config` (`name`, `key`, `value`) VALUES


-- 邮箱配置（用于系统通知）
('SMTP服务器', 'smtp_server', 'smtp.example.com'),
('SMTP端口', 'smtp_port', '465'),
('SMTP用户名', 'smtp_username', 'your_email@example.com'),
('SMTP密码', 'smtp_password', 'your_email_password'),
('发件人邮箱', 'smtp_sender', 'noreply@example.com'),

-- 系统通用配置
('系统名称', 'system_name', 'QingScan'),
('系统版本', 'system_version', 'v1.0'),
('自动备份开关', 'auto_backup', '1'),
('备份间隔（小时）', 'backup_interval', '24'),


-- 安全配置
('登录失败次数限制', 'login_fail_limit', '5'),
('登录锁定时间（分钟）', 'login_lock_time', '15'),
('密码最小长度', 'password_min_length', '8'),

-- 存储配置
('上传文件最大大小（MB）', 'upload_max_size', '50'),
('文件存储路径', 'file_storage_path', './data/uploads');
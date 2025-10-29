# AWVS 安装与配置指南

## 简介

AWVS (Acunetix Web Vulnerability Scanner) 是一款商业Web应用漏洞扫描器，能够自动发现和报告Web应用中的安全漏洞。

## 安装方法

AWVS是商业软件，需要购买许可证才能使用。有两种部署方式：

### 1. 本地安装

访问Acunetix官网下载并安装AWVS：
https://www.acunetix.com/download/

### 2. Docker部署

```bash
# 拉取AWVS Docker镜像
docker pull secfa/docker-awvs

# 运行容器
docker run -it -d -p 3443:3443 secfa/docker-awvs
```

## 配置

QingScan需要配置AWVS的API地址和访问令牌才能使用。

### 获取API令牌

1. 登录AWVS管理界面
2. 进入"User Management" > "Users"
3. 点击用户详情，找到API密钥

### 在QingScan中配置

在QingScan的配置表中添加以下配置项：

```sql
INSERT INTO `config` (`name`, `value`) VALUES ('awvs_url', 'https://your-awvs-server:3443');
INSERT INTO `config` (`name`, `value`) VALUES ('awvs_token', 'your-api-token');
```

或者在系统配置界面中设置：

- awvs_url: AWVS服务器地址（例如：https://localhost:3443）
- awvs_token: API访问令牌

## 验证配置

在QingScan中运行测试扫描，确认能够成功连接到AWVS服务器。

## 在QingScan中的使用

QingScan会在执行扫描任务时自动调用AWVS API。相关配置在数据库config表中：
- awvs_url: AWVS服务器地址
- awvs_token: API访问令牌

## 常见问题

### 1. 连接问题

确保：
- AWVS服务器正在运行
- 网络可以访问AWVS服务器
- API令牌正确且未过期

### 2. SSL证书问题

如果AWVS使用自签名证书，可能需要在QingScan中忽略SSL验证。

### 3. 权限问题

确保API令牌具有足够的权限执行扫描任务。

## 更多信息

- [Acunetix官网](https://www.acunetix.com/)
- [AWVS API文档](https://github.com/acunetix/acunetix-api-documentation)
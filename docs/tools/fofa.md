# FOFA 安装与配置指南

## 简介

FOFA是一款网络空间资产搜索引擎，可以帮助用户快速发现和识别网络资产。

## 注册与获取API密钥

### 1. 注册FOFA账号

访问FOFA官网注册账号：
https://fofa.info/

### 2. 获取API密钥

1. 登录FOFA账户
2. 进入个人资料页面
3. 找到API Key部分，获取邮箱和API密钥

## 在QingScan中配置

QingScan需要配置FOFA的账号邮箱和API密钥才能使用FOFA搜索功能。

### 数据库配置

在QingScan的配置表中添加以下配置项：

```sql
INSERT INTO `config` (`name`, `value`) VALUES ('fofa_email', 'your-email@example.com');
INSERT INTO `config` (`name`, `value`) VALUES ('fofa_key', 'your-fofa-api-key');
```

或者在系统配置界面中设置：

- fofa_email: FOFA账号邮箱
- fofa_key: FOFA API密钥

## 验证配置

在QingScan中运行FOFA搜索测试，确认能够成功连接到FOFA API并获取数据。

## 在QingScan中的使用

QingScan会在执行ASM(资产发现)任务时自动调用FOFA API。相关配置在数据库config表中：
- fofa_email: FOFA账号邮箱
- fofa_key: FOFA API密钥

## FOFA查询语法

FOFA支持丰富的查询语法，例如：

- `ip="8.8.8.8"` - 搜索特定IP
- `domain="example.com"` - 搜索特定域名
- `port="80"` - 搜索开放80端口的主机
- `title="登录"` - 搜索页面标题包含"登录"的网站
- `header="nginx"` - 搜索HTTP头包含"nginx"的网站

## 常见问题

### 1. API配额限制

FOFA免费用户有API调用次数限制，如果超出限制将无法继续调用。

### 2. 查询语法错误

确保FOFA查询语法正确，错误的语法会导致查询失败。

### 3. 网络连接问题

确保服务器可以正常访问FOFA API地址。

## 更多信息

- [FOFA官网](https://fofa.info/)
- [FOFA API文档](https://fofa.info/api)
- [FOFA查询语法](https://fofa.info/static_pages/api_help)
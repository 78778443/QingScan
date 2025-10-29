# Murphysec 安装与配置指南

## 简介

Murphysec是一款代码安全检测工具，用于检测代码中的安全漏洞和依赖风险。

## 安装方法

### Linux/macOS 安装

```bash
# 使用官方脚本安装
curl -sSL https://cdn.jsdelivr.net/gh/murphysecurity/murphysec@main/install.sh | bash

# 或者下载二进制文件
wget https://github.com/murphysecurity/murphysec/releases/latest/download/murphysec-linux-amd64.tar.gz
tar -zxvf murphysec-linux-amd64.tar.gz
sudo mv murphysec /usr/local/bin/
```

### Windows 安装

访问Murphysec GitHub发布页面下载Windows版本：
https://github.com/murphysecurity/murphysec/releases

### Docker安装

```bash
docker pull murphysecurity/murphysec
```

## 配置

### 获取Token

1. 访问Murphysec平台注册账号：https://www.murphysec.com/
2. 登录后进入控制台
3. 在个人设置中获取访问Token

### 在QingScan中配置

Murphysec工具在QingScan中的路径配置：
`{QingScan项目根目录}/code/extend/tools/amd64/murphysec`

确保该路径下有Murphysec可执行文件，并添加执行权限：

```bash
chmod +x {QingScan项目根目录}/code/extend/tools/amd64/murphysec
```

## 验证安装

```bash
murphysec version
```

或者使用QingScan中的版本：

```bash
{QingScan项目根目录}/code/extend/tools/amd64/murphysec version
```

## 在QingScan中的使用

QingScan会在执行代码扫描任务时自动调用Murphysec工具。

### 使用方式

QingScan通过命令行调用Murphysec：

```bash
murphysec scan /path/to/code
```

## 常见问题

### 1. 权限问题

确保Murphysec具有执行权限：

```bash
chmod +x {QingScan项目根目录}/code/extend/tools/amd64/murphysec
```

### 2. Token配置问题

确保在使用Murphysec前已正确配置Token：

```bash
murphysec auth login
```

### 3. 网络连接问题

Murphysec需要连接到云端服务，确保网络连接正常。

## 更多信息

- [Murphysec官网](https://www.murphysec.com/)
- [Murphysec GitHub项目](https://github.com/murphysecurity/murphysec)
- [Murphysec文档](https://www.murphysec.com/docs/)
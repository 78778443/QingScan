# XRAY 安装与配置指南

## 简介

XRAY是一款功能强大的安全评估工具，支持主动和被动扫描模式，能够检测多种Web漏洞。

## 安装方法

### 下载XRAY工具

1. 访问XRAY的GitHub发布页面：https://github.com/chaitin/xray/releases
2. 下载适合您系统的版本（通常为Linux版本）
3. 解压下载的文件

### 部署到指定位置

将XRAY工具放置在项目指定目录中：

```bash
# 创建目录
mkdir -p {QingScan项目根目录}/code/extend/tools/xray

# 将下载的XRAY文件复制到该目录
cp xray_linux_amd64 {QingScan项目根目录}/code/extend/tools/xray/

# 添加执行权限
chmod +x {QingScan项目根目录}/code/extend/tools/xray/xray_linux_amd64
```

## 验证安装

```bash
cd {QingScan项目根目录}/code/extend/tools/xray/
./xray_linux_amd64 version
```

## 在QingScan中的使用

QingScan会在执行扫描任务时自动调用XRAY工具。工具路径配置在代码中为：
`{QingScan项目根目录}/code/extend/tools/xray/`

## 常见问题

### 1. 权限问题

确保XRAY可执行文件具有执行权限：

```bash
chmod +x {QingScan项目根目录}/code/extend/tools/xray/xray_linux_amd64
```

### 2. 配置文件

XRAY支持使用配置文件，可以根据需要创建配置文件：

```bash
cd {QingScan项目根目录}/code/extend/tools/xray/
./xray_linux_amd64 genca
```

## 更多信息

- [XRAY GitHub项目](https://github.com/chaitin/xray)
- [XRAY官方文档](https://docs.xray.cool/)
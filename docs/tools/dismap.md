# Dismap 安装与配置指南

## 简介

Dismap是一款Web指纹识别工具，用于识别目标网站的CMS类型、版本、服务器信息等。

## 安装方法

### 下载Dismap工具

1. 访问Dismap的GitHub项目页面：https://github.com/zhzyker/dismap
2. 下载适合您系统的版本（通常为Linux版本）

### 部署到指定位置

将Dismap工具放置在项目指定目录中：

```bash
# 创建目录
mkdir -p {QingScan项目根目录}/code/extend/tools/dismap

# 将下载的Dismap文件复制到该目录
cp dismap {QingScan项目根目录}/code/extend/tools/dismap/

# 添加执行权限
chmod +x {QingScan项目根目录}/code/extend/tools/dismap/dismap
```

## 验证安装

```bash
cd {QingScan项目根目录}/code/extend/tools/dismap/
./dismap --help
```

## 在QingScan中的使用

QingScan会在执行扫描任务时自动调用Dismap工具。工具路径配置在代码中为：
`{QingScan项目根目录}/code/extend/tools/dismap/`

## 常见问题

### 1. 权限问题

确保Dismap可执行文件具有执行权限：

```bash
chmod +x {QingScan项目根目录}/code/extend/tools/dismap/dismap
```

### 2. 依赖库问题

如果运行时出现依赖库问题，可能需要安装额外的库：

```bash
# Ubuntu/Debian
sudo apt-get install libc6

# CentOS/RHEL
sudo yum install glibc
```

## 更多信息

- [Dismap GitHub项目](https://github.com/zhzyker/dismap)
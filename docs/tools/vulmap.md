# Vulmap 安装与配置指南

## 简介

Vulmap是一款Web漏洞扫描和验证工具，可以检测多种Web漏洞，包括SQL注入、XSS、命令执行等。

## 安装方法

### 下载Vulmap工具

1. 访问Vulmap的GitHub项目页面：https://github.com/zhzyker/vulmap
2. 克隆或下载项目代码

### 部署到指定位置

将Vulmap工具放置在项目指定目录中：

```bash
# 创建目录
mkdir -p {QingScan项目根目录}/code/extend/tools/vulmap

# 克隆项目到该目录
git clone https://github.com/zhzyker/vulmap.git {QingScan项目根目录}/code/extend/tools/vulmap

# 安装依赖
cd {QingScan项目根目录}/code/extend/tools/vulmap
pip install -r requirements.txt
```

## 验证安装

```bash
cd {QingScan项目根目录}/code/extend/tools/vulmap/
python3 vulmap.py --help
```

## 在QingScan中的使用

QingScan会在执行扫描任务时自动调用Vulmap工具。工具路径配置在代码中为：
`{QingScan项目根目录}/code/extend/tools/vulmap/`

## 常见问题

### 1. Python依赖问题

确保系统已安装Python3和pip：

```bash
# Ubuntu/Debian
sudo apt-get install python3 python3-pip

# CentOS/RHEL
sudo yum install python3 python3-pip
```

### 2. 权限问题

确保Vulmap脚本具有执行权限：

```bash
chmod +x {QingScan项目根目录}/code/extend/tools/vulmap/vulmap.py
```

## 更多信息

- [Vulmap GitHub项目](https://github.com/zhzyker/vulmap)
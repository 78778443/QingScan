# RAD 安装与配置指南

## 简介

RAD是一款基于Chrome内核的浏览器爬虫，用于爬取Web应用程序并生成站点地图。它能够模拟用户行为，发现隐藏的页面和参数。

## 安装方法

### 下载RAD工具

1. 访问RAD的GitHub发布页面：https://github.com/zcgonvh/Rad/releases
2. 下载适合您系统的版本（通常为Linux版本）
3. 解压下载的文件

### 安装Chrome浏览器

RAD依赖于Chrome浏览器，需要先安装Chrome：

```bash
# Ubuntu/Debian
wget -q -O - https://dl.google.com/linux/linux_signing_key.pub | sudo apt-key add -
sudo sh -c 'echo "deb [arch=amd64] http://dl.google.com/linux/chrome/deb/ stable main" >> /etc/apt/sources.list.d/google-chrome.list'
sudo apt-get update
sudo apt-get install google-chrome-stable

# CentOS/RHEL
sudo yum install wget
wget https://dl.google.com/linux/direct/google-chrome-stable_current_x86_64.rpm
sudo yum localinstall google-chrome-stable_current_x86_64.rpm
```

### 部署到指定位置

将RAD工具放置在项目指定目录中：

```bash
# 创建目录
mkdir -p {QingScan项目根目录}/code/extend/tools/rad

# 将下载的RAD文件复制到该目录
cp rad_linux_amd64 {QingScan项目根目录}/code/extend/tools/rad/

# 添加执行权限
chmod +x {QingScan项目根目录}/code/extend/tools/rad/rad_linux_amd64
```

## 验证安装

```bash
cd {QingScan项目根目录}/code/extend/tools/rad/
./rad_linux_amd64 --help
```

## 在QingScan中的使用

QingScan会在执行扫描任务时自动调用RAD工具。工具路径配置在代码中为：
`{QingScan项目根目录}/code/extend/tools/rad/`

## 常见问题

### 1. Chrome依赖问题

如果遇到Chrome依赖问题，可以尝试安装额外的依赖：

```bash
# Ubuntu/Debian
sudo apt-get install -f
sudo apt-get install libappindicator1 libindicator7

# CentOS/RHEL
sudo yum install -y libXScrnSaver libappindicator libappindicator-gtk3
```

### 2. 权限问题

确保RAD可执行文件具有执行权限：

```bash
chmod +x {QingScan项目根目录}/code/extend/tools/rad/rad_linux_amd64
```

## 更多信息

- [RAD GitHub项目](https://github.com/zcgonvh/Rad)
# Wafw00f 安装与配置指南

## 简介

Wafw00f是一款Web应用防火墙(WAF)识别工具，可以识别多种WAF产品。

## 安装方法

### 使用pip安装（推荐）

```bash
pip install wafw00f
```

### 从源码安装

```bash
# 克隆项目
git clone https://github.com/EnableSecurity/wafw00f.git
cd wafw00f

# 安装
python setup.py install
```

### 使用Docker安装

```bash
docker pull enablesecurity/wafw00f
```

## 验证安装

```bash
wafw00f --help
```

## 在QingScan中的使用

QingScan会在执行扫描任务时自动调用Wafw00f工具。

### 使用方式

QingScan通过命令行调用Wafw00f：

```bash
wafw00f http://example.com
```

## 支持的WAF产品

Wafw00f可以识别多种WAF产品，包括但不限于：

- CloudFlare
- AWS WAF
- Azure Application Gateway WAF
- Imperva Incapsula
- F5 BIG-IP
- Fortinet FortiWeb
- Akamai Kona Site Defender

## 常见问题

### 1. 权限问题

确保Wafw00f具有执行权限：

```bash
which wafw00f
```

### 2. Python依赖问题

确保系统已安装Python3和pip：

```bash
# Ubuntu/Debian
sudo apt-get install python3 python3-pip

# CentOS/RHEL
sudo yum install python3 python3-pip
```

### 3. 网络连接问题

确保可以正常访问目标网站。

## 更多信息

- [Wafw00f GitHub项目](https://github.com/EnableSecurity/wafw00f)
- [Wafw00f文档](https://github.com/EnableSecurity/wafw00f/blob/master/README.md)
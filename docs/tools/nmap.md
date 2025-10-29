# Nmap 安装与配置指南

## 简介

Nmap（Network Mapper）是一款开源的网络发现和安全审计工具。它用于网络发现（确定网络上有哪些主机正在运行）和端口扫描（确定主机上哪些服务正在运行）。

## 安装方法

### Ubuntu/Debian 系统

```bash
sudo apt-get update
sudo apt-get install nmap
```

### CentOS/RHEL/Fedora 系统

```bash
sudo yum install nmap
# 或者对于较新的版本
sudo dnf install nmap
```

### macOS 系统

使用 Homebrew 安装：

```bash
brew install nmap
```

### Windows 系统

1. 访问 [Nmap 官网](https://nmap.org/download.html)
2. 下载 Windows 版本的安装包
3. 运行安装程序并按照提示完成安装
4. 确保将 Nmap 添加到系统 PATH 环境变量中

## 验证安装

安装完成后，可以通过以下命令验证：

```bash
nmap --version
```

如果显示版本信息，说明安装成功。

## 在 QingScan 中使用

QingScan 使用 Nmap 进行端口扫描，系统会自动检测 Nmap 是否已安装。如果未安装，扫描任务将无法执行。

## 常见问题

### 1. 权限问题

在 Linux/macOS 系统上，某些 Nmap 扫描功能可能需要 root 权限：

```bash
sudo nmap -sS target
```

### 2. 防火墙阻止

如果扫描结果不准确，可能是防火墙阻止了 Nmap 的探测包。可以尝试使用不同的扫描技术：

```bash
# TCP connect 扫描
nmap -sT target

# SYN 扫描（需要 root 权限）
sudo nmap -sS target
```

## 更多信息

- [Nmap 官方文档](https://nmap.org/book/man.html)
- [Nmap 扫描技术指南](https://nmap.org/book/man-port-scanning-techniques.html)
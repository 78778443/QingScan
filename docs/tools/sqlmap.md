# SQLMap 安装与配置指南

## 简介

SQLMap是一款开源的自动化SQL注入工具，用于检测和利用Web应用中的SQL注入漏洞。

## 安装方法

### 使用Git克隆（推荐）

```bash
# 克隆项目
git clone --depth 1 https://github.com/sqlmapproject/sqlmap.git {QingScan项目根目录}/code/extend/tools/sqlmap

# 进入目录
cd {QingScan项目根目录}/code/extend/tools/sqlmap
```

### 使用pip安装

```bash
pip install sqlmap
```

### 从官网下载

访问SQLMap官网下载最新版本：
https://github.com/sqlmapproject/sqlmap/releases

## 验证安装

```bash
# 如果使用Git克隆的方式
cd {QingScan项目根目录}/code/extend/tools/sqlmap
python3 sqlmap.py --version

# 如果使用pip安装
sqlmap --version
```

## 在QingScan中的使用

QingScan会在执行扫描任务时自动调用SQLMap工具。工具路径配置在代码中为：
`{QingScan项目根目录}/code/extend/tools/sqlmap/`

## 配置

SQLMap通常不需要特殊配置，但可以通过命令行参数进行定制。

### 常用参数

- `--batch`: 非交互模式，自动使用默认选项
- `--level`: 测试等级（1-5）
- `--risk`: 风险等级（1-3）
- `--threads`: 使用的线程数

## 常见问题

### 1. Python依赖问题

确保系统已安装Python3：

```bash
# Ubuntu/Debian
sudo apt-get install python3

# CentOS/RHEL
sudo yum install python3
```

### 2. 权限问题

确保SQLMap脚本具有执行权限：

```bash
chmod +x {QingScan项目根目录}/code/extend/tools/sqlmap/sqlmap.py
```

### 3. 更新问题

定期更新SQLMap以获取最新的漏洞检测规则：

```bash
cd {QingScan项目根目录}/code/extend/tools/sqlmap
git pull
```

## 更多信息

- [SQLMap GitHub项目](https://github.com/sqlmapproject/sqlmap)
- [SQLMap官方文档](https://github.com/sqlmapproject/sqlmap/wiki)
- [SQLMap使用手册](https://github.com/sqlmapproject/sqlmap/blob/master/doc/translations/README-zh-CN.md)
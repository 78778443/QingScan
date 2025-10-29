# Dirmap 安装与配置指南

## 简介

Dirmap是一款高级Web目录、文件扫描工具，具有较好的兼容性、可靠性、稳定性。

## 安装方法

### 克隆项目

```bash
# 创建目录
mkdir -p {QingScan项目根目录}/code/extend/tools/dirmap

# 克隆项目
git clone https://github.com/H4ckForJob/dirmap.git {QingScan项目根目录}/code/extend/tools/dirmap

# 进入目录
cd {QingScan项目根目录}/code/extend/tools/dirmap
```

### 安装依赖

```bash
# 安装Python依赖
pip3 install -r requirements.txt

# 如果出现安装问题，可以尝试使用国内源
pip3 install -r requirements.txt -i https://pypi.tuna.tsinghua.edu.cn/simple/
```

## 验证安装

```bash
cd {QingScan项目根目录}/code/extend/tools/dirmap
python3 dirmap.py -h
```

## 在QingScan中的使用

QingScan会在执行扫描任务时自动调用Dirmap工具。工具路径需要在配置文件中指定。

### 配置文件设置

在 `config/tools.php` 文件中配置Dirmap路径：

```php
'dirmap' => [
    'install_path'=>'{QingScan项目根目录}/code/extend/tools/dirmap/'
]
```

## 常见问题

### 1. Python依赖问题

确保系统已安装Python3.6+和pip3：

```bash
# Ubuntu/Debian
sudo apt-get install python3 python3-pip

# CentOS/RHEL
sudo yum install python3 python3-pip
```

### 2. 权限问题

确保Dirmap脚本具有执行权限：

```bash
chmod +x {QingScan项目根目录}/code/extend/tools/dirmap/dirmap.py
```

### 3. 字典文件

确保字典文件存在，Dirmap需要字典文件来进行目录爆破。

## 更多信息

- [Dirmap GitHub项目](https://github.com/H4ckForJob/dirmap)
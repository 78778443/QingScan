# WhatWeb 安装与配置指南

## 简介

WhatWeb是一款Web应用程序指纹识别工具，可以识别Web技术，包括内容管理系统(CMS)、博客平台、统计分析代码、JavaScript库、服务器等。

## 安装方法

### Ubuntu/Debian 系统

```bash
sudo apt-get update
sudo apt-get install whatweb
```

### CentOS/RHEL/Fedora 系统

```bash
# CentOS/RHEL
sudo yum install whatweb

# Fedora
sudo dnf install whatweb
```

### 从源码安装

```bash
# 克隆项目
git clone https://github.com/urbanadventurer/WhatWeb.git {QingScan项目根目录}/code/extend/tools/whatweb

# 安装依赖
cd {QingScan项目根目录}/code/extend/tools/whatweb
sudo gem install bundler
bundle install
```

## 配置

QingScan中WhatWeb的配置在 `config/tools.php` 文件中：

```php
'whatweb'=>[
    'install_path'=>'',
    'file_path' => '/data/tools/whatweb',
]
```

## 验证安装

```bash
whatweb --version
```

或者使用源码安装的版本：

```bash
cd {QingScan项目根目录}/code/extend/tools/whatweb
./whatweb --version
```

## 在QingScan中的使用

QingScan会在执行扫描任务时自动调用WhatWeb工具。工具路径配置在代码中为：
`{QingScan项目根目录}/code/extend/tools/whatweb`

## 常见问题

### 1. 权限问题

确保WhatWeb脚本具有执行权限：

```bash
chmod +x {QingScan项目根目录}/code/extend/tools/whatweb/whatweb
```

### 2. Ruby依赖问题

如果从源码安装，确保系统已安装Ruby：

```bash
# Ubuntu/Debian
sudo apt-get install ruby ruby-dev

# CentOS/RHEL
sudo yum install ruby ruby-devel
```

## 更多信息

- [WhatWeb GitHub项目](https://github.com/urbanadventurer/WhatWeb)
- [WhatWeb官方文档](https://github.com/urbanadventurer/WhatWeb/wiki)
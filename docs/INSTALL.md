# QingScan 工具安装说明

## 概述

QingScan 提供了两种方式来安装所需的扫描工具：
1. 使用PHP命令安装（推荐）
2. 使用Shell脚本安装

## 使用PHP命令安装（推荐）

### 查看帮助信息

```bash
php think install help
```

### 查看可安装的工具列表

```bash
php think install list
```

### 安装所有工具

```bash
php think install all
```

### 安装指定工具

```bash
php think install <工具名>
```

例如安装 nmap：
```bash
php think install nmap
```
 

## 支持的工具

QingScan 目前支持安装以下工具：

1. nmap       - 网络发现和端口扫描工具
2. whatweb    - Web应用程序指纹识别工具
3. hydra      - 网络登录破解工具
4. sqlmap     - 自动化SQL注入工具
5. wafw00f    - WAF指纹识别工具
6. semgrep    - 快速静态分析工具
7. rad        - 浏览器爬虫
8. xray       - 安全评估工具
9. nuclei     - 基于模板的漏洞扫描器
10. vulmap    - Web漏洞扫描和验证工具
11. dismap    - Web指纹识别工具
12. crawlergo - 浏览器爬虫
13. murphysec - 代码安全检测工具
14. dirmap    - 目录扫描工具

## 安装原理

### 系统工具安装

对于系统工具（如 nmap、whatweb、hydra 等），安装脚本会根据当前操作系统类型自动选择合适的包管理器进行安装：
- Debian/Ubuntu 系统使用 apt-get
- RedHat/CentOS 系统使用 yum
- Fedora 系统使用 dnf

### 第三方工具安装

对于第三方工具（如 rad、xray、nuclei 等），安装脚本会从官方 GitHub 仓库下载最新版本，并解压到项目目录下的 `./extend/tools/` 目录中。

### Python 工具安装

对于基于 Python 的工具（如 sqlmap、wafw00f、semgrep 等），安装脚本会使用 pip3 进行安装。

## 注意事项

1. 部分工具需要 root 权限才能安装
2. 安装过程中会自动检测工具是否已经安装，避免重复安装
3. 安装完成后，工具会自动集成到 QingScan 的扫描流程中
4. 如果安装失败，请检查网络连接和系统依赖
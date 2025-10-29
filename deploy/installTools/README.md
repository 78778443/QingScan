# QingScan 工具安装说明

## 支持的工具

| 工具名 | 用途 | 安装方式 |
|--------|------|----------|
| nmap | 网络发现和端口扫描 | 系统包管理器 |
| whatweb | Web应用程序指纹识别 | 系统包管理器/源码 |
| hydra | 网络登录破解 | 系统包管理器 |
| sqlmap | 自动化SQL注入 | pip |
| wafw00f | WAF指纹识别 | pip |
| semgrep | 静态代码分析 | pip |
| rad | 浏览器爬虫 | GitHub Release |
| xray | 安全评估工具 | GitHub Release |
| nuclei | 漏洞扫描器 | GitHub Release |
| vulmap | Web漏洞扫描 | Git克隆 |
| dismap | Web指纹识别 | GitHub Release |
| crawlergo | 浏览器爬虫 | GitHub Release |
| murphysec | 代码安全检测 | GitHub Release |
| dirmap | 目录扫描 | Git克隆 |

## 安装位置

所有下载和安装的工具都会放置在项目目录的正确位置，确保与QingScan的工具检查机制兼容：

- RAD、XRAY、Nuclei、Vulmap、Dismap、Crawlergo、Murphysec、Dirmap等工具会安装到 `code/extend/tools/` 目录下相应的子目录中
- WhatWeb、Hydra、Nmap、SQLMap、Wafw00f、Semgrep等系统工具会通过包管理器安装到系统路径中

## 注意事项

1. 脚本需要在 `install` 目录下运行
2. 部分工具需要 root 权限进行安装
3. 部分工具需要先安装系统依赖（如 git, wget, unzip 等）
4. 有些工具会安装到项目目录的 `code/extend/tools/` 下
5. 脚本会自动检测工具是否已安装，避免重复安装

## 故障排除

如果安装过程中遇到问题，请参考 `docs/tools/` 目录下的详细安装文档。
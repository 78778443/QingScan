# 工具安装与配置指南

本目录包含了 QingScan 所需各种安全工具的安装与配置指南。

## 已支持的工具列表

| 工具名称 | 用途 | 文档链接 |
|---------|------|---------|
| [Nmap](nmap.md) | 网络发现和端口扫描 | [文档](nmap.md) |
| [RAD](rad.md) | 浏览器爬虫 | [文档](rad.md) |
| [XRAY](xray.md) | 安全评估工具 | [文档](xray.md) |
| [Nuclei](nuclei.md) | 漏洞扫描器 | [文档](nuclei.md) |
| [Vulmap](vulmap.md) | Web漏洞扫描和验证工具 | [文档](vulmap.md) |
| [Dismap](dismap.md) | Web指纹识别工具 | [文档](dismap.md) |
| [WhatWeb](whatweb.md) | Web应用程序指纹识别工具 | [文档](whatweb.md) |
| [AWVS](awvs.md) | 商业Web应用漏洞扫描器 | [文档](awvs.md) |
| [FOFA](fofa.md) | 网络空间资产搜索引擎 | [文档](fofa.md) |
| [OneForAll](oneforall.md) | 子域收集工具 | [文档](oneforall.md) |
| [Hydra](hydra.md) | 网络登录破解工具 | [文档](hydra.md) |
| [SQLMap](sqlmap.md) | 自动化SQL注入工具 | [文档](sqlmap.md) |
| [Dirmap](dirmap.md) | 目录扫描工具 | [文档](dirmap.md) |
| [Fortify](fortify.md) | 商业静态代码分析工具 | [文档](fortify.md) |
| [Semgrep](semgrep.md) | 快速静态分析工具 | [文档](semgrep.md) |
| [Murphysec](murphysec.md) | 代码安全检测工具 | [文档](murphysec.md) |
| [CodeQL](codeql.md) | 语义代码分析引擎 | [文档](codeql.md) |
| [Crawlergo](crawlergo.md) | 浏览器爬虫 | [文档](crawlergo.md) |
| [Wafw00f](wafw00f.md) | WAF指纹识别工具 | [文档](wafw00f.md) |
| [Google](google.md) | Google API相关功能 | [文档](google.md) |

## 使用说明

当您运行 QingScan 扫描任务时，系统会自动检查所需工具是否已安装。如果工具未安装或配置不正确，系统将显示安装引导信息，指导您如何安装和配置相关工具。

每个工具文档都包含了以下信息：
1. 工具简介
2. 详细的安装方法
3. 在QingScan中的使用方式
4. 常见问题及解决方案
5. 相关链接

## 贡献文档

如果您愿意为某个工具完善安装文档，请按照以下格式提交：

1. 在本目录下创建对应的 Markdown 文件
2. 包含工具简介、安装方法、配置说明等信息
3. 提交 Pull Request

感谢您的贡献！
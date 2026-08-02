# QingScan

一站式安全运营平台：**漏洞扫描 · 代码审计 · 资产清点 · 工单推进**

- GitHub：https://github.com/78778443/QingScan
- 码云地址：https://gitee.com/songboy/QingScan
- 详细文档：http://wiki.qingscan.site
- 哔哩哔哩：https://space.bilibili.com/437273065

## 介绍

QingScan 是一款开箱即用的安全运营平台，所有扫描能力均为**内置自研引擎**（纯 PHP 实现，不依赖任何外部扫描工具），覆盖从资产发现、漏洞扫描、代码审计到工单处置的完整闭环：

| 功能 | 说明 |
|---|---|
| **漏洞扫描** | 目标管理 + Web漏洞检测、SQL注入检测、目录扫描、指纹识别、弱口令爆破、WAF识别等 |
| **代码审计** | 代码项目管理 + 语法感知级污点分析（SQL注入/XSS/命令执行等 10+ 类规则） |
| **资产清点** | 主机、端口、域名、子域名、URL 资产统一管理 |
| **工单推进** | 从漏洞一键创建工单，五态流转闭环处置 |

### 自研引擎

端口扫描、子域名枚举、目录扫描、指纹识别、SQL注入检测、弱口令爆破、漏洞检测、WAF识别、爬虫、代码审计 —— 全部内置引擎，零外部工具依赖，安装即用。

### 技术栈

- 后端：ThinkPHP 8 + MySQL（自研扫描引擎、常驻任务调度器）
- 前端：React 19 + TypeScript + Vite + shadcn/ui（ECharts 统计大盘）

## 安装教程

需要 Ubuntu 24.04 系统下安装，其他系统请自行安装

1. 安装 PHP 扩展和项目依赖

```bash
apt install php php-xml php-gd php-mysqli php-dom php-cli php-zip unzip php-curl composer

cd QingScan/code && composer install
```

2. 安装前端依赖并构建

```bash
cd QingScan/code/web && npm install && npm run build
```

3. 新建数据库并导入数据表（SQL 文件在 `deploy` 下）

```bash
mysql -uroot -p -e "CREATE DATABASE QingScan DEFAULT CHARACTER SET utf8mb4;"
mysql -uroot -p QingScan < deploy/qingscan.sql
mysql -uroot -p QingScan < deploy/insert.sql
```

> 注意：`qingscan.sql` 不含 USE 语句，导入时必须指定数据库名

4. 配置数据库连接（`code/.env`）

```ini
[DATABASE]
HOSTNAME = 127.0.0.1
DATABASE = QingScan
USERNAME = root
PASSWORD = 你的密码
```

5. 启动服务

```bash
# Web 服务 + 常驻扫描调度器（自动生成并执行扫描任务）
./script.sh
```

或分别启动：

```bash
# Web 服务（新前端访问地址）
php think run -p 8080

# 常驻扫描调度器：自动生成任务、自动执行、异常跳过
php think schedule
```

6. 访问系统

```bash
http://127.0.0.1:8080/web/

账号密码 admin / 2111
```

## 使用流程

1. **网站扫描** → 添加目标（多行 URL + 勾选扫描任务）
2. 调度器自动执行（可在「网站扫描 → 任务队列」查看进度）
3. 查看扫描结果（Web漏洞 / SQL注入 / 目录扫描 / 指纹识别等）
4. **代码审计** → 添加代码仓库 → 自动拉取并审计
5. 漏洞确认后 → **工单管理** → 一键建单 → 状态流转闭环

## 技术支持

QingScan 提供私人订制服务，如果你二次开发需求，可以联系我微信 `songboy8888`

## 联系我

在使用过程中有任何问题，可以通过公众号、微信、QQ群联系
![联系我们](https://user-images.githubusercontent.com/76991805/165303155-10c0a418-78a4-48c2-b5f1-428d8e6118b7.png)

## 功能展示
![](https://oss.songboy.site/blog/20240617224644.png)

![](https://oss.songboy.site/blog/20240617224721.png)

![](https://oss.songboy.site/blog/20240617224735.png)

![](https://oss.songboy.site/blog/20240617224838.png)

## 📑 Licenses

本工具禁止进行未授权商业用途，禁止二次开发后进行未授权商业用途。

本工具仅面向合法授权的企业安全建设行为，在使用本工具进行检测时，您应确保该行为符合当地的法律法规，并且已经取得了足够的授权。

如您在使用本工具的过程中存在任何非法行为，您需自行承担相应后果，我们将不承担任何法律及连带责任。

在使用本工具前，请您务必审慎阅读、充分理解各条款内容，限制、免责条款或者其他涉及您重大权益的条款可能会以加粗、加下划线等形式提示您重点注意。

除非您已充分阅读、完全理解并接受本协议所有条款，否则，请您不要使用本工具。您的使用行为或者您以其他任何明示或者默示方式表示接受本协议的，即视为您已阅读并同意本协议的约束。

## Stargazers

[![Stargazers over time](https://starchart.cc/78778443/QingScan.svg?v211231)](https://github.com/78778443/QingScan)

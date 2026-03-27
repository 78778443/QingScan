# QingScan 扫描平台架构规划

## 一、系统概述

### 1.1 核心理念
- **插件化工具接入**：工具作为独立插件，通过配置化方式接入，无需修改代码
- **统一结果处理**：所有工具的扫描结果统一格式存储，由 LLM 进行智能汇总分析
- **多系统协同**：黑盒、白盒、资产三大系统数据互通，形成完整安全资产视图

### 1.2 系统架构图

```
┌─────────────────────────────────────────────────────────────────────┐
│                           Web UI / API                               │
│  (任务创建、结果展示、资产查看、工具配置、系统管理)                     │
└─────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────┐
│                        任务调度层 (Asynq)                           │
│  (任务队列、异步执行、进度跟踪、结果回调)                              │
└─────────────────────────────────────────────────────────────────────┘
                                    │
              ┌─────────────────────┼─────────────────────┐
              ▼                     ▼                     ▼
┌─────────────────────┐  ┌─────────────────────┐  ┌─────────────────────┐
│   黑盒扫描系统        │  │   白盒审计系统        │  │   资产管理系统       │
│   (Black Box)       │  │   (White Box)       │  │   (Asset)           │
├─────────────────────┤  ├─────────────────────┤  ├─────────────────────┤
│ - Web 漏洞扫描      │  │ - 静态代码分析       │  │ - 主机发现          │
│ - 端口扫描          │  │ - 代码审计           │  │ - 域名发现          │
│ - POC 验证          │  │ - SAST/DAST        │  │ - 端口扫描          │
│ - 爬虫              │  │ - 供应链安全         │  │ - 指纹识别          │
│ - 敏感信息泄露       │  │ - 代码依赖检查        │  │ - 资产监控          │
└─────────────────────┘  └─────────────────────┘  └─────────────────────┘
              │                     │                     │
              └─────────────────────┼─────────────────────┘
                                    ▼
┌─────────────────────────────────────────────────────────────────────┐
│                        工具插件层                                   │
│  ┌────────────┐ ┌────────────┐ ┌────────────┐ ┌────────────┐       │
│  │   Nuclei   │ │   Xray     │ │  Semgrep   │ │   Rad      │  ...   │
│  │   Nmap     │ │   SQLMap   │ │  CodeQL    │ │  Flawfinder│       │
│  │   Crawler  │ │   Dirmap   │ │  Fortify   │ │  Dependency│       │
│  └────────────┘ └────────────┘ └────────────┘ └────────────┘       │
└─────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────┐
│                      结果汇总与 LLM 分析层                           │
│  ┌────────────────┐  ┌────────────────┐  ┌────────────────┐       │
│  │  结果标准化     │  │  LLM 智能分析   │  │  报告生成      │       │
│  │  (统一格式)    │  │  (漏洞分析/建议)│  │  (自动报告)    │       │
│  └────────────────┘  └────────────────┘  └────────────────┘       │
└─────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────┐
│                         数据存储层                                   │
│  MySQL (结构化数据)  +  Redis (缓存/队列)  +  文件存储              │
└─────────────────────────────────────────────────────────────────────┘
```

---

## 二、核心模块设计

### 2.1 工具插件系统（核心）

#### 设计目标
- 工具通过 YAML/JSON 配置化接入，零代码修改
- 工具注册后自动被发现和加载
- 支持工具版本管理、状态检查、参数配置

#### 配置化工具定义
```yaml
# tools/nuclei.yaml
name: nuclei
type: blackbox          # blackbox | whitebox | asset
version: "3.x"
path: /opt/tools/nuclei

# 工具能力定义
capabilities:
  - web-vuln-scan
  - poc-scan
  - cve-detect

# 扫描参数模板
scan_params:
  severity:
    type: select
    options: [critical, high, medium, low, info]
    default: critical,high,medium

  rate_limit:
    type: number
    default: 150

  threads:
    type: number
    default: 25

# 输出解析器
result_parser:
  type: json           # json | xml | text | custom
  pattern:              # 自定义解析正则（可选）
    vuln_name: "info.name"
    severity: "info.severity"
    target: "matched-at"

# 安装命令
install:
  cmd: go install github.com/projectdiscovery/nuclei/v3@latest
  check_cmd: nuclei -version
```

#### 工具注册机制
```
1. 工具配置文件放入 config/tools/ 目录
2. 系统启动时自动扫描加载
3. API 自动生成工具列表和参数表单
4. 任务执行时根据配置调用工具
```

#### 容器化工具管理（重点）

**设计背景**：部分工具安装复杂，依赖环境多，采用容器化方案便于部署和管理。

**镜像目录结构**：
```
/opt/qingscan/tools/images/          # 工具镜像 tar 存放目录
├── nuclei_v3.1.0.tar
├── xray_v1.9.0.tar
├── sqlmap_latest.tar
├── semgrep_v1.40.0.tar
└── ...
```

**配置化工具定义（支持容器模式）**：
```yaml
# tools/nuclei.yaml
name: nuclei
type: blackbox
version: "3.1.0"

# 工具运行模式：native(本地运行) | container(容器运行)
runtime: container

# 容器配置
container:
  image: nuclei:3.1.0           # 镜像名称（从 tar 导入）
  image_file: nuclei_v3.1.0.tar # tar 文件路径（相对于 tools/images/）
  tag: "3.1.0"

  # 容器运行配置
  resources:
    memory: "512m"               # 内存限制
    cpu: "1.0"                  # CPU 限制

  # 卷挂载（将目标数据传入容器）
  volumes:
    - source: /opt/qingscan/output
      target: /output
    - source: /tmp/scan
      target: /scan

  # 环境变量
  env:
    - KEY=VALUE

# 能力定义
capabilities:
  - web-vuln-scan
  - poc-scan
  - cve-detect

# 扫描参数
scan_params:
  severity:
    type: select
    options: [critical, high, medium, low, info]
    default: critical,high,medium

# 输出解析器
result_parser:
  type: json
  mapping:
    vuln_name: "info.name"
    severity: "info.severity"
    target: "matched-at"

# 镜像导入命令（首次使用）
install:
  import: docker import ${image_file} ${image}:${tag}
  check: docker run --rm ${image}:${tag} nuclei -version
```

**镜像管理服务**：
```go
// 镜像管理服务
type ImageManager struct {
    ImageDir string  // tar 文件目录
}

// 加载镜像到 Docker
func (m *ImageManager) LoadImage(tarFile string) error {
    // 1. 检查镜像是否已存在
    // 2. docker load -i xxx.tar
    // 3. 打标签
    // 4. 返回镜像信息
}

// 运行容器执行扫描
func (m *ImageManager) RunContainer(config *ContainerConfig) (*ContainerResult, error) {
    // 1. 检查镜像是否存在，不存在则先加载
    // 2. docker run [参数] 镜像名 [命令]
    // 3. 获取输出
    // 4. 清理容器
}

// 容器配置
type ContainerConfig struct {
    Image     string
    Cmd       []string
    Volumes   []VolumeMount
    Env       []string
    Memory    string
    CPU       string
    Timeout   time.Duration
}
```

**工具调用流程（容器模式）**：
```
1. 任务调度 → Worker 处理任务
2. 检查镜像是否存在
   ├── 不存在 → 从 tar 加载镜像
   └── 存在 → 继续
3. 构造容器运行参数
   ├── 挂载目标目录/文件
   ├── 设置环境变量
   └── 传入扫描参数
4. 运行容器执行扫描
5. 获取容器输出
6. 解析结果并存储
7. 清理容器
```

**tar 镜像管理命令**：
```bash
# 镜像导入（系统启动时自动扫描导入）
docker load -i /opt/qingscan/tools/images/xxx.tar

# 手动导入单个镜像
docker import xxx.tar image-name:tag

# 查看已导入镜像
docker images | grep qingscan

# 清理未使用的镜像
docker image prune -f
```

### 2.2 三大扫描系统

#### 2.2.1 黑盒扫描系统 (Black Box)

**功能定位**：外部漏洞发现，从攻击者视角模拟扫描

**支持工具分类**：
| 类别 | 工具 | 用途 |
|------|------|------|
| Web 漏洞 | Nuclei, Xray, Gxscan | POC 扫描、Web 漏洞检测 |
| 端口服务 | Nmap, Masscan | 端口扫描、服务识别 |
| 目录爬虫 | Crawlergo, Rad, Dirmap | 目录发现、URL 爬取 |
| SQL 注入 | SQLMap | SQL 注入检测 |
| 弱口令 | Hydra, Medusa | 暴力破解 |
| 敏感信息 | GitLeaks, TruffleHog | 敏感信息泄露 |

**工作流程**：
```
添加目标 → 选择扫描类型 → 配置扫描参数 → 任务入队
    ↓
Worker 调度工具执行 → 实时进度反馈 → 结果存储
    ↓
LLM 分析结果 → 生成修复建议 → 报告输出
```

#### 2.2.2 白盒审计系统 (White Box)

**功能定位**：代码层面安全审计，源码安全分析

**支持工具分类**：
| 类别 | 工具 | 用途 |
|------|------|------|
| SAST | Semgrep, CodeQL, Flawfinder | 静态代码分析 |
| SCA | Dependency-Check, OSV | 依赖漏洞检查 |
| 供应链安全 | Syft, SPDX | 软件成分分析 |
| 容器安全 | Trivy, Hadolint | 镜像/容器扫描 |
| IaC 安全 | Checkov, Terrascan | 基础设施代码扫描 |

**工作流程**：
```
代码仓库/上传代码 → 选择审计规则 → 配置扫描参数 → 执行审计
    ↓
解析分析结果 → 代码问题定位 → 漏洞影响评估
    ↓
LLM 分析代码问题 → 生成修复代码 → 报告输出
```

#### 2.2.3 资产管理系统 (Asset)

**功能定位**：企业资产发现与梳理，构建完整资产清单

**功能模块**：
| 模块 | 功能 | 工具示例 |
|------|------|----------|
| 主机发现 | IP 段扫描、资产探测 | Nmap, Masscan, Naabu |
| 域名发现 | 子域名枚举、域名爆破 | OneForAll, Subfinder |
| 指纹识别 | Web 技术指纹、服务识别 | WhatWeb, Wappalyzer, CMSeek |
| 端口扫描 | 端口服务识别、版本探测 | Nmap |
| 证书监控 | SSL 证书监控、到期提醒 | - |
| 资产监控 | 变更通知、异常告警 | - |

### 2.3 LLM 智能分析层

#### 设计目标
- 统一分析各工具的扫描结果
- 自动判断漏洞真实性、严重性
- 生成中文修复建议和解释
- 自动生成漏洞报告

#### 实现方案
```go
// LLM 分析请求结构
type LLMAnalysisRequest struct {
    Tool       string           // 工具名称
    Target     string           // 扫描目标
    RawResults []ScanResult     // 原始扫描结果
    Context    ScanContext      // 扫描上下文（目标类型、技术栈等）
}

// LLM 分析响应
type LLMAnalysisResponse struct {
    Summary         string           // 总体评估
    Vulns           []VulnAnalysis  // 漏洞分析
    RiskLevel       string           // 整体风险等级
    Recommendations []string         // 建议
    Report          string           // 完整报告（Markdown）
}
```

#### 提示词示例
```
你是一个专业的安全工程师，请分析以下 Nuclei 扫描结果：

目标：https://example.com
技术栈：PHP 7.4, Nginx, MySQL

扫描结果：
1. [critical] CVE-2021-44238 - Log4j RCE
   位置：/api/user
   描述：发现 Log4j 存在远程代码执行漏洞
2. [high] SQL Injection - /admin/login.php
   描述：发现 SQL 注入漏洞

请按以下格式分析：
1. 漏洞是否真实存在（误报分析）
2. 漏洞可利用性评估
3. 修复建议（中文）
4. 风险评级
```

---

## 三、数据模型设计

### 3.1 核心实体

```go
// 目标
type Target struct {
    ID        uint
    Name      string
    Type      string      // host | domain | url | code | container
    Value     string     // IP/域名/URL/代码路径
    System    string     // blackbox | whitebox | asset
    Status    int
    Tags      string     // 标签
    CreatedBy uint
}

// 扫描任务
type Task struct {
    ID        uint
    Name      string
    TargetID  uint
    System    string     // blackbox | whitebox | asset
    Tools     string     // 使用的工具，多个用逗号分隔
    Params    string     // JSON 格式的参数配置
    Status    int        // 0:待执行 1:执行中 2:已完成 3:失败
    Progress  int
    ResultCount int
    UserID    uint
}

// 扫描结果（统一格式）
type ScanResult struct {
    ID           uint
    TaskID       uint
    TargetID     uint
    Tool         string     // 来源工具
    VulnName     string     // 漏洞名称
    Severity     string     // critical | high | medium | low | info
    Status       int        // 0:待确认 1:确认 2:误报 3:已修复
    Target       string     // 漏洞地址
    Type         string     // 漏洞类型
    Description  string     // 描述
    Request      string     // 请求包
    Response     string     // 响应包
    Solution     string     // 修复建议（LLM 生成）
    RawResult    string     // 原始结果
}

// 工具配置
type ToolConfig struct {
    ID           uint
    Name         string     // 工具名称
    Type         string     // blackbox | whitebox | asset
    Version      string
    Path         string     // 工具路径
    ConfigPath   string     // 配置文件路径
    Status       int        // 0:未安装 1:已安装 2:异常
    Capabilities string     // JSON 数组，支持的能力
    ScanParams   string     // JSON，扫描参数配置
    InstallCmd   string     // 安装命令
}
```

### 3.2 数据库表
```sql
-- 目标表
CREATE TABLE target (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    type VARCHAR(20) NOT NULL,  -- host|domain|url|code|container
    value VARCHAR(500) NOT NULL,
    system VARCHAR(20) NOT NULL, -- blackbox|whitebox|asset
    status INT DEFAULT 1,
    tags VARCHAR(255),
    user_id BIGINT NOT NULL,
    created_at DATETIME,
    updated_at DATETIME
);

-- 扫描结果表（统一）
CREATE TABLE scan_result (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    task_id BIGINT,
    target_id BIGINT,
    tool VARCHAR(50),
    vuln_name VARCHAR(200),
    severity VARCHAR(20),  -- critical|high|medium|low|info
    status INT DEFAULT 0, -- 0:待确认 1:确认 2:误报 3:已修复
    target VARCHAR(500),
    type VARCHAR(50),
    description TEXT,
    request TEXT,
    response TEXT,
    solution TEXT,
    raw_result TEXT,
    llm_analysis TEXT,   -- LLM 分析结果
    created_at DATETIME
);
```

---

## 四、API 设计

### 4.1 任务相关 API
```
POST   /api/tasks              # 创建扫描任务
GET    /api/tasks              # 获取任务列表
GET    /api/tasks/:id          # 获取任务详情
POST   /api/tasks/:id/start    # 启动任务
POST   /api/tasks/:id/stop     # 停止任务
DELETE /api/tasks/:id          # 删除任务
GET    /api/tasks/:id/results  # 获取任务结果
```

### 4.2 目标相关 API
```
POST   /api/targets            # 添加目标
GET    /api/targets            # 获取目标列表
GET    /api/targets/:id        # 获取目标详情
PUT    /api/targets/:id        # 更新目标
DELETE /api/targets/:id        # 删除目标
```

### 4.3 工具相关 API
```
GET    /api/tools              # 获取工具列表
GET    /api/tools/:name        # 获取工具详情
POST   /api/tools/:name/check  # 检查工具状态
GET    /api/tools/:name/params # 获取工具参数配置
```

### 4.4 LLM 分析 API
```
POST   /api/llm/analyze        # 分析扫描结果
POST   /api/llm/report         # 生成报告
POST   /api/llm/recommend      # 获取修复建议
```

---

## 五、工具接入流程（零代码修改）

### 5.1 新工具接入步骤
```
1. 下载/安装工具到 /opt/tools/ 目录
2. 在 config/tools/ 目录下创建工具配置文件（YAML）
3. 重启服务，工具自动加载
4. 在 UI 上配置工具参数并使用
```

### 5.2 配置文件示例：Semgrep（白盒工具）
```yaml
# config/tools/semgrep.yaml
name: semgrep
type: whitebox
version: "1.x"
path: /opt/tools/semgrep

capabilities:
  - code-scan
  - vuln-detect
  - best-practice

scan_params:
  rules:
    type: select
    options: [auto, security, correctness]
    default: auto

  scan_mode:
    type: select
    options: [deep, shallow]
    default: shallow

  json_output:
    type: boolean
    default: true

result_parser:
  type: json
  mapping:
    severity: extra.severity
    line: start.line
    message: extra.message

install:
  cmd: pip install semgrep
  check_cmd: semgrep --version
```

---

## 六、LLM 集成方案

### 6.1 支持的 LLM
- OpenAI GPT-4/GPT-3.5
- Anthropic Claude
- 本地部署 Llama2 / CodeLlama
- 其他兼容 OpenAI API 的模型

### 6.2 LLM 功能
```go
type LLMConfig struct {
    Provider   string  // openai | anthropic | local
    APIKey     string
    Model      string
    Endpoint   string  // 自定义端点（可选）
}

// 分析单个漏洞
func (s *LLMService) AnalyzeVuln(result *ScanResult) (*VulnAnalysis, error)

// 批量分析扫描结果
func (s *LLMService) AnalyzeResults(results []ScanResult) (*AnalysisReport, error)

// 生成修复建议
func (s *LLMService) GenerateFixSuggestion(vuln *ScanResult) (string, error)

// 生成完整报告
func (s *LLMService) GenerateReport(task *Task, results []ScanResult) (string, error)
```

---

## 七、后续扩展

### 7.1 计划接入的工具

**黑盒扫描**：
- [ ] Xray (Web 漏洞扫描)
- [ ] Gxscan (漏洞扫描)
- [ ] Kscan (端口扫描)
- [ ] GoByPass (bypass 工具)
- [ ] W9scan (综合扫描)
- [ ] Afrog (POC 扫描)

**白盒审计**：
- [ ] CodeQL
- [ ] Fortify
- [ ] SonarQube
- [ ] Checkmarx
- [ ] Snyk
- [ ] MurphySec

**资产管理**：
- [ ] OneForAll (子域名)
- [ ] Subfinder (子域名)
- [ ] Masscan (端口)
- [ ] Naabu (端口扫描)
- [ ] httpx (指纹识别)
- [ ] WhatWeb (指纹识别)

### 7.2 功能扩展
- [ ] WebSocket 实时进度
- [ ] 分布式扫描支持
- [ ] 扫描任务定时调度
- [ ] 扫描结果邮件/钉钉通知
- [ ] 自定义报告模板
- [ ] 漏洞复现验证
- [ ] CVE 漏洞库集成
- [ ] 多租户支持

---

## 八、部署架构

### 8.1 标准部署（Docker Compose）

```yaml
# docker-compose.yaml
version: '3.8'

services:
  qingscan:
    build: .
    image: qingscan:latest
    container_name: qingscan
    ports:
      - "8080:8080"
    volumes:
      - ./config:/opt/qingscan/config        # 配置文件
      - ./tools:/opt/qingscan/tools           # 工具目录
      - ./tools/images:/opt/qingscan/tools/images  # 镜像 tar 文件
      - ./output:/var/qingscan/output        # 扫描结果输出
      - /var/run/docker.sock:/var/run/docker.sock  # Docker _socket（用于运行工具容器）
    environment:
      - DATABASE_HOST=mysql
      - REDIS_HOST=redis
    depends_on:
      - mysql
      - redis
    networks:
      - qingscan

  mysql:
    image: mysql:8.0
    container_name: qingscan-mysql
    environment:
      MYSQL_ROOT_PASSWORD: root
      MYSQL_DATABASE: qingscan
    volumes:
      - mysql_data:/var/lib/mysql
    networks:
      - qingscan

  redis:
    image: redis:7
    container_name: qingscan-redis
    volumes:
      - redis_data:/data
    networks:
      - qingscan

volumes:
  mysql_data:
  redis_data:

networks:
  qingscan:
    driver: bridge
```

### 8.2 目录结构

```
/opt/qingscan/
├── qingscan                    # 主程序（可选，镜像内已包含）
├── config.yaml                 # 主配置文件
├── config/
│   ├── tools/                  # 工具配置文件
│   │   ├── nuclei.yaml
│   │   ├── xray.yaml
│   │   └── semgrep.yaml
│   └── database/               # 数据库配置
├── tools/                      # 工具目录
│   ├── nuclei                  # 可执行工具（native 模式）
│   ├── sqlmap/
│   └── images/                 # ★ 镜像 tar 文件目录
│       ├── nuclei_v3.1.0.tar
│       ├── xray_v1.9.0.tar
│       ├── sqlmap_latest.tar
│       └── semgrep_v1.40.0.tar
└── output/                     # 扫描结果输出目录
```

### 8.3 工具镜像管理

**首次部署**：
```bash
# 1. 启动基础服务
docker-compose up -d mysql redis

# 2. 启动 qingscan（会自动创建网络、加载镜像）
docker-compose up -d qingscan

# 或手动加载镜像后再启动
cd /opt/qingscan/tools/images
for f in *.tar; do docker load -i "$f"; done
docker-compose up -d
```

**工具镜像更新**：
```bash
# 1. 停止 qingscan
docker-compose stop qingscan

# 2. 替换新的 tar 文件
cp /path/to/new_nuclei.tar ./tools/images/

# 3. 重新加载镜像
docker load -i ./tools/images/new_nuclei.tar

# 4. 重启 qingscan
docker-compose restart qingscan
```

### 8.4 Docker in Docker 说明

系统需要访问宿主机的 Docker 来运行工具容器，有两种方案：

**方案 A：挂载 Docker Socket（推荐）**
```yaml
volumes:
  - /var/run/docker.sock:/var/run/docker.sock
```
- 优点：简单，容器内可直接使用 docker 命令
- 缺点：容器有宿主机 Docker 完整权限

**方案 B：独立 Docker 服务**
```yaml
# 在 qingscan 容器内安装 Docker CLI
# 通过 TCP 连接宿主机 Docker API
environment:
  - DOCKER_HOST=tcp://host.docker.internal:2375
```

### 8.5 资源规划建议

| 组件 | CPU | 内存 | 磁盘 |
|------|-----|------|------|
| qingscan | 2核 | 2GB | - |
| MySQL | 1核 | 1GB | 50GB+ |
| Redis | 0.5核 | 512MB | - |
| 工具容器 | 按需 | 按工具配置 | - |

**注意**：工具容器运行时会占用额外资源，建议：
- 根据并发扫描任务数调整 qingscan 资源
- 限制单个工具容器内存（512MB~1GB）
- 监控 Docker 宿主机资源使用

---

## 九、总结

本系统采用**配置化驱动**的设计理念，新工具接入只需：
1. 放置工具到指定目录
2. 编写配置文件
3. 重启服务

无需修改任何代码，真正实现**热插拔**。

LLM 的引入让扫描结果不再是冰冷的数据，而是智能的分析报告，大幅提升安全团队效率。

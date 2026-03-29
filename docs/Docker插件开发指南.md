# Docker 插件开发指南

> 本文档面向工具插件开发者，帮助你快速封装安全工具为 Docker 镜像并对接平台。

## 一分钟快速开始

### 你需要做的事

1. **写一个入口脚本** - 接收参数、执行扫描、回调结果
2. **写一个 Dockerfile** - 打包工具和依赖
3. **配置数据库** - 填写启动命令

### 平台提供的能力

- 任务启动时传递 `task_id`、`target`、`callback_url`
- 接收你的回调通知（状态、结果）
- **不关心数据格式** - 想传什么传什么

---

## 核心对接规范

### 1. 入口脚本参数

| 参数 | 必填 | 说明 | 示例 |
|------|------|------|------|
| `--task-id` | 是 | 任务ID | `123` |
| `--callback` | 是 | 回调地址 | `http://host:8081/api/callback` |
| `--target` | 是 | 扫描目标 | `http://example.com?id=1` |

### 2. 回调接口

#### 状态更新 - POST `/api/callback/status`

```json
{
  "task_id": 123,
  "status": "running",
  "progress": 50,
  "message": "正在扫描..."
}
```

| 字段 | 必填 | 说明 |
|------|------|------|
| task_id | 是 | 任务ID |
| status | 是 | `running` / `completed` / `failed` |
| progress | 否 | 进度 0-100 |
| message | 否 | 状态消息 |

#### 结果提交 - POST `/api/callback/result`

```json
{
  "task_id": 123,
  "status": "completed",
  "data": "任意内容",
  "data_type": "asset"
}
```

| 字段 | 必填 | 说明 |
|------|------|------|
| task_id | 是 | 任务ID |
| status | 是 | `completed` / `failed` |
| data | 否 | 任意数据：字符串、JSON、数组、对象... |
| data_type | 否 | 数据类型标识，自定义，如 `raw`、`json`、`asset`、`vuln` |
| message | 否 | 失败时的错误信息 |

---

## 完整示例

### 目录结构

```
docker/sqlmap/
├── Dockerfile        # 镜像构建
├── entrypoint.py     # 入口脚本
└── config.yaml       # 工具配置
```

### config.yaml

```yaml
name: sqlmap
label: SQLMap
description: SQL注入检测工具
image: daxia/qingscan-sqlmap:latest
command: docker run --rm --network qingscan-net {image} --task-id={task_id} --callback={callback_url} --target={target}
enabled: true
```

| 字段 | 必填 | 说明 |
|------|------|------|
| name | 是 | 工具唯一标识 |
| label | 是 | 显示名称 |
| description | 否 | 工具描述 |
| image | 是 | Docker镜像地址 |
| command | 是 | 启动命令，`{image}`会被替换为image字段 |
| enabled | 否 | 是否启用，默认true |

### Dockerfile

```dockerfile
FROM python:3.11-slim

RUN pip install --no-cache-dir requests

COPY sqlmap.tar.gz /tmp/
RUN tar -xzf /tmp/sqlmap.tar.gz -C /opt && rm /tmp/sqlmap.tar.gz

COPY entrypoint.py /opt/entrypoint.py
RUN chmod +x /opt/entrypoint.py

WORKDIR /opt
ENTRYPOINT ["python3", "/opt/entrypoint.py"]
```

### entrypoint.py

```python
#!/usr/bin/env python3
import argparse
import subprocess
import requests

def main():
    parser = argparse.ArgumentParser()
    parser.add_argument("--task-id", required=True, type=int)
    parser.add_argument("--callback", required=True)
    parser.add_argument("--target", required=True)
    args = parser.parse_args()

    # 1. 启动通知
    requests.post(f"{args.callback}/status", json={
        "task_id": args.task_id,
        "status": "running",
        "progress": 0
    })

    try:
        # 2. 执行扫描
        output = subprocess.check_output([
            "python3", "/opt/sqlmap/sqlmap.py",
            "-u", args.target, "--batch"
        ], timeout=300).decode()

        # 3. 提交结果
        requests.post(f"{args.callback}/result", json={
            "task_id": args.task_id,
            "status": "completed",
            "data": output,
            "data_type": "raw"
        })

    except Exception as e:
        requests.post(f"{args.callback}/result", json={
            "task_id": args.task_id,
            "status": "failed",
            "message": str(e)
        })

if __name__ == "__main__":
    main()
```

### 数据库配置

```sql
INSERT INTO scan_tool (tool_name, tool_label, description, start_command, is_enabled)
VALUES (
    'sqlmap',
    'SQLMap',
    'SQL注入检测工具',
    'docker run --rm --network qingscan-net daxia/qingscan-sqlmap:latest --task-id={task_id} --callback={callback_url} --target={target}',
    1
);
```

## 已发布镜像列表

| 工具 | 镜像地址 | 大小 |
|------|----------|------|
| SQLMap | daxia/qingscan-sqlmap:latest | 171MB |
| Fscan | daxia/qingscan-fscan:latest | 85.2MB |
| Nmap | daxia/qingscan-nmap:latest | 168MB |
| Nuclei | daxia/qingscan-nuclei:latest | 320MB |
| Gobuster | daxia/qingscan-gobuster:latest | 155MB |
| Xray | daxia/qingscan-xray:latest | 213MB |
| Afrog | daxia/qingscan-afrog:latest | 1.6GB |
| Subfinder | daxia/qingscan-subfinder:latest | 1.21GB |
| Httpx | daxia/qingscan-httpx:latest | 192MB |
| Dismap | daxia/qingscan-dismap:latest | 137MB |
| Kunpeng | daxia/qingscan-kunpeng:latest | 191MB |

---

## data 示例

传什么都行：

```json
// 纯文本
{"data": "扫描完成，发现3个开放端口"}

// JSON 对象
{"data": {"vulns": [...], "count": 5}, "data_type": "vuln"}

// 数组
{"data": ["host1.com", "host2.com"], "data_type": "asset"}

// 嵌套结构
{"data": {"scan": {"target": "...", "results": [...]}}}
```

---

## JSON 输出建议（推荐但不强制）

**推荐**：工具优先使用 JSON 格式输出，便于后续解析和展示。

**示例**：

```python
# 推荐：结构化 JSON
result = {
    "task_id": task_id,
    "status": "completed",
    "data": {
        "vulnerabilities": [...],
        "count": 5
    },
    "data_type": "vuln"
}

# 也可以：原始文本
result = {
    "task_id": task_id,
    "status": "completed",
    "data": "fscan scan output...\nport 80 open",
    "data_type": "raw"
}
```

**data_type 常用值**：

| 值 | 说明 |
|---|---|
| `raw` | 原始文本（默认） |
| `vuln` | 漏洞数据 |
| `asset` | 资产数据 |
| `subdomain` | 子域名列表 |
| `port` | 端口扫描结果 |
| 自定义 | 任意字符串 |

---

## 开发检查清单

- [ ] 支持 `--task-id`、`--callback`、`--target` 参数
- [ ] 启动时调用 `/status` 回调
- [ ] 完成后调用 `/result` 回调
- [ ] 失败时调用 `/result` 回调（status=failed, message=错误信息）
- [ ] Dockerfile ENTRYPOINT 指向入口脚本
- [ ] 创建 `config.yaml` 配置文件

---

## 自动加载工具

从 docker/*/config.yaml 加载工具配置到数据库：

```bash
cd /opt/qingscan/docker
./load-tools.sh
```

输出：
```
=== Load tools ===
Done
```

加载逻辑：工具存在则更新，不存在则插入。

---

## 模板文件

见 `docker/sqlmap/` 目录

---

## 快速验证脚本

```bash
# 一键验证插件是否对接成功
./docker/test-plugin.sh <镜像名> <目标>

# 示例
./docker/test-plugin.sh daxia/qingscan-fscan:latest http://testphp.vulnweb.com
```

成功输出：
```json
{"success":true,"saved_count":1}
```

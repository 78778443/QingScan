# SQLMap Docker 封装样例

## 说明

这是一个将SQLMap封装为Docker镜像的样例，验证工具Docker化的稳定性。

**核心思路：平台不关心工具如何运行，只负责执行启动命令。**

## 构建镜像

```bash
cd docker/sqlmap
docker build -t qingscan/sqlmap:latest .
```

## 测试运行

```bash
# 手动测试
docker run --rm --network host qingscan/sqlmap:latest \
    --task-id=1 \
    --callback=http://localhost:8000/api/callback \
    --target="http://testphp.vulnweb.com/artists.php?artist=1"
```

## 平台配置

在 `scan_tool` 表中插入：

```sql
INSERT INTO `scan_tool` (`tool_name`, `display_name`, `description`, `start_command`, `is_enabled`)
VALUES (
    'sqlmap',
    'SQLMap (Docker)',
    'SQL注入自动化检测工具',
    'docker run --rm --network host qingscan/sqlmap:latest --task-id={task_id} --callback={callback_url} --target={target}',
    1
);
```

## 流程图

```
平台                        Docker容器
  │                              │
  │  docker run ...              │
  │ ─────────────────────────────>
  │                              │
  │                      执行SQLMap扫描
  │                              │
  │  POST /api/callback/status   │
  │ <─────────────────────────────
  │                              │
  │  POST /api/callback/result   │
  │ <─────────────────────────────
  │                              │
  v                              v
```

## 优势

1. **环境隔离** - 每个工具独立环境，无依赖冲突
2. **版本稳定** - 镜像固定版本，行为一致
3. **平台无感** - 平台只执行命令，不关心实现
4. **易于扩展** - 新工具只需制作镜像，配置启动命令

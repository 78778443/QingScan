# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

QingScan is a security scanning platform built on ThinkPHP 8 that orchestrates multiple security scanning tools (SQLMap, Nmap, Nuclei, Xray, Afrog, etc.) through a unified plugin architecture.

## Architecture

### Platform + Plugin (Callback) Pattern

```
Platform (ThinkPHP 8) --starts--> Plugin (Docker/script)
Plugin --callbacks--> Platform (status + results)
```

- **Platform** handles: task scheduling, target management, receiving callbacks, storing results, LLM analysis
- **Plugins** handle: the actual scanning, reporting status/results back via HTTP callbacks
- Data formats are free-form — the platform stores whatever the plugin sends

### Database Tables

| Table | Purpose |
|-------|---------|
| `scan_tool` | Tool configurations with start_command templates and output_parse rules |
| `scan_target` | Scan targets (URLs, IPs, domains) |
| `scan_task` | Scan task lifecycle tracking (pending/running/success/failed) |
| `scan_result` | Raw scan results stored as-is from plugins |
| `llm_analysis` | LLM-generated analysis of scan results |

### Key Files

| File | Purpose |
|------|---------|
| `code/app/controller/Task.php` | Task API (create, status, results) |
| `code/app/controller/Callback.php` | Plugin callback endpoints (status, result) |
| `code/app/service/TaskRunner.php` | Starts plugin processes (Docker or script mode) |
| `code/app/command/target.php` | CLI scanner (`php think scan`) |
| `code/app/command/LoadTools.php` | Load tools from ToolsCode/*/config.yaml |
| `code/app/model/ScanTool.php` | Tool model with output_parse regex rules |
| `code/app/model/ScanTask.php` | Task lifecycle (start, complete, fail) |
| `code/app/model/LlmAnalysis.php` | LLM analysis with risk level calculation |
| `code/database/qingscan.sql` | Full database schema |
| `docs/Docker插件开发指南.md` | Plugin developer guide |
| `docs/工具回调方案设计.md` | Callback architecture design doc |

### Tool Integration

Each tool in `code/ToolsCode/<tool>/` has a `config.yaml`:

```yaml
name: sqlmap
label: SQLMap
description: SQL注入检测工具
image: daxia/qingscan-sqlmap:latest
command: docker run --rm --network qingscan-net {image} --task-id={task_id} --callback={callback_url} --target={target}
enabled: true
```

## CLI Usage

```bash
# Initialize tools to database (load from ToolsCode/*/config.yaml)
php think tools:load

# List available tools
php think scan --list

# Run a scan
php think scan -t "http://example.com/test.php?id=1" -s sqlmap

# View tasks and results
php think scan --tasks
php think scan --status <task_id>
php think scan --results <task_id>
php think scan --analyze <task_id>

# Start dev server
php think run
```

#!/usr/bin/env python3
"""
SQLMap Docker 封装脚本
负责：执行扫描 → 回调结果
"""
import argparse
import json
import os
import subprocess
import sys
import time
import requests


def send_status(callback_url: str, task_id: int, status: str, progress: int = 0, message: str = ""):
    """发送状态回调"""
    try:
        url = f"{callback_url}/status"
        resp = requests.post(url, json={
            "task_id": task_id,
            "status": status,
            "progress": progress,
            "message": message
        }, timeout=10)
        print(f"[DEBUG] 状态回调: {resp.status_code}")
    except Exception as e:
        print(f"[WARN] 状态回调失败: {e}")


def send_result(callback_url: str, task_id: int, status: str, data=None, message: str = ""):
    """发送结果回调"""
    try:
        url = f"{callback_url}/result"
        payload = {
            "task_id": task_id,
            "status": status,
        }
        if data is not None:
            payload["data"] = data
            payload["data_type"] = "vuln"
        if message:
            payload["message"] = message

        resp = requests.post(url, json=payload, timeout=30)
        print(f"[DEBUG] 结果回调: {resp.status_code} - {resp.text[:100]}")
    except Exception as e:
        print(f"[ERROR] 结果回调失败: {e}")


def run_sqlmap(target: str, output_dir: str) -> dict:
    """
    执行SQLMap扫描
    返回: {"success": bool, "output": str, "vulns": list}
    """
    sqlmap_path = "/opt/sqlmap/sqlmap.py"

    cmd = [
        "python3", sqlmap_path,
        "-u", target,
        "--batch",
        "--random-agent",
        f"--output-dir={output_dir}",
        "--level=1",
        "--risk=1",
    ]

    print(f"[INFO] 执行命令: {' '.join(cmd)}")

    result = subprocess.run(
        cmd,
        capture_output=True,
        text=True,
        timeout=300
    )

    output = result.stdout + result.stderr
    print(f"[DEBUG] SQLMap输出:\n{output[:2000]}")

    # 解析漏洞信息
    vulns = parse_sqlmap_output(output, output_dir)

    return {
        "success": True,
        "output": output,
        "vulns": vulns
    }


def parse_sqlmap_output(output: str, output_dir: str) -> list:
    """解析SQLMap输出，提取漏洞信息"""
    results = []

    # 从标准输出解析
    if "injectable" in output.lower() or "sql injection" in output.lower():
        lines = output.split('\n')
        vuln_info = {
            "vuln_type": "sqli",
            "vuln_level": "high",
            "vuln_title": "SQL注入漏洞",
            "target": "",
            "parameter": "",
            "technique": ""
        }

        for line in lines:
            line_lower = line.lower()
            if 'parameter' in line_lower and ('injectable' in line_lower or 'appears' in line_lower):
                vuln_info["parameter"] = line.strip()
            if 'type' in line_lower and 'boolean' in line_lower or 'time-based' in line_lower or 'error-based' in line_lower:
                vuln_info["technique"] = line.strip()
            if 'GET parameter' in line or 'POST parameter' in line:
                vuln_info["target"] = line.strip()

        results.append(vuln_info)

    # 从日志文件解析
    log_dir = os.path.join(output_dir, "output")
    if os.path.exists(log_dir):
        for root, dirs, files in os.walk(log_dir):
            for f in files:
                if f.endswith('.log') or f.endswith('.json'):
                    try:
                        filepath = os.path.join(root, f)
                        with open(filepath, 'r') as fp:
                            content = fp.read()
                            if 'injectable' in content.lower() and not results:
                                results.append({
                                    "vuln_type": "sqli",
                                    "vuln_level": "high",
                                    "vuln_title": "SQL注入漏洞",
                                    "detail": f"检测到注入点，详见: {f}"
                                })
                    except Exception as e:
                        print(f"[WARN] 读取文件失败: {e}")

    return results


def main():
    parser = argparse.ArgumentParser(description="SQLMap Docker Scanner")
    parser.add_argument("--task-id", required=True, type=int, help="任务ID")
    parser.add_argument("--callback", required=True, help="回调地址")
    parser.add_argument("--target", required=True, help="扫描目标URL")

    args = parser.parse_args()

    task_id = args.task_id
    callback_url = args.callback
    target = args.target

    print(f"=" * 50)
    print(f"[INFO] SQLMap Docker Scanner")
    print(f"[INFO] 任务ID: {task_id}")
    print(f"[INFO] 目标: {target}")
    print(f"[INFO] 回调: {callback_url}")
    print(f"=" * 50)

    output_dir = f"/tmp/scan_{task_id}"
    os.makedirs(output_dir, exist_ok=True)

    # 1. 发送启动状态
    send_status(callback_url, task_id, "running", 0, "开始SQLMap扫描")

    try:
        # 2. 执行扫描
        send_status(callback_url, task_id, "running", 30, "执行SQLMap检测中...")
        result = run_sqlmap(target, output_dir)

        # 3. 发送结果
        send_status(callback_url, task_id, "running", 90, "扫描完成，提交结果")

        if result["vulns"]:
            # 发送漏洞数据
            send_result(callback_url, task_id, "completed", {
                "found": True,
                "count": len(result["vulns"]),
                "vulns": result["vulns"],
                "raw_output": result["output"][:5000]
            })
            print(f"[INFO] 扫描完成，发现 {len(result['vulns'])} 个漏洞")
        else:
            # 无漏洞
            send_result(callback_url, task_id, "completed", {
                "found": False,
                "count": 0,
                "raw_output": result["output"][:2000]
            })
            print(f"[INFO] 扫描完成，未发现漏洞")

    except subprocess.TimeoutExpired:
        send_result(callback_url, task_id, "failed", message="扫描超时(5分钟)")
    except Exception as e:
        import traceback
        print(f"[ERROR] 执行异常: {e}")
        traceback.print_exc()
        send_result(callback_url, task_id, "failed", message=str(e))


if __name__ == "__main__":
    main()

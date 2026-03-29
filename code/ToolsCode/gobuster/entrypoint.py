#!/usr/bin/env python3
"""
Gobuster Docker 封装脚本
负责：执行目录扫描 - 回调结果
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
            payload["data_type"] = "dir_scan"
        if message:
            payload["message"] = message

        resp = requests.post(url, json=payload, timeout=30)
        print(f"[DEBUG] 结果回调: {resp.status_code} - {resp.text[:200]}")
        return resp.status_code == 200
    except Exception as e:
        print(f"[ERROR] 结果回调失败: {e}")
        return False


def run_gobuster(target: str, output_file: str) -> dict:
    """
    执行Gobuster目录扫描
    返回: {"success": bool, "output": str, "dirs": list}
    """
    gobuster_path = "/usr/local/bin/gobuster"
    
    wordlist = "/opt/common.txt"
    
    common_dirs = [
        "admin", "login", "api", "config", "backup", "data", "db",
        "uploads", "images", "css", "js", "assets", "static",
        "files", "docs", "test", "debug", "tmp", "logs",
        "wp-admin", "wp-content", "wp-includes", "phpmyadmin",
        ".git", ".env", ".svn", "robots.txt", "sitemap.xml"
    ]
    
    with open(wordlist, "w") as f:
        f.write("\n".join(common_dirs))
    
    cmd = [
        gobuster_path, "dir",
        "-u", target,
        "-w", wordlist,
        "-o", output_file,
        "-q",
        "--timeout", "10s",
        "-t", "10"
    ]
    
    cmd_str = " ".join(cmd)
    print(f"[INFO] 执行命令: {cmd_str}")
    
    try:
        result = subprocess.run(
            cmd,
            capture_output=True,
            text=True,
            timeout=120
        )
        
        output = result.stdout + result.stderr
        print(f"[DEBUG] Gobuster输出:\n{output[:2000]}")
        
        dirs = parse_gobuster_output(output_file)
        
        return {
            "success": True,
            "output": output,
            "dirs": dirs
        }
    except subprocess.TimeoutExpired:
        return {
            "success": False,
            "output": "扫描超时",
            "dirs": []
        }
    except Exception as e:
        return {
            "success": False,
            "output": str(e),
            "dirs": []
        }


def parse_gobuster_output(output_file: str) -> list:
    """解析Gobuster输出，提取目录信息"""
    results = []
    
    if os.path.exists(output_file):
        with open(output_file, "r") as f:
            for line in f:
                line = line.strip()
                if line and not line.startswith("#"):
                    parts = line.split()
                    if len(parts) >= 2:
                        path = parts[0]
                        status = ""
                        size = ""
                        for i, p in enumerate(parts):
                            if "Status:" in p:
                                status = parts[i+1].strip(")") if i+1 < len(parts) else ""
                            if "Size:" in p:
                                size = parts[i+1].strip("]") if i+1 < len(parts) else ""
                        
                        results.append({
                            "path": path,
                            "status": status,
                            "size": size
                        })
    
    return results


def main():
    parser = argparse.ArgumentParser(description="Gobuster Docker Scanner")
    parser.add_argument("--task-id", required=True, type=int, help="任务ID")
    parser.add_argument("--callback", required=True, help="回调地址")
    parser.add_argument("--target", required=True, help="扫描目标URL")

    args = parser.parse_args()

    task_id = args.task_id
    callback_url = args.callback
    target = args.target

    print(f"=" * 50)
    print(f"[INFO] Gobuster Docker Scanner")
    print(f"[INFO] 任务ID: {task_id}")
    print(f"[INFO] 目标: {target}")
    print(f"[INFO] 回调: {callback_url}")
    print(f"=" * 50)

    output_dir = f"/tmp/scan_{task_id}"
    os.makedirs(output_dir, exist_ok=True)
    output_file = os.path.join(output_dir, "gobuster.txt")

    send_status(callback_url, task_id, "running", 0, "开始Gobuster目录扫描")

    try:
        send_status(callback_url, task_id, "running", 30, "执行目录扫描中...")
        result = run_gobuster(target, output_file)

        send_status(callback_url, task_id, "running", 90, "扫描完成，提交结果")

        if result["success"]:
            scan_data = {
                "found": len(result["dirs"]) > 0,
                "count": len(result["dirs"]),
                "dirs": result["dirs"],
                "raw_output": result["output"][:3000]
            }
            success = send_result(callback_url, task_id, "completed", scan_data)
            if success:
                print(f"[INFO] 回调成功，发现 {len(result['dirs'])} 个目录")
            else:
                print(f"[WARN] 回调可能失败")
        else:
            send_result(callback_url, task_id, "failed", message=result["output"])

    except Exception as e:
        import traceback
        print(f"[ERROR] 执行异常: {e}")
        traceback.print_exc()
        send_result(callback_url, task_id, "failed", message=str(e))


if __name__ == "__main__":
    main()

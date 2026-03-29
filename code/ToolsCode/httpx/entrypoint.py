#!/usr/bin/env python3
import argparse
import subprocess
import json
import requests
import sys
from datetime import datetime

def run_httpx(target):
    """运行 httpx 探测目标"""
    try:
        # 使用 projectdiscovery httpx 命令行工具
        result = subprocess.run(
            ["httpx", "-u", target, "-json", "-silent", "-status-code", "-title", "-tech-detect"],
            capture_output=True,
            text=True,
            timeout=60
        )
        
        stdout_preview = result.stdout[:500] if result.stdout else "empty"
        stderr_preview = result.stderr[:500] if result.stderr else "empty"
        print(f"[*] httpx stdout: {stdout_preview}")
        print(f"[*] httpx stderr: {stderr_preview}")
        
        if result.returncode == 0 and result.stdout.strip():
            lines = result.stdout.strip().split("\n")
            probe_results = []
            for line in lines:
                if line.strip():
                    try:
                        probe_results.append(json.loads(line))
                    except json.JSONDecodeError:
                        pass
            
            if probe_results:
                return {
                    "success": True,
                    "target": target,
                    "results": probe_results,
                    "timestamp": datetime.now().isoformat()
                }
        
        # 如果 httpx 没有返回结果，使用简单的 HTTP 请求
        return fallback_probe(target)
            
    except subprocess.TimeoutExpired:
        return {
            "success": False,
            "target": target,
            "error": "httpx timeout",
            "timestamp": datetime.now().isoformat()
        }
    except Exception as e:
        return {
            "success": False,
            "target": target,
            "error": str(e),
            "timestamp": datetime.now().isoformat()
        }

def fallback_probe(target):
    """备用探测方法"""
    try:
        import httpx as httpx_lib
        with httpx_lib.Client(timeout=30, verify=False) as client:
            resp = client.get(target)
            return {
                "success": True,
                "target": target,
                "results": [{
                    "url": str(resp.url),
                    "status_code": resp.status_code,
                    "title": "",
                    "content_length": len(resp.content),
                }],
                "timestamp": datetime.now().isoformat()
            }
    except Exception as e:
        return {
            "success": False,
            "target": target,
            "error": "Fallback probe failed: " + str(e),
            "timestamp": datetime.now().isoformat()
        }

def send_callback(callback_url, task_id, result_data):
    """发送回调结果到 /api/callback/result 接口"""
    # 适配现有的回调接口格式
    status = "completed" if result_data.get("success") else "failed"
    
    task_id_int = int(task_id) if task_id.isdigit() else task_id
    payload = {
        "task_id": task_id_int,
        "status": status,
        "data": result_data,
        "data_type": "httpx_probe"
    }
    
    # 使用 /api/callback/result 作为回调地址
    full_callback_url = callback_url.rstrip("/") + "/result"
    
    print(f"[*] Sending callback to: {full_callback_url}")
    payload_str = json.dumps(payload, indent=2, ensure_ascii=False)[:500]
    print(f"[*] Payload: {payload_str}")
    
    try:
        response = requests.post(
            full_callback_url,
            json=payload,
            timeout=30
        )
        
        # 检查响应是否包含成功标志
        response_text = response.text if response.text else ""
        is_success = '"success":true' in response_text or response.status_code == 200
        
        return {
            "success": is_success,
            "status_code": response.status_code,
            "response": response_text[:500]
        }
    except Exception as e:
        return {
            "success": False,
            "error": str(e)
        }

def main():
    parser = argparse.ArgumentParser(description="HTTP Probe Tool with Callback")
    parser.add_argument("--task-id", required=True, help="Task ID for tracking")
    parser.add_argument("--callback", required=True, help="Callback URL base (e.g., http://host/api/callback)")
    parser.add_argument("--target", required=True, help="Target URL to probe")
    
    args = parser.parse_args()
    
    print(f"[*] Starting HTTP probe for task: {args.task_id}")
    print(f"[*] Target: {args.target}")
    print(f"[*] Callback: {args.callback}")
    
    # 执行探测
    probe_result = run_httpx(args.target)
    print(f"[*] Probe result: {json.dumps(probe_result, indent=2, ensure_ascii=False)}")
    
    # 发送回调
    callback_result = send_callback(args.callback, args.task_id, probe_result)
    
    if callback_result.get("success"):
        print(f"[+] Callback successful!")
        print(f"[+] Response: {callback_result}")
    else:
        print(f"[-] Callback failed: {callback_result}")
        sys.exit(1)
    
    print("[+] Task completed successfully")

if __name__ == "__main__":
    main()

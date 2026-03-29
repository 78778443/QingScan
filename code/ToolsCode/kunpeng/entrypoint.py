#!/usr/bin/env python3
import argparse
import json
import requests
import sys
import ctypes
from ctypes import c_char_p

def main():
    parser = argparse.ArgumentParser(description="Kunpeng vulnerability scanner")
    parser.add_argument("--task-id", required=True, help="Task ID")
    parser.add_argument("--callback", required=True, help="Callback URL")
    parser.add_argument("--target", required=True, help="Target URL to scan")
    args = parser.parse_args()

    print(f"[*] Starting Kunpeng scan...")
    print(f"[*] Task ID: {args.task_id}")
    print(f"[*] Target: {args.target}")
    print(f"[*] Callback: {args.callback}")

    # 加载 kunpeng 库 (非Go语言使用 kunpeng_c.so)
    try:
        kunpeng = ctypes.cdll.LoadLibrary("/app/kunpeng_c.so")
        kunpeng.GetPlugins.restype = c_char_p
        kunpeng.Check.argtypes = [c_char_p]
        kunpeng.Check.restype = c_char_p
        kunpeng.SetConfig.argtypes = [c_char_p]
        kunpeng.GetVersion.restype = c_char_p
        print(f"[*] Kunpeng library loaded successfully")
        print(f"[*] Version: {kunpeng.GetVersion().decode()}")
    except Exception as e:
        print(f"[!] Failed to load kunpeng library: {e}")
        return False

    # 设置配置
    try:
        config = json.dumps({
            "timeout": 10,
        })
        kunpeng.SetConfig(config.encode("utf-8"))
        print(f"[*] Config set successfully")
    except Exception as e:
        print(f"[!] Failed to set config: {e}")

    # 执行扫描
    vulnerabilities = []
    try:
        # 构建扫描目标 JSON
        task = {
            "type": "web",
            "netloc": args.target,
            "target": "web"  # 使用 web 类型进行通用 web 扫描
        }
        task_json = json.dumps(task)
        print(f"[*] Scanning target: {task_json}")
        
        result = kunpeng.Check(task_json.encode("utf-8"))
        if result:
            result_str = result.decode("utf-8")
            print(f"[*] Scan result: {result_str}")
            
            # 解析结果
            try:
                vulns = json.loads(result_str)
                if isinstance(vulns, list):
                    vulnerabilities = vulns
                elif isinstance(vulns, dict):
                    vulnerabilities = [vulns]
            except json.JSONDecodeError:
                pass
    except Exception as e:
        print(f"[!] Scan failed: {e}")
        import traceback
        traceback.print_exc()

    # 构建回调数据
    callback_data = {
        "task_id": int(args.task_id),
        "status": "completed",
        "data": {
            "vulnerabilities": vulnerabilities,
            "count": len(vulnerabilities)
        },
        "data_type": "vuln"
    }

    # 发送回调
    print(f"[*] Sending callback to {args.callback}")
    try:
        response = requests.post(
            args.callback + "/result",
            json=callback_data,
            timeout=30
        )
        print(f"[*] Callback response: {response.status_code}")
        print(f"[*] Response body: {response.text}")
        return response.status_code == 200
    except Exception as e:
        print(f"[!] Callback failed: {e}")
        import traceback
        traceback.print_exc()
        return False

if __name__ == "__main__":
    success = main()
    sys.exit(0 if success else 1)

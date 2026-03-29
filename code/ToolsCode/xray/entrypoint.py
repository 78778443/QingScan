#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import argparse
import subprocess
import requests
import json
import os

def main():
    parser = argparse.ArgumentParser(description="Xray Active Scanner")
    parser.add_argument("--task-id", required=True, help="Task ID")
    parser.add_argument("--callback", required=True, help="Callback URL")
    parser.add_argument("--target", required=True, help="Target URL to scan")
    args = parser.parse_args()

    print("[+] Starting xray scan for task:", args.task_id)
    print("[+] Target:", args.target)
    print("[+] Callback:", args.callback)

    output_file = "/tmp/xray_result_" + args.task_id + ".json"
    
    cmd = ["/app/xray", "webscan", "--url", args.target, "--json-output", output_file]
    
    print("[+] Running command:", " ".join(cmd))
    
    try:
        result = subprocess.run(cmd, capture_output=True, text=True, timeout=300)
        print("[+] Xray stdout:", result.stdout)
        if result.stderr:
            print("[!] Xray stderr:", result.stderr)
    except subprocess.TimeoutExpired:
        print("[!] Xray scan timeout")
    except Exception as e:
        print("[!] Xray error:", e)

    vulnerabilities = []
    if os.path.exists(output_file):
        with open(output_file, "r") as f:
            for line in f:
                line = line.strip()
                if line:
                    try:
                        vuln = json.loads(line)
                        vulnerabilities.append(vuln)
                    except json.JSONDecodeError:
                        pass
    
    print("[+] Found", len(vulnerabilities), "vulnerabilities")

    callback_data = {
        "task_id": int(args.task_id),
        "status": "completed",
        "data": {
            "vulnerabilities": vulnerabilities,
            "count": len(vulnerabilities)
        },
        "data_type": "vuln"
    }

    try:
        response = requests.post(args.callback + "/result", json=callback_data, timeout=30)
        print("[+] Callback response:", response.status_code, "-", response.text)
    except Exception as e:
        print("[!] Callback error:", e)

    print("[+] Scan completed")

if __name__ == "__main__":
    main()

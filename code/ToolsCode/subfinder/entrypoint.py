#!/usr/bin/env python3
"""Subfinder 子域名发现工具入口脚本"""

import argparse
import json
import subprocess
import sys
import requests
from typing import Optional, List


def run_subfinder(target: str) -> List[str]:
    """运行 subfinder 扫描目标域名"""
    try:
        result = subprocess.run(
            ['subfinder', '-d', target, '-silent', '-all'],
            capture_output=True,
            text=True,
            timeout=300
        )
        if result.returncode != 0:
            print(f"Subfinder error: {result.stderr}", file=sys.stderr)
            return []
        
        # 解析输出，获取子域名列表
        subdomains = [line.strip() for line in result.stdout.strip().split('\n') if line.strip()]
        return subdomains
    except subprocess.TimeoutExpired:
        print("Subfinder timeout", file=sys.stderr)
        return []
    except Exception as e:
        print(f"Error running subfinder: {e}", file=sys.stderr)
        return []


def send_callback(callback_url: str, task_id: int, subdomains: List[str]) -> bool:
    """发送回调结果"""
    payload = {
        "task_id": task_id,
        "status": "completed",
        "data": {
            "subdomains": subdomains,
            "count": len(subdomains)
        },
        "data_type": "subfinder_result"
    }
    
    try:
        response = requests.post(
            callback_url,
            json=payload,
            headers={"Content-Type": "application/json"},
            timeout=30
        )
        print(f"Callback response: {response.status_code}")
        print(f"Callback body: {response.text}")
        return response.status_code == 200
    except Exception as e:
        print(f"Callback error: {e}", file=sys.stderr)
        return False


def main():
    parser = argparse.ArgumentParser(description='Subfinder 子域名发现工具')
    parser.add_argument('--task-id', required=True, type=int, help='任务ID')
    parser.add_argument('--callback', required=True, help='回调URL')
    parser.add_argument('--target', required=True, help='目标域名')
    
    args = parser.parse_args()
    
    print(f"Starting subfinder scan for: {args.target}")
    print(f"Task ID: {args.task_id}")
    print(f"Callback URL: {args.callback}")
    
    # 运行 subfinder 扫描
    subdomains = run_subfinder(args.target)
    
    print(f"Found {len(subdomains)} subdomains")
    for subdomain in subdomains[:10]:  # 只显示前10个
        print(f"  - {subdomain}")
    if len(subdomains) > 10:
        print(f"  ... and {len(subdomains) - 10} more")
    
    # 发送回调
    success = send_callback(args.callback, args.task_id, subdomains)
    
    if success:
        print("Callback successful!")
        sys.exit(0)
    else:
        print("Callback failed!", file=sys.stderr)
        sys.exit(1)


if __name__ == '__main__':
    main()

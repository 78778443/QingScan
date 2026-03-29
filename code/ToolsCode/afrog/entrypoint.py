#!/usr/bin/env python3
import argparse
import subprocess
import json
import requests
import os
import sys
from datetime import datetime

def run_afrog(target, output_file):
    """运行 afrog 扫描"""
    cmd = ['afrog', '-t', target, '-o', output_file]
    print(f"Running command: {' '.join(cmd)}")
    result = subprocess.run(cmd, capture_output=True, text=True)
    print(f"afrog stdout: {result.stdout}")
    print(f"afrog stderr: {result.stderr}")
    return result.returncode, result.stdout, result.stderr

def parse_results(output_file):
    """解析 afrog 扫描结果（HTML格式）"""
    results = []
    try:
        if os.path.exists(output_file):
            with open(output_file, 'r', encoding='utf-8') as f:
                content = f.read()
                print(f"Result file size: {len(content)} bytes")
                # 将HTML内容作为结果返回
                results = [{'html_output': content[:5000]}]  # 截取前5000字符
    except Exception as e:
        print(f"Error parsing results: {e}")
    return results

def callback_result(task_id, callback_url, results, status='completed'):
    """回调结果到指定URL"""
    payload = {
        'task_id': int(task_id),
        'status': status,
        'data': results,
        'data_type': 'vuln'
    }
    print(f"Callback payload: {json.dumps(payload, ensure_ascii=False, indent=2)[:1000]}")

    try:
        response = requests.post(callback_url + "/result", json=payload, timeout=30)
        print(f"Callback response status: {response.status_code}")
        print(f"Callback response body: {response.text}")
        return response.status_code == 200
    except Exception as e:
        print(f"Callback error: {e}")
        return False

def main():
    parser = argparse.ArgumentParser(description='afrog vulnerability scanner')
    parser.add_argument('--task-id', required=True, help='Task ID')
    parser.add_argument('--callback', required=True, help='Callback URL')
    parser.add_argument('--target', required=True, help='Target URL to scan')
    
    args = parser.parse_args()
    print(f"Task ID: {args.task_id}")
    print(f"Callback URL: {args.callback}")
    print(f"Target: {args.target}")
    
    # 输出文件路径 - afrog要求.html扩展名
    output_file = f'/tmp/afrog_result_{args.task_id}.html'
    
    # 运行 afrog 扫描
    returncode, stdout, stderr = run_afrog(args.target, output_file)
    
    # 解析结果
    results = parse_results(output_file)
    
    # 如果有stderr输出，也记录下来
    if stderr:
        results.append({'stderr': stderr, 'returncode': returncode})
    
    # 回调结果
    status = 'success' if returncode == 0 else 'completed_with_errors'
    success = callback_result(args.task_id, args.callback, results, status)
    
    if success:
        print("Callback successful")
    else:
        print("Callback failed")
    
    return 0

if __name__ == '__main__':
    sys.exit(main())

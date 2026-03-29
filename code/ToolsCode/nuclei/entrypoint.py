#!/usr/bin/env python3
import argparse
import subprocess
import json
import requests
import sys
from datetime import datetime

def run_nuclei(target):
    """执行 nuclei 扫描"""
    cmd = [
        'nuclei',
        '-u', target,
        '-json',
        '-silent'
    ]
    
    results = []
    try:
        process = subprocess.run(
            cmd,
            capture_output=True,
            text=True,
            timeout=300
        )
        
        # 解析输出
        for line in process.stdout.strip().split('\n'):
            if line:
                try:
                    result = json.loads(line)
                    results.append(result)
                except json.JSONDecodeError:
                    continue
                    
    except subprocess.TimeoutExpired:
        return {'error': 'Scan timeout'}
    except Exception as e:
        return {'error': str(e)}
    
    return results

def callback_status(callback_url, task_id, status, progress=0, message=''):
    """更新任务状态"""
    payload = {
        'task_id': int(task_id),
        'status': status,
        'progress': progress,
        'message': message
    }
    try:
        response = requests.post(
            f'{callback_url}/status',
            json=payload,
            timeout=30
        )
        return response.status_code == 200
    except Exception as e:
        print(f'[-] Status callback failed: {e}')
        return False

def callback_result(callback_url, task_id, results):
    """提交扫描结果"""
    payload = {
        'task_id': int(task_id),
        'status': 'completed',
        'data': results,
        'data_type': 'nuclei_json'
    }
    
    try:
        response = requests.post(
            f'{callback_url}/result',
            json=payload,
            timeout=30
        )
        return response.status_code == 200, response.text
    except Exception as e:
        return False, str(e)

def main():
    parser = argparse.ArgumentParser(description='Nuclei vulnerability scanner')
    parser.add_argument('--task-id', required=True, help='Task ID (integer)')
    parser.add_argument('--callback', required=True, help='Callback URL base')
    parser.add_argument('--target', required=True, help='Target URL to scan')
    
    args = parser.parse_args()
    
    print(f'[*] Starting nuclei scan for task: {args.task_id}')
    print(f'[*] Target: {args.target}')
    
    # 更新状态为 running
    callback_status(args.callback, args.task_id, 'running', 0, 'Starting scan')
    
    # 执行扫描
    results = run_nuclei(args.target)
    
    if isinstance(results, dict) and 'error' in results:
        print(f'[!] Scan error: {results["error"]}')
        # 回调失败状态
        callback_status(args.callback, args.task_id, 'failed', 100, results['error'])
        sys.exit(1)
    
    print(f'[*] Found {len(results)} results')
    
    # 回调结果
    success, response = callback_result(args.callback, args.task_id, results)
    
    if success:
        print(f'[+] Callback successful')
    else:
        print(f'[-] Callback failed: {response}')
        sys.exit(1)

if __name__ == '__main__':
    main()

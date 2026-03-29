#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import argparse
import subprocess
import json
import requests
import sys
import os
import re

def run_dismap(target, output_file):
    """运行 dismap 扫描"""
    # 判断是网络段还是单个URL
    if re.match(r'.*/\d+$', target) or re.match(r'.*-\d+$', target):
        cmd = ['dismap', '-i', target, '-o', output_file]
    else:
        cmd = ['dismap', '-u', target, '-o', output_file]
    
    print('[+] Running: ' + ' '.join(cmd))
    
    try:
        result = subprocess.run(cmd, capture_output=True, text=True, timeout=300)
        print('[+] dismap stdout:')
        print(result.stdout)
        if result.stderr:
            print('[!] dismap stderr:')
            print(result.stderr)
        return True
    except subprocess.TimeoutExpired:
        print('[!] dismap scan timeout')
        return False
    except Exception as e:
        print('[!] dismap error: ' + str(e))
        return False

def parse_result(output_file):
    """解析扫描结果"""
    results = []
    if os.path.exists(output_file):
        with open(output_file, 'r') as f:
            content = f.read()
            for line in content.strip().split('\n'):
                if line.strip():
                    results.append(line.strip())
    return results

def send_callback(task_id, callback_url, results, success):
    """发送回调结果"""
    # 构造符合 API 要求的 payload
    payload = {
        'task_id': int(task_id),
        'status': 'completed' if success else 'failed',
        'data': results,
        'data_type': 'dismap'
    }
    
    print('[+] Sending callback to ' + callback_url)
    print('[+] Payload: ' + json.dumps(payload, ensure_ascii=False))
    
    try:
        resp = requests.post(callback_url, json=payload, timeout=30)
        print('[+] Callback response: ' + str(resp.status_code) + ' - ' + resp.text)
        return resp.status_code == 200
    except Exception as e:
        print('[!] Callback error: ' + str(e))
        return False

def main():
    parser = argparse.ArgumentParser(description='dismap asset discovery tool')
    parser.add_argument('--task-id', required=True, help='Task ID')
    parser.add_argument('--callback', required=True, help='Callback URL')
    parser.add_argument('--target', required=True, help='Target URL or IP')
    
    args = parser.parse_args()
    
    print('[+] Starting dismap scan')
    print('[+] Task ID: ' + args.task_id)
    print('[+] Target: ' + args.target)
    print('[+] Callback: ' + args.callback)
    
    output_file = '/tmp/dismap_' + args.task_id + '.txt'
    
    success = run_dismap(args.target, output_file)
    results = parse_result(output_file)
    print('[+] Found ' + str(len(results)) + ' results')
    
    callback_success = send_callback(args.task_id, args.callback, results, success)
    
    if callback_success:
        print('[+] Callback success!')
    else:
        print('[!] Callback failed!')
    
    if os.path.exists(output_file):
        os.remove(output_file)
    
    return 0 if callback_success else 1

if __name__ == '__main__':
    sys.exit(main())

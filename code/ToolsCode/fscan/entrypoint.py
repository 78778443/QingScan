#!/usr/bin/env python3
import argparse
import subprocess
import json
import requests
import os
import sys

def main():
    parser = argparse.ArgumentParser(description='Fscan Scanner')
    parser.add_argument('--task-id', required=True, help='Task ID')
    parser.add_argument('--callback', required=True, help='Callback URL base')
    parser.add_argument('--target', required=True, help='Target to scan')
    args = parser.parse_args()

    result = {
        'task_id': int(args.task_id) if args.task_id.isdigit() else 1,
        'status': 'running',
        'data_type': 'raw'
    }

    try:
        # 执行 fscan 扫描
        cmd = ['/usr/local/bin/fscan', '-h', args.target]
        print(f"Running: {' '.join(cmd)}")
        
        proc = subprocess.run(
            cmd,
            capture_output=True,
            text=True,
            timeout=300
        )
        
        output = proc.stdout + proc.stderr
        result['data'] = output
        result['status'] = 'completed'
        
    except subprocess.TimeoutExpired:
        result['status'] = 'failed'
        result['message'] = 'Scan timeout after 300 seconds'
    except Exception as e:
        result['status'] = 'failed'
        result['message'] = str(e)

    # 回调结果到 /api/callback/result
    callback_url = args.callback.rstrip('/') + '/result'
    try:
        resp = requests.post(
            callback_url,
            json=result,
            timeout=30
        )
        print(f"Callback to {callback_url}")
        print(f"Callback response: {resp.status_code}")
        print(f"Callback body: {resp.text}")
    except Exception as e:
        print(f"Callback failed: {e}")

    print(json.dumps(result, indent=2, ensure_ascii=False))

if __name__ == '__main__':
    main()

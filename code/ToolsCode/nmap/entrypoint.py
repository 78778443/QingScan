#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""Nmap 端口扫描工具入口脚本"""

import argparse
import subprocess
import json
import requests
import sys
import re
from datetime import datetime
from typing import Dict, List, Any


def run_nmap_scan(target: str, ports: str = None, scan_type: str = "-sT") -> Dict[str, Any]:
    """
    运行 nmap 扫描
    """
    cmd = ["nmap", scan_type, "-Pn"]
    
    if ports:
        cmd.extend(["-p", ports])
    else:
        cmd.extend(["-p", "1-10000"])
    
    cmd.append("-sV")
    cmd.append(target)
    
    cmd_str = " ".join(cmd)
    print(f"[*] Running nmap: {cmd_str}")
    
    try:
        result = subprocess.run(
            cmd,
            capture_output=True,
            text=True,
            timeout=600
        )
        
        return {
            "success": True,
            "stdout": result.stdout,
            "stderr": result.stderr,
            "returncode": result.returncode
        }
    except subprocess.TimeoutExpired:
        return {
            "success": False,
            "error": "Nmap scan timeout (600s)"
        }
    except Exception as e:
        return {
            "success": False,
            "error": str(e)
        }


def parse_nmap_output(output: str) -> List[Dict[str, Any]]:
    """
    解析 nmap 输出，提取端口信息
    """
    ports = []
    port_pattern = re.compile(r"(\d+)/(tcp|udp)\s+(open|closed|filtered)\s+(.+)")
    
    for line in output.split("\n"):
        match = port_pattern.match(line.strip())
        if match:
            port_num = int(match.group(1))
            protocol = match.group(2)
            state = match.group(3)
            service = match.group(4).strip()
            
            ports.append({
                "port": port_num,
                "protocol": protocol,
                "state": state,
                "service": service
            })
    
    return ports


def send_callback(callback_url: str, task_id: int, target: str, 
                  ports: List[Dict], raw_output: str, status: str) -> Dict[str, Any]:
    """
    发送回调结果
    使用 qingscan 平台要求的格式:
    {
        "task_id": integer,
        "status": "completed" | "failed",
        "data": {...}  # 可选
    }
    """
    # 构建符合平台要求的数据格式
    result_data = {
        "target": target,
        "ports": ports,
        "port_count": len(ports),
        "open_ports": [p for p in ports if p["state"] == "open"],
        "open_port_count": len([p for p in ports if p["state"] == "open"]),
        "raw_output": raw_output
    }
    
    payload = {
        "task_id": task_id,
        "status": status,
        "data": result_data,
        "data_type": "nmap"
    }
    
    print(f"[*] Sending callback to: {callback_url}")
    
    try:
        response = requests.post(
            callback_url,
            json=payload,
            headers={"Content-Type": "application/json"},
            timeout=30
        )
        print(f"[*] Callback response: {response.status_code}")
        resp_text = response.text[:500] if response.text else ""
        print(f"[*] Response body: {resp_text}")
        return {
            "success": response.status_code == 200,
            "status_code": response.status_code,
            "response": response.text
        }
    except Exception as e:
        print(f"[!] Callback error: {e}")
        return {
            "success": False,
            "error": str(e)
        }


def main():
    parser = argparse.ArgumentParser(description="Nmap Port Scanner")
    parser.add_argument("--task-id", required=True, type=int, help="Task ID for tracking")
    parser.add_argument("--callback", required=True, help="Callback URL")
    parser.add_argument("--target", required=True, help="Target IP or hostname to scan")
    parser.add_argument("--ports", default=None, help="Port range (e.g., 1-1000, 80,443,22)")
    parser.add_argument("--scan-type", default="-sT", help="Nmap scan type (default: -sT)")
    
    args = parser.parse_args()
    
    print(f"[*] Starting Nmap scan")
    print(f"[*] Task ID: {args.task_id}")
    print(f"[*] Target: {args.target}")
    print(f"[*] Ports: {args.ports or 1-10000}")
    print(f"[*] Callback: {args.callback}")
    
    scan_result = run_nmap_scan(args.target, args.ports, args.scan_type)
    
    if not scan_result.get("success", False):
        error_msg = scan_result.get("error", "Unknown error")
        print(f"[!] Scan failed: {error_msg}")
        send_callback(
            args.callback, args.task_id, args.target,
            [], error_msg, "failed"
        )
        sys.exit(1)
    
    raw_output = scan_result.get("stdout", "")
    ports = parse_nmap_output(raw_output)
    
    print(f"[*] Found {len(ports)} ports")
    open_ports = [p for p in ports if p["state"] == "open"]
    print(f"[*] Open ports: {len(open_ports)}")
    for p in open_ports:
        port_val = p["port"]
        proto_val = p["protocol"]
        svc_val = p["service"]
        print(f"    - {port_val}/{proto_val} {svc_val}")
    
    callback_result = send_callback(
        args.callback, args.task_id, args.target,
        ports, raw_output, "completed"
    )
    
    if callback_result.get("success"):
        print(f"[+] Callback successful!")
        sys.exit(0)
    else:
        print(f"[!] Callback failed: {callback_result}")
        sys.exit(1)


if __name__ == "__main__":
    main()

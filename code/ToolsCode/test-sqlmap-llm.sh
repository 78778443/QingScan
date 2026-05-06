#!/bin/bash
# SQLMap + LLM 测试脚本
# 用法: ./test-sqlmap-llm.sh <目标URL>

TARGET="${1:-http://localhost:8889/vulnerabilities/sqli/?id=1&Submit=Submit}"
PORT=$((RANDOM % 10000 + 20000))

echo "=== SQLMap + LLM 测试 ==="
echo "目标: $TARGET"
echo "回调端口: $PORT"
echo ""

# 创建结果文件
RESULT_FILE="/tmp/sqlmap_result_$$.json"

# 启动HTTP回调服务
python3 << PYEOF &
import http.server, json, sys

class Handler(http.server.SimpleHTTPRequestHandler):
    def do_POST(self):
        body = self.rfile.read(int(self.headers.get('Content-Length', 0))).decode()
        data = json.loads(body)

        # 保存结果
        with open('$RESULT_FILE', 'w') as f:
            json.dump(data, f, ensure_ascii=False, indent=2)

        print(f"\n>>> 收到回调: {self.path}")
        self.send_response(200)
        self.send_header('Content-Type', 'application/json')
        self.end_headers()
        self.wfile.write(b'{"success":true}')

    def log_message(self, *a): pass

with http.server.HTTPServer(('0.0.0.0', $PORT), Handler) as s:
    s.handle_request()
PYEOF
HTTP_PID=$!
sleep 1

# 运行SQLMap
echo ">>> 启动SQLMap扫描..."
docker run --rm --network host \
  -e "http_proxy=" -e "https_proxy=" \
  qingscan/sqlmap:latest \
  --task-id 1 \
  --callback "http://localhost:$PORT/api/callback" \
  --target "$TARGET"

wait $HTTP_PID 2>/dev/null

# 检查结果
if [ ! -f "$RESULT_FILE" ]; then
    echo "未收到回调结果"
    exit 1
fi

echo ""
echo "=== 扫描结果 ==="
cat "$RESULT_FILE"

# 调用LLM分析
echo ""
echo "=== LLM分析 ==="
python3 << PYEOF2
import json, requests

with open('$RESULT_FILE') as f:
    result = json.load(f)

data = result.get('data', {})
if isinstance(data, str):
    data_str = data[:2000]
else:
    data_str = json.dumps(data, ensure_ascii=False)[:2000]

prompt = f"""分析以下SQLMap扫描结果，判断是否存在SQL注入漏洞，给出风险等级和建议。

扫描数据:
{data_str}

请用简洁的中文回答：
1. 是否存在漏洞
2. 漏洞类型
3. 风险等级(高/中/低)
4. 修复建议"""

resp = requests.post('http://localhost:11434/api/generate', json={
    'model': 'qwen2.5:0.5b',
    'prompt': prompt,
    'stream': False
})

if resp.status_code == 200:
    print(resp.json().get('response', '分析失败'))
else:
    print(f"LLM调用失败: {resp.status_code}")
PYEOF2

rm -f "$RESULT_FILE"
echo ""
echo "=== 完成 ==="

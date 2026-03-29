#!/bin/bash
# 插件验证脚本 - 启动临时HTTP服务接收回调
# 用法: ./test-plugin.sh <镜像> <目标>

IMAGE="${1:-daxia/qingscan-fscan:latest}"
TARGET="${2:-http://example.com}"
PORT=$((RANDOM % 10000 + 20000))  # 随机端口 20000-29999

echo "=== 插件验证 ==="
echo "镜像: $IMAGE"
echo "目标: $TARGET"
echo "监听端口: $PORT"
echo ""

# 启动HTTP服务
python3 -c "
import http.server, json, sys
class H(http.server.SimpleHTTPRequestHandler):
    def do_POST(self):
        body = self.rfile.read(int(self.headers.get('Content-Length', 0))).decode()
        print(f'\n>>> 回调: {self.path}')
        print(f'>>> 数据: {body}')
        self.send_response(200)
        self.send_header('Content-Type', 'application/json')
        self.end_headers()
        self.wfile.write(b'{\"success\":true,\"saved_count\":1}')
    def log_message(self, *a): pass
http.server.HTTPServer(('0.0.0.0', $PORT), H).handle_request()
" &
HTTP_PID=$!
sleep 1

# 运行容器
docker run --rm --add-host=host.docker.internal:host-gateway "$IMAGE" \
  --task-id 999 \
  --callback "http://host.docker.internal:$PORT/api/callback" \
  --target "$TARGET"

wait $HTTP_PID 2>/dev/null
echo ""
echo "=== 完成 ==="

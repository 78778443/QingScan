FROM alpine:latest

# 安装依赖
RUN apk add --no-cache python3 py3-pip wget ca-certificates && \
    pip3 install --break-system-packages requests

# 下载 fscan (直接下载二进制文件)
RUN wget -O /usr/local/bin/fscan https://github.com/shadow1ng/fscan/releases/download/1.8.4/fscan && \
    chmod +x /usr/local/bin/fscan

# 创建工作目录
WORKDIR /app

# 复制入口脚本
COPY entrypoint.py /app/entrypoint.py

ENTRYPOINT ["python3", "/app/entrypoint.py"]

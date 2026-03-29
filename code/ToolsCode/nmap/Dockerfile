FROM python:3.11-slim

WORKDIR /app

# 安装 nmap 和依赖
RUN apt-get update && apt-get install -y nmap && rm -rf /var/lib/apt/lists/*

# 安装 Python 依赖
RUN pip install --no-cache-dir requests

# 复制入口脚本
COPY entrypoint.py /app/entrypoint.py

ENTRYPOINT ["python", "/app/entrypoint.py"]

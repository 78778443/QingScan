FROM python:3.9-slim

# 安装依赖并下载 dismap
RUN apt-get update && apt-get install -y --no-install-recommends     wget     ca-certificates     && wget -q https://github.com/zhzyker/dismap/releases/download/v0.4/dismap-0.4-linux-amd64 -O /usr/local/bin/dismap     && chmod +x /usr/local/bin/dismap     && apt-get purge -y wget     && apt-get autoremove -y     && rm -rf /var/lib/apt/lists/*

# 安装 Python 依赖
RUN pip install --no-cache-dir requests

WORKDIR /app

COPY entrypoint.py /app/entrypoint.py

ENTRYPOINT ["python", "/app/entrypoint.py"]

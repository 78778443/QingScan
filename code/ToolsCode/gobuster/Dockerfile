FROM python:3.11-slim

# 安装必要工具
RUN apt-get update && apt-get install -y --no-install-recommends \
    wget \
    ca-certificates \
    && rm -rf /var/lib/apt/lists/*

# 安装 requests (已在 python 镜像中可用，但确保安装)
RUN pip install --no-cache-dir requests

# 下载 gobuster 预编译二进制
RUN wget -q https://github.com/OJ/gobuster/releases/download/v3.6.0/gobuster_Linux_x86_64.tar.gz \
    && tar -xzf gobuster_Linux_x86_64.tar.gz \
    && mv gobuster /usr/local/bin/ \
    && rm gobuster_Linux_x86_64.tar.gz \
    && chmod +x /usr/local/bin/gobuster

# 复制入口脚本
COPY entrypoint.py /opt/entrypoint.py
RUN chmod +x /opt/entrypoint.py

WORKDIR /opt

ENTRYPOINT ["python3", "/opt/entrypoint.py"]

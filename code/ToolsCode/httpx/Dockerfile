FROM python:3.11-slim

# 安装系统依赖
RUN apt-get update && apt-get install -y --no-install-recommends \
    curl \
    unzip \
    ca-certificates \
    && rm -rf /var/lib/apt/lists/*

# 安装 projectdiscovery httpx 工具
RUN curl -sL https://github.com/projectdiscovery/httpx/releases/download/v1.6.0/httpx_1.6.0_linux_amd64.zip -o /tmp/httpx.zip \
    && unzip /tmp/httpx.zip -d /usr/local/bin \
    && rm /tmp/httpx.zip \
    && chmod +x /usr/local/bin/httpx

# 验证 httpx 安装
RUN /usr/local/bin/httpx -version

# 安装 Python 依赖
RUN pip install --no-cache-dir requests

WORKDIR /app

COPY entrypoint.py /app/entrypoint.py
RUN chmod +x /app/entrypoint.py

ENTRYPOINT ["python", "/app/entrypoint.py"]

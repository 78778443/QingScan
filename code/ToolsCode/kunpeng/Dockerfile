FROM python:3.11-slim

WORKDIR /app

# 安装依赖
RUN apt-get update && apt-get install -y wget unzip && rm -rf /var/lib/apt/lists/*

# 下载 kunpeng
RUN wget -q https://github.com/opensec-cn/kunpeng/releases/download/20190527/kunpeng_linux_v20190527.zip -O kunpeng.zip \
    && unzip kunpeng.zip -d /app \
    && chmod +x /app/kunpeng_c.so \
    && rm kunpeng.zip

# 安装 Python 依赖
RUN pip install --no-cache-dir requests

# 复制入口脚本
COPY entrypoint.py /app/entrypoint.py

ENTRYPOINT ["python", "/app/entrypoint.py"]

FROM python:3.9-slim

WORKDIR /app

# 安装依赖
RUN pip install requests

# 下载 xray 社区版
RUN apt-get update && apt-get install -y wget unzip libpcap0.8 && \
    wget https://download.xray.cool/xray/1.9.6/xray_linux_amd64.zip -O xray.zip && \
    unzip xray.zip && \
    mv xray_linux_amd64 xray && \
    chmod +x xray && \
    rm xray.zip && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

# 复制入口脚本
COPY entrypoint.py /app/entrypoint.py

ENTRYPOINT ["python", "/app/entrypoint.py"]

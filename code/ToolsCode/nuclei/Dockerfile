FROM python:3.11-slim

# 安装系统依赖
RUN apt-get update && apt-get install -y     wget     ca-certificates     unzip     && rm -rf /var/lib/apt/lists/*

# 安装 nuclei
RUN wget -q https://github.com/projectdiscovery/nuclei/releases/download/v3.7.1/nuclei_3.7.1_linux_amd64.zip -O /tmp/nuclei.zip     && unzip /tmp/nuclei.zip -d /usr/local/bin/     && chmod +x /usr/local/bin/nuclei     && rm /tmp/nuclei.zip

# 安装 Python 依赖
RUN pip install --no-cache-dir requests

# 创建工作目录
WORKDIR /app

# 复制入口脚本
COPY entrypoint.py /app/entrypoint.py

# 更新 nuclei 模板
RUN nuclei -update-templates

ENTRYPOINT ["python", "/app/entrypoint.py"]

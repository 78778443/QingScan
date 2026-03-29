FROM python:3.11-slim

# 安装requests用于回调
RUN pip install --no-cache-dir requests

# 从本地复制sqlmap（避免网络依赖）
COPY sqlmap.tar.gz /tmp/sqlmap.tar.gz
RUN tar -xzf /tmp/sqlmap.tar.gz -C /opt && rm /tmp/sqlmap.tar.gz

# 复制回调包装脚本
COPY entrypoint.py /opt/entrypoint.py
RUN chmod +x /opt/entrypoint.py

WORKDIR /opt

ENTRYPOINT ["python3", "/opt/entrypoint.py"]

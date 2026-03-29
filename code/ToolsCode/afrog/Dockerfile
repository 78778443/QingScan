FROM golang:1.21-alpine

# 安装必要的工具
RUN apk add --no-cache git python3 py3-pip

# 安装 afrog
RUN go install github.com/zan8in/afrog/v2/cmd/afrog@latest

# 安装 Python requests 库
RUN pip3 install requests --break-system-packages

# 设置工作目录
WORKDIR /app

# 复制入口脚本
COPY entrypoint.py /app/entrypoint.py
RUN chmod +x /app/entrypoint.py

# 设置 PATH
ENV PATH="/root/go/bin:${PATH}"

ENTRYPOINT ["python3", "/app/entrypoint.py"]

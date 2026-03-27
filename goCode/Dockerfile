FROM ubuntu:22.04

LABEL maintainer="QingScan"
LABEL description="QingScan Security Scanner with all tools"

# 设置环境变量
ENV DEBIAN_FRONTEND=noninteractive
ENV SCANNER_HOME=/opt/qingscan
ENV PATH="${SCANNER_HOME}/tools:${PATH}"

# 创建目录
RUN mkdir -p ${SCANNER_HOME}/{tools,output,runtime} && \
    mkdir -p /root/.nuclei-templates

# 安装基础工具和依赖
RUN apt-get update && apt-get install -y --no-install-recommends \
    wget \
    curl \
    git \
    build-essential \
    python3 \
    python3-pip \
    python3-venv \
    nmap \
    whatweb \
    hydra \
    mysql-client \
    redis-tools \
    chromium-browser \
    chromium-chromedriver \
    && rm -rf /var/lib/apt/lists/*

# 安装 Go
RUN wget -q https://go.dev/dl/go1.21.5.linux-amd64.tar.gz && \
    tar -C /usr/local -xzf go1.21.5.linux-amd64.tar.gz && \
    rm go1.21.5.linux-amd64.tar.gz

ENV PATH="/usr/local/go/bin:${PATH}"
ENV GOPATH="/root/go"
ENV PATH="${GOPATH}/bin:${PATH}"

# 安装 nuclei
RUN go install -v github.com/projectdiscovery/nuclei/v3@latest && \
    mv /root/go/bin/nuclei ${SCANNER_HOME}/tools/nuclei

# 安装 crawlergo
RUN go install -v github.com/9bie-sec/crawlergo@latest && \
    mv /root/go/bin/crawlergo ${SCANNER_HOME}/tools/crawlergo

# 安装 vulmap
RUN go install -v github.com/zhzyker/vulmap@latest && \
    mv /root/go/bin/vulmap ${SCANNER_HOME}/tools/vulmap

# 安装 dismap
RUN go install -v github.com/zhzyker/dismap@latest && \
    mv /root/go/bin/dismap ${SCANNER_HOME}/tools/dismap

# 安装 rad
RUN go install -v github.com/chaitin/rad@latest && \
    mv /root/go/bin/rad ${SCANNER_HOME}/tools/rad

# 克隆并安装 dirmap
RUN git clone --depth 1 https://github.com/H4ckForJob/dirmap.git ${SCANNER_HOME}/tools/dirmap && \
    mv ${SCANNER_HOME}/tools/dirmap/dirmap.py ${SCANNER_HOME}/tools/dirmap

# 安装 sqlmap
RUN git clone --depth 1 https://github.com/sqlmapproject/sqlmap.git ${SCANNER_HOME}/tools/sqlmap

# 安装 semgrep
RUN pip3 install semgrep --break-system-packages

# 下载 xray (需要手动放到宿主机)
# COPY xray_linux_amd64 ${SCANNER_HOME}/tools/xray
# RUN chmod +x ${SCANNER_HOME}/tools/xray

# 复制 qingscan 程序
COPY qingscan ${SCANNER_HOME}/qingscan
COPY config.yaml ${SCANNER_HOME}/config.yaml

# 设置权限
RUN chmod +x ${SCANNER_HOME}/qingscan && \
    chmod +x ${SCANNER_HOME}/tools/nuclei && \
    chmod +x ${SCANNER_HOME}/tools/crawlergo && \
    chmod +x ${SCANNER_HOME}/tools/vulmap && \
    chmod +x ${SCANNER_HOME}/tools/dismap && \
    chmod +x ${SCANNER_HOME}/tools/rad

# 工作目录
WORKDIR ${SCANNER_HOME}

# 暴露端口
EXPOSE 8080

# 启动命令
CMD ["/opt/qingscan/qingscan", "-c", "/opt/qingscan/config.yaml"]

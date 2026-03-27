# 超简版工具容器
FROM alpine:3.19

ENV TOOLS_PATH=/opt/qingscan/tools

RUN apk add --no-cache \
    nmap \
    git \
    python3 \
    py3-pip \
    curl

RUN mkdir -p ${TOOLS_PATH}

# sqlmap
RUN git clone --depth 1 https://github.com/sqlmapproject/sqlmap.git ${TOOLS_PATH}/sqlmap

WORKDIR ${TOOLS_PATH}

CMD ["sh"]

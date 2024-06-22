FROM ubuntu:22.04

COPY ./sources.list /etc/apt/sources.list
# 更新包列表并安装必需的系统工具
RUN apt-get update && \
    apt-get install -y apt-utils tzdata && \
    apt-get upgrade -y

ARG DEBIAN_FRONTEND=noninteractive
RUN ln -fs /usr/share/zoneinfo/Asia/Shanghai /etc/localtime && echo "Asia/Shanghai" > /etc/timezone && dpkg-reconfigure -f noninteractive tzdata



# 安装PHP及其扩展
RUN apt-get install -y \
        php \
        php-xml \
        php-gd \
        php-mysqli \
        php-dom \
        php-cli \
        php-zip \
        unzip \
        php-curl \
        curl \
        composer \
        mysql-server

# 设置工作目录
WORKDIR /root

# 复制项目代码到容器内
RUN git clone https://gitee.com/songboy/QingScan.git --depth=1

# 进入项目代码目录并使用Composer安装依赖
RUN cd /root/QingScan/code && composer install --no-dev --optimize-autoloader

RUN apt install -y vim
RUN cd /root/QingScan/code && git pull && ls

# 设置脚本可执行权限
RUN chmod +x /root/QingScan/code/script.sh
#配置信息
RUN  cp /root/QingScan/code/.example.env /root/QingScan/code/.env
RUN  sed -i 's/127.0.0.1/mysql_addr/g' /root/QingScan/code/.env


# 定义容器启动时执行的命令
CMD ["sh", "-c", "cd  /root/QingScan/code && php think run -p 80 & ./script.sh"]
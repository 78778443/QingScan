FROM registry.cn-hangzhou.aliyuncs.com/daxia/qingscan:211014_01

# 基础内容更新
WORKDIR /root/qingscan
ENV DEBIAN_FRONTEND=noninteractive
ADD ./google-chrome /usr/bin/google-chrome


# 定义端口为80
CMD ["php","/root/qingscan/safe.php"]
#   docker build -t registry.cn-hangzhou.aliyuncs.com/daxia/qingscan:211111_01 ./
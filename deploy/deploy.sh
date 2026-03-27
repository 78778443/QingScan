#!/bin/bash

# QingScan Go 版本部署脚本
# 部署到 huoshan.songboy.site

set -e

SERVER="huoshan.songboy.site"
USER="root"
PORT=22

echo "=== QingScan Go 版本部署 ==="
echo "目标服务器: $SERVER"
echo ""

# 检查 docker 和 docker-compose
echo "1. 检查 Docker 环境..."
ssh $USER@$SERVER "docker --version && docker-compose --version"

# 创建远程目录
echo "2. 创建部署目录..."
ssh $USER@$SERVER "mkdir -p /opt/qingscan"

# 复制部署文件
echo "3. 复制部署文件..."
scp -r deploy/ $USER@$SERVER:/opt/qingscan/

# 复制源码
echo "4. 复制源码..."
rsync -avz --exclude='.git' --exclude='vendor' --exclude='node_modules' \
    --exclude='*.so' --exclude='qingscan' --exclude='qingscan-cli' \
    ./ $USER@$SERVER:/opt/qingscan/code/

# 构建并启动服务
echo "5. 构建 Docker 镜像..."
ssh $USER@$SERVER "cd /opt/qingscan/deploy && docker-compose build"

echo "6. 启动服务..."
ssh $USER@$SERVER "cd /opt/qingscan/deploy && docker-compose up -d"

echo ""
echo "=== 部署完成 ==="
echo "服务地址: http://$SERVER:8080"
echo ""
echo "查看日志: docker-compose logs -f"
echo "停止服务: docker-compose down"
echo "重启服务: docker-compose restart"

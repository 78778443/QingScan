# Go 服务 Dockerfile
FROM golang:1.25-alpine AS builder

WORKDIR /build

# 安装 build 依赖
RUN apk add --no-cache gcc musl-dev

# 复制源码
COPY go.mod go.sum ./
COPY internal/ ./internal/
COPY cmd/ ./cmd/
COPY view/ ./view/
COPY static/ ./static/
COPY config.yaml ./
COPY main.go .

# 下载依赖
RUN go mod download

# 构建
RUN CGO_ENABLED=0 GOOS=linux go build -a -installsuffix cgo -o qingscan .

# 运行阶段
FROM alpine:latest

WORKDIR /app

# 安装证书
RUN apk --no-cache add ca-certificates tzdata

# 复制构建产物
COPY --from=builder /build/qingscan .
COPY --from=builder /build/view ./view
COPY --from=builder /build/static ./static

# 创建工具目录
RUN mkdir -p /opt/qingscan/tools

EXPOSE 8080

CMD ["./qingscan"]

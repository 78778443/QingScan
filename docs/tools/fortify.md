# Fortify 安装与配置指南

## 简介

Fortify是一款商业静态代码分析工具，用于检测源代码中的安全漏洞。

## 安装方法

Fortify是商业软件，需要购买许可证才能使用。

### 获取Fortify

1. 访问Micro Focus官网获取Fortify软件
2. 下载适合您系统的安装包
3. 获取许可证文件

### 安装Fortify

```bash
# 解压安装包
tar -zxvf fortify_linux.tar.gz

# 运行安装程序
sudo ./fortify_install.sh
```

## 配置

Fortify在QingScan中的使用需要配置相关路径。

### 相关目录

- 代码检查目录: `{QingScan项目根目录}/code/data/codeCheck`
- Fortify结果目录: `{QingScan项目根目录}/code/data/fortify_result`

确保这些目录存在且具有适当的权限：

```bash
mkdir -p {QingScan项目根目录}/code/data/codeCheck
mkdir -p {QingScan项目根目录}/code/data/fortify_result
```

## 在QingScan中的使用

QingScan会在执行代码扫描任务时调用Fortify工具。

### 工作流程

1. 下载代码到 `{QingScan项目根目录}/code/data/codeCheck` 目录
2. 使用Fortify分析代码
3. 将结果保存到 `{QingScan项目根目录}/code/data/fortify_result` 目录

## 常见问题

### 1. 许可证问题

确保Fortify许可证文件有效且未过期。

### 2. 路径配置问题

确保Fortify安装路径正确配置。

### 3. Java环境问题

Fortify需要Java环境支持，确保已安装合适的JDK版本。

## 更多信息

- [Micro Focus Fortify官网](https://www.microfocus.com/en-us/products/static-code-analysis-sast/overview)
- [Fortify官方文档](https://www.microfocus.com/documentation/fortify-static-code-analyzer-and-tools/)
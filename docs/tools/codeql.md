# CodeQL 安装与配置指南

## 简介

CodeQL是一款语义代码分析引擎，由GitHub开发，用于自动化安全检查和研究项目中的代码。

## 安装方法

### 下载CodeQL

1. 访问CodeQL CLI工具页面：https://github.com/github/codeql-cli-binaries/releases
2. 下载适合您系统的版本
3. 解压下载的文件

### 安装CodeQL

```bash
# 创建目录
mkdir -p {QingScan项目根目录}/code/extend/tools/codeql

# 将下载的文件解压到该目录
unzip codeql-linux64.zip -d {QingScan项目根目录}/code/extend/tools/codeql
```

### 安装CodeQL查询库

```bash
# 克隆CodeQL查询库
git clone https://github.com/github/codeql.git {QingScan项目根目录}/code/extend/tools/codeql/qlpacks
```

## 配置环境变量

将CodeQL添加到系统PATH中：

```bash
export PATH={QingScan项目根目录}/code/extend/tools/codeql:$PATH
```

或者在使用时指定完整路径：

```bash
{QingScan项目根目录}/code/extend/tools/codeql/codeql --version
```

## 验证安装

```bash
codeql --version
```

## 在QingScan中的使用

QingScan会在执行代码扫描任务时自动调用CodeQL工具。

### 使用方式

QingScan通过命令行调用CodeQL：

```bash
codeql database create my-db --language=python --source-root=/path/to/code
codeql database analyze my-db java-code-scanning.qls --format=sarif --output=results.sarif
```

## CodeQL查询包

CodeQL使用查询包(qlpacks)来定义特定语言的分析规则。

### 安装查询包

```bash
codeql pack download codeql/python-queries
```

### 更新查询包

```bash
codeql pack upgrade codeql/python-queries
```

## 常见问题

### 1. 权限问题

确保CodeQL具有执行权限：

```bash
chmod +x {QingScan项目根目录}/code/extend/tools/codeql/codeql
```

### 2. 查询库问题

确保已正确安装和配置CodeQL查询库。

### 3. Java环境问题

CodeQL需要Java环境支持，确保已安装合适的JDK版本。

## 更多信息

- [CodeQL官网](https://codeql.github.com/)
- [CodeQL文档](https://codeql.github.com/docs/)
- [CodeQL GitHub项目](https://github.com/github/codeql)
- [CodeQL CLI文档](https://codeql.github.com/docs/codeql-cli/)
# Semgrep 安装与配置指南

## 简介

Semgrep是一款快速静态分析工具，用于在编辑器、提交时和CI中查找错误和强制代码标准。

## 安装方法

### 使用pip安装（推荐）

```bash
pip install semgrep
```

### 使用Homebrew安装（macOS）

```bash
brew install semgrep
```

### 使用Docker运行

```bash
docker run --rm -v "${PWD}:/src" returntocorp/semgrep semgrep --config=auto /src
```

### 从源码安装

```bash
# 克隆项目
git clone https://github.com/returntocorp/semgrep.git
cd semgrep

# 安装
pip install .
```

## 验证安装

```bash
semgrep --version
```

## 在QingScan中的使用

QingScan会在执行代码扫描任务时自动调用Semgrep工具。

### 使用方式

QingScan通过命令行调用Semgrep：

```bash
semgrep --config=auto /path/to/code
```

## 配置规则

Semgrep支持自定义规则，可以针对特定的编程语言和安全问题编写规则。

### 规则文件位置

QingScan中Semgrep规则文件路径：
`{QingScan项目根目录}/code/extend/tools/semgrep/`

### 创建自定义规则

可以创建YAML格式的规则文件，例如：

```yaml
rules:
  - id: my-custom-rule
    patterns:
      - pattern: eval(...)
    message: "Use of eval is dangerous"
    languages: [python]
    severity: WARNING
```

## 常见问题

### 1. 权限问题

确保Semgrep具有执行权限：

```bash
which semgrep
```

### 2. 规则库问题

可以更新规则库获取最新的检测规则：

```bash
semgrep --update
```

### 3. Python环境问题

确保系统已安装Python3.6+：

```bash
python3 --version
```

## 更多信息

- [Semgrep官网](https://semgrep.dev/)
- [Semgrep GitHub项目](https://github.com/returntocorp/semgrep)
- [Semgrep文档](https://semgrep.dev/docs/)
- [Semgrep规则库](https://github.com/returntocorp/semgrep-rules)
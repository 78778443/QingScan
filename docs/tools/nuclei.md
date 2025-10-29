# Nuclei 安装与配置指南

## 简介

Nuclei是一款基于模板的漏洞扫描器，用于发送针对目标的请求，根据模板中的规则匹配响应，从而发现安全漏洞。

## 安装方法

### 使用官方安装脚本（推荐）

```bash
# 下载并安装
curl -L https://github.com/projectdiscovery/nuclei/releases/latest/download/nuclei-linux-amd64.tar.gz -o nuclei.tar.gz
tar -xzf nuclei.tar.gz
sudo mv nuclei /usr/local/bin/
rm -f nuclei.tar.gz
```

### 手动下载安装

1. 访问Nuclei的GitHub发布页面：https://github.com/projectdiscovery/nuclei/releases
2. 下载适合您系统的版本（通常为Linux版本）
3. 解压下载的文件

### 部署到指定位置

将Nuclei工具放置在项目指定目录中：

```bash
# 创建目录
mkdir -p {QingScan项目根目录}/code/extend/tools/nuclei

# 将下载的Nuclei文件复制到该目录
cp nuclei {QingScan项目根目录}/code/extend/tools/nuclei/

# 添加执行权限
chmod +x {QingScan项目根目录}/code/extend/tools/nuclei/nuclei
```

### 安装模板

Nuclei需要模板才能工作，需要安装模板文件：

```bash
nuclei -update-templates
```

或者手动下载：

```bash
git clone https://github.com/projectdiscovery/nuclei-templates.git {QingScan项目根目录}/code/extend/tools/nuclei/nuclei-templates
```

## 验证安装

```bash
cd {QingScan项目根目录}/code/extend/tools/nuclei/
./nuclei -version
```

## 在QingScan中的使用

QingScan会在执行扫描任务时自动调用Nuclei工具。工具路径配置在代码中为：
`{QingScan项目根目录}/code/extend/tools/nuclei/`

## 常见问题

### 1. 权限问题

确保Nuclei可执行文件具有执行权限：

```bash
chmod +x {QingScan项目根目录}/code/extend/tools/nuclei/nuclei
```

### 2. 模板路径

如果使用自定义模板路径，需要在QingScan配置中指定模板目录。

## 更多信息

- [Nuclei GitHub项目](https://github.com/projectdiscovery/nuclei)
- [Nuclei官方文档](https://nuclei.projectdiscovery.io/)
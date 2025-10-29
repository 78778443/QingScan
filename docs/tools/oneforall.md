# OneForAll 安装与配置指南

## 简介

OneForAll是一款功能强大的子域收集工具，用于收集目标的子域名信息。

## 安装方法

### 克隆项目

```bash
# 创建目录
mkdir -p /data/tools/oneforall

# 克隆项目
git clone https://github.com/shmilylty/OneForAll.git /data/tools/oneforall

# 进入目录
cd /data/tools/oneforall
```

### 安装依赖

```bash
# 安装Python依赖
pip3 install -r requirements.txt

# 如果出现安装问题，可以尝试使用国内源
pip3 install -r requirements.txt -i https://pypi.tuna.tsinghua.edu.cn/simple/
```

## 配置

OneForAll支持多种配置选项，可以通过修改配置文件来定制功能。

### 主要配置文件

- `config/log.py`: 日志配置
- `config/settings.py`: 主要设置
- `config/api.py`: API密钥配置

### API密钥配置

OneForAll支持多种DNS查询API，可以在`config/api.py`中配置：

```python
# 示例API配置
api = {
    'aliyun': {
        'accessKeyId': '',
        'accessKeySecret': ''
    },
    'tencent': {
        'secretId': '',
        'secretKey': ''
    }
}
```

## 部署到指定位置

OneForAll工具在QingScan中配置路径为：
`/data/tools/oneforall/`

确保该路径下有完整的OneForAll项目文件。

## 验证安装

```bash
cd /data/tools/oneforall
python3 oneforall.py --help
```

## 在QingScan中的使用

QingScan会在执行ASM(资产发现)任务时自动调用OneForAll工具。工具路径配置在代码中为：
`/data/tools/oneforall/`

## 常见问题

### 1. Python依赖问题

确保系统已安装Python3.6+和pip3：

```bash
# Ubuntu/Debian
sudo apt-get install python3 python3-pip

# CentOS/RHEL
sudo yum install python3 python3-pip
```

### 2. 权限问题

确保OneForAll脚本具有执行权限：

```bash
chmod +x /data/tools/oneforall/oneforall.py
```

### 3. 数据库依赖

OneForAll需要数据库支持，确保已安装相关依赖：

```bash
pip3 install pymysql
```

## 更多信息

- [OneForAll GitHub项目](https://github.com/shmilylty/OneForAll)
- [OneForAll使用文档](https://github.com/shmilylty/OneForAll/blob/master/docs/README-en.md)
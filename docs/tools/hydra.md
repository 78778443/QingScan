# Hydra 安装与配置指南

## 简介

Hydra是一款网络登录破解工具，支持多种协议的暴力破解和字典攻击。

## 安装方法

### Ubuntu/Debian 系统

```bash
sudo apt-get update
sudo apt-get install hydra
```

### CentOS/RHEL/Fedora 系统

```bash
# CentOS/RHEL
sudo yum install hydra

# Fedora
sudo dnf install hydra
```

### 从源码编译安装

```bash
# 安装依赖
sudo apt-get install libssl-dev libssh-dev libidn11-dev libpcre3-dev libgtk2.0-dev libmysqlclient-dev libpq-dev libsvn-dev gcc

# 下载源码
git clone https://github.com/vanhauser-thc/thc-hydra.git
cd thc-hydra

# 编译安装
./configure
make
sudo make install
```

## 配置

Hydra在QingScan中的配置路径为：
`/data/tools/hydra/`

相关配置在 `config/tools.php` 文件中：

```php
'hydra' => [
    'install_path'=>'/data/tools/hydra/',
    'username'=>\think\facade\App::getRootPath().'tools/hydra/username.txt',
    'password'=>\think\facade\App::getRootPath().'tools/hydra/password.txt'
],
```

## 字典文件

Hydra需要用户名和密码字典文件来进行暴力破解。

### 默认字典文件位置

- 用户名字典: `{QingScan项目根目录}/code/extend/tools/hydra/username.txt`
- 密码字典: `{QingScan项目根目录}/code/extend/tools/hydra/password.txt`

### 创建字典文件

```bash
# 创建用户名字典
echo -e "admin\nroot\nuser\ntest" > {QingScan项目根目录}/code/extend/tools/hydra/username.txt

# 创建密码字典
echo -e "123456\nadmin\nroot\npassword\ntest" > {QingScan项目根目录}/code/extend/tools/hydra/password.txt
```

## 验证安装

```bash
hydra --help
```

## 在QingScan中的使用

QingScan会在执行扫描任务时自动调用Hydra工具。工具路径配置在代码中为：
`/data/tools/hydra/`

## 常见问题

### 1. 权限问题

确保Hydra具有执行权限：

```bash
which hydra
```

### 2. 字典文件问题

确保字典文件存在且格式正确。

### 3. 协议支持问题

某些协议支持可能需要额外的库文件，确保已安装相关依赖。

## 更多信息

- [Hydra GitHub项目](https://github.com/vanhauser-thc/thc-hydra)
- [Hydra官方文档](https://github.com/vanhauser-thc/thc-hydra/blob/master/README.md)
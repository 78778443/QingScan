## 安装教程

1. 需要安装docker、docker-compose 安装方法（http://get.daocloud.io/）
2. 下载代码后,启动容器`cd QingScan/docker/latest  && docker-compose up -d `
2. <b>首次</b>启动需要更新容器内代码`docker exec  qingscan sh -c 'cd /root/qingscan && git fetch && git reset --hard origin/main' `
3. 依次执行命令创建MySQL数据库`docker exec -it  mysqlser bash`,`mysql -uroot -p123`执行创建数据库 `CREATE DATABASE IF NOT EXISTS QingScan;`
4. 浏览器访问  http://127.0.0.1:8000/ 自动进入安装界面

> 1. fortify 涉及许可证问题，镜像内不包含，需要自己将Linux版本的fortify放到`/data/tools`文件夹中
> 2. AWVS 调用主要通过API，需要自己将API配置系统，配置管理中去


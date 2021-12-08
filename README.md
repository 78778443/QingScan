# QingScan
一个批量漏洞挖掘工具，黏合各种好用的扫描器。

#### 介绍

QingScan 是一款聚合扫描器，本身不生产安全扫描功能，但会作为一个安全扫描工具的搬运工； 当添加一个目标后，QingScan会自动调用各种扫描器对目标进行扫描，并将扫描结果录入到QingScan平台中进行聚合展示


#### 联系我

在使用过程中有任何问题，可以通过微信联系我

微信交流群

![](http://oss.songboy.site/blog/b5d43ea2473167be951306d72190f3e.jpg)

个人微信

![](http://oss.songboy.site/blog/4a5ebf7feb5b62ba4a39a8a620c3d5c.jpg)

#### 安装教程

1.  需要安装docker、docker-compose
2. `cd QingScan/docker/20211204_01  && docker-compose up `
3. 浏览器访问  http://127.0.0.1:8000/ 自动进入登录界面
4. 初始账号: `test1` 密码: `123456`

详细文档：http://wiki.qingscan.songboy.site

> 1. fortify 涉及许可证问题，镜像内不包含，需要自己将Linux版本的fortify放到`/data/tools`文件夹中
> 2. AWVS 调用主要通过API，需要自己将API配置系统，配置管理中去

#### 功能展示
![image](https://user-images.githubusercontent.com/8509054/143174877-879408de-e594-4508-aa7c-b2fe095382cb.png)

![image](https://user-images.githubusercontent.com/8509054/143174979-f93bab2f-1506-4b01-9a2c-888a1c377478.png)

![image](https://user-images.githubusercontent.com/8509054/143175009-ceb5e762-4770-469e-827d-82937550d3a6.png)


![image](https://user-images.githubusercontent.com/8509054/143175022-d7821199-ef11-4f5d-a7ac-76003bd3074f.png)

![image](https://user-images.githubusercontent.com/8509054/143175091-91d04fea-0fa7-45ad-8f39-d8d77f816cbf.png)


![image](https://user-images.githubusercontent.com/8509054/143175157-0934560b-5ed2-4ce8-bc9b-9faff19e3517.png)

#### 免责申明

1、本软件产品为免费软件仅供学习交流使用，用户可以非商业性地下载、安装、复制和散发本软件产品。
2、本软件不得用于从事违反中国人民共和国相关法律所禁止的活动，对于用户擅自使用本软件从事违法活动不承担任何责任（包括但不限于刑事责任、行政责任、民事责任）。

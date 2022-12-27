# QingScan
一个批量漏洞挖掘工具，黏合各种好用的扫描器。

## 介绍

QingScan 是一款聚合扫描器，本身不生产安全扫描功能，但会作为一个安全扫描工具的搬运工； 当添加一个目标后，QingScan会自动调用各种扫描器对目标进行扫描，并将扫描结果录入到QingScan平台中进行聚合展示

- GitHub：https://github.com/78778443/QingScan
- 码云地址：https://gitee.com/songboy/QingScan
- 详细文档：http://wiki.qingscan.site
- 哔哩哔哩：https://space.bilibili.com/437273065
- 官网地址：http://qingscan.site/


## 在线演示
在线体验地址：http://demo.qingscan.site/
用户名：admin   密码：admin
> 注：在线体验地址为功能演示，不会对目标实际扫描~

## 安装教程

1. 需要安装docker、docker-compose 安装方法 http://get.daocloud.io/
2. 下载代码后,启动容器`cd QingScan/ && docker-compose up -d `
3. <b>首次</b>启动更新容器内代码`docker exec  qingscan sh -c 'cd /root/qingscan && git fetch && git reset --hard origin/main && rm code/public/install/install.lock' `
4. 浏览器访问  http://127.0.0.1:8000/ 自动进入安装界面
5. 安装中出现任何问题，请查看视频安装教程:https://www.bilibili.com/video/BV1rF411i7Gx

#### 个别插件配置
- fortify 涉及许可证问题，镜像内不包含，需要自己将Linux版本的fortify放到`/data/tools`文件夹中
- AWVS 调用主要通过API，需要自己将API配置系统，配置管理中去
- AWVS默认账户:admin@admin.com 默认密码:Admin123
- murphysec 调用时，需要自己将墨菲安全token配置到管理中去

#### 重复安装(保留上一次数据)

1. 使用数据库管理软件,导出之前的数据为SQL文件(不要表结构,只需要数据部分)
2. 重新安装一次qingscan
3. 将导出的SQL文件的,覆盖到现在的数据库(再次提醒,不要表结构,只需要数据部分)

## 支持支持

QingScan尽最大能力保障各位使用的顺畅，但QingScan人力有限，目前仍然无法预料到每一处场景，希望您尽量按照视频教程中的环境来搭建和使用；

如果在安装或使用的过程中遇到任何问题，也可以联系我们的工程师远程协助帮你解决问题(需淘宝下单`18.8`元人民币),下单后主动添加群里`婷婷的橙子`为好友，并将向日葵的ID和验证码发给她。

淘宝链接地址：https://item.taobao.com/item.htm?spm=a2126o.success.0.0.5e484831UkSn6H&id=666295567386&mt=
![QingScan 远程协助安装二维码](https://user-images.githubusercontent.com/8509054/170407026-ab399c52-37a6-4ebe-8e96-31fe61ae4b32.png)


## 靶场系统

您在安装之后请不要对未获得足够授权的目标进行扫描，同时为了让你能够快速上手，我们搭建了一些靶场系统授权你进行安全扫描：
1. http://permeate.qingscan.site/  轻松渗透测试系统测试


## 联系我

在使用过程中有任何问题，可以通过公众号、微信、QQ群联系
![联系我们](https://user-images.githubusercontent.com/76991805/165303155-10c0a418-78a4-48c2-b5f1-428d8e6118b7.png)


## 功能展示
![image](https://user-images.githubusercontent.com/8509054/143174877-879408de-e594-4508-aa7c-b2fe095382cb.png)

![image](https://user-images.githubusercontent.com/8509054/143174979-f93bab2f-1506-4b01-9a2c-888a1c377478.png)

![image](https://user-images.githubusercontent.com/8509054/143175009-ceb5e762-4770-469e-827d-82937550d3a6.png)


![image](https://user-images.githubusercontent.com/8509054/143175022-d7821199-ef11-4f5d-a7ac-76003bd3074f.png)

![image](https://user-images.githubusercontent.com/8509054/143175091-91d04fea-0fa7-45ad-8f39-d8d77f816cbf.png)


![image](https://user-images.githubusercontent.com/8509054/143175157-0934560b-5ed2-4ce8-bc9b-9faff19e3517.png)

## 📑 Licenses
本工具禁止进行未授权商业用途，禁止二次开发后进行未授权商业用途。

本工具仅面向合法授权的企业安全建设行为，在使用本工具进行检测时，您应确保该行为符合当地的法律法规，并且已经取得了足够的授权。

如您在使用本工具的过程中存在任何非法行为，您需自行承担相应后果，我们将不承担任何法律及连带责任。

在使用本工具前，请您务必审慎阅读、充分理解各条款内容，限制、免责条款或者其他涉及您重大权益的条款可能会以加粗、加下划线等形式提示您重点注意。

除非您已充分阅读、完全理解并接受本协议所有条款，否则，请您不要使用本工具。您的使用行为或者您以其他任何明示或者默示方式表示接受本协议的，即视为您已阅读并同意本协议的约束。



## Stargazers
[![Stargazers over time](https://starchart.cc/78778443/QingScan.svg?v211231)](https://github.com/78778443/QingScan)

# QingScan
一个批量漏洞挖掘工具，黏合各种好用的扫描器。

## 介绍

QingScan 是一款聚合扫描器，本身不生产安全扫描功能，但会作为一个安全扫描工具的搬运工； 当添加一个目标后，QingScan会自动调用各种扫描器对目标进行扫描，并将扫描结果录入到QingScan平台中进行聚合展示

- GitHub：https://github.com/78778443/QingScan
- 码云地址：https://gitee.com/songboy/QingScan
- 详细文档：http://wiki.qingscan.site
- 哔哩哔哩：https://space.bilibili.com/437273065 

 
## 安装教程

需要再Ubuntu22.04系统下安装，其他系统请自行安装
1. 安装PHP扩展和项目依赖

```bash
apt install php php-xml php-gd php-mysqli php-dom php-cli php-zip unzip php-curl composer

cd QingScan/code && composer install  
```

2. 用PHP启动项目web页面

```bash
php think run -p 80
```

3. 新建数据库，并导入数据表，SQL文件在`deploy`下的`qingscan.sql`

4. 访问web页面

```bash
http://127.0.0.1/
```
 
5. 启动调用脚本

```bash
./script.sh
```

## 技术支持

QingScan提供私人订制服务,如果你二次开发需求,可以联系我微信 `songboy8888`

## 联系我

在使用过程中有任何问题，可以通过公众号、微信、QQ群联系
![联系我们](https://user-images.githubusercontent.com/76991805/165303155-10c0a418-78a4-48c2-b5f1-428d8e6118b7.png)


## 功能展示
![](https://oss.songboy.site/blog/20240617224644.png)

![](https://oss.songboy.site/blog/20240617224721.png)

![](https://oss.songboy.site/blog/20240617224735.png)

![](https://oss.songboy.site/blog/20240617224838.png)
## 📑 Licenses
本工具禁止进行未授权商业用途，禁止二次开发后进行未授权商业用途。

本工具仅面向合法授权的企业安全建设行为，在使用本工具进行检测时，您应确保该行为符合当地的法律法规，并且已经取得了足够的授权。

如您在使用本工具的过程中存在任何非法行为，您需自行承担相应后果，我们将不承担任何法律及连带责任。

在使用本工具前，请您务必审慎阅读、充分理解各条款内容，限制、免责条款或者其他涉及您重大权益的条款可能会以加粗、加下划线等形式提示您重点注意。

除非您已充分阅读、完全理解并接受本协议所有条款，否则，请您不要使用本工具。您的使用行为或者您以其他任何明示或者默示方式表示接受本协议的，即视为您已阅读并同意本协议的约束。



## Stargazers
[![Stargazers over time](https://starchart.cc/78778443/QingScan.svg?v211231)](https://github.com/78778443/QingScan)

# some_pocsuite



用于企业内部进行漏洞排查与验证的的pocsuite3验证POC代码（pocsuite3是知道创宇安全团队的开源漏洞测试框架）。

由于原Pocsuite已停止更新，因此将原来的POC代码全部重新改写并迁移到pocsuite3，原POC备份在PocsuiteV2中。



## 插件代码编写

使用pocsuite3 漏洞测试框架，插件编写请参考 pocsuite3 项目插件编写要求。

[PoC 编写规范及要求说明](https://github.com/knownsec/pocsuite3/blob/master/docs/CODING.md)

| 序号 | poc                                     | 说明                                                         |
| ---- | --------------------------------------- | ------------------------------------------------------------ |
| 1    | hikvision-2013-4976_web_login-bypass.py | 海康威视cve-2013-4976匿名登录验证绕过                        |
| 2    | weblogic-vul-check_all_rce.py           | weblogic反序列化漏洞（CVE-2016-0638，CVE-2016-3510，CVE-2017-3248，CVE-2018-2628，CVE-2018-2893, CVE-2019-2890） |
| 3    | weblogic-wls-2017-10271_all_rce.py      | weblogic WLS组件反序列化漏洞CVE-2017-10271                   |
| 4    | zookeeper_all_unauthorized.py           | zookeeper未授权访问                                          |
| 5    | redis_all_unauthorized.py               | redis未授权访问（ [pocsuite_poc_collect](https://github.com/njcx/pocsuite_poc_collect)） |
| 6    | memcached_all_unauthorized.py           | memchached未授权访问                                         |
| 7    | snmp_v2_unauthorized.py                 | snmp未授权访问（需要安装pysnmp模块）                         |
| 8    | iis-ms15-034_7_rce.py                   | IIS（CVE-2015-1635，MS15-034）Check Script                   |
| 9    | iis-shortname_6_disclosure.py           | IIS Tilde Vulnerability (IIS Shortname) Check                |
| 10   | rdp-ms12-020_all_rce.py                 | MS12-020/CVE-2012-0002 Vulnerability Tester                  |
| 11   | windows-ms14-066_all_rce.py             | winshock (MS14-066) Vulnerability Tester                     |
| 12   | weblogic-async-2019-2725_all_rce.py     | weblogic async反序列化(CVE-2019-2725)Vulnerability Tester (POP: jdk7u21) |
| 13   | rdp-2019-0708_all_rce.py                | windows RDP RCE Vulnerability（CVE-2019-0708）Check (by  robertdavidgraham/rdpscan) |
| 14   | weblogic-ssrf_all_ssrf.py               | WebLogic SSRF And XSS(CVE-2014-4241, CVE-2014-4210, CVE-2014-4242) check POC |
| 15   | webmin_1.92_rce.py                      | Webmin 1.920 Unauthenticated Remote Code Execution（CVE-2019-15107）check POC by jerome |
| 16   | solr_8.3.1_rce.py                       | Apache Solr Velocity 注入远程命令执行漏洞 (CVE-2019-17558) check POC by jerome |
| 17   | supervisord-2017-11610_3_rce.py         | CVE-2017-11610 Supervisord 远程命令执行漏洞 check POC by kcat |
| 18   | tomcat-ajp-ghostcat_all_lfi.py          | CNVD-2020-10487/CVE-2020-1938  TomcatAJP任意文件读取/包含漏洞 check POC by rootk1t |
| 19   | f5-CVE-2020-5902_all_rce.py             | CVE-2020-5902 BIG-IP TMUI RCE漏洞 check POC                  |
| 20  | weblogic-iiop-2020-2551_all_rce.py             | CVE-2020-2551 Weblogic IIOP反序列化漏洞 check POC by rootk1t |
| 21  | weblogic-console-2020-14882_all_rce.py      | CVE-2020-14882 Weblogic Unauthorized bypass RCE check POC|
| 22  | flink-CVE-2020-17519_1.11.2_fileread.py      | CVE-2020-17519 Apache Flink目录穿越漏洞 check POC|
| 23  | flink-CVE-2020-17518_1.11.2_rce.py      | CVE-2020-17518 Apache Flink任意文件写漏洞导致RCE|
| 24  | springboot-actuator_all_unauthorized.py      | SpringBoot Actuator配置信息未授权访问 check Poc|
| 25  | vmware-vcenter-FileUpload_CVE-2021-22005_rce.py      | VMware vCenter Server未授权任意文件上传漏洞CVE-2021-22005 check Poc by knownsec|
陆续扩充中...



## 参考

- [Pocsuite3](https://github.com/knownsec/pocsuite3/)


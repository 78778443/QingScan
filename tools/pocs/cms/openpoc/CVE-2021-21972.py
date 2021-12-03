#!/usr/bin/env python
# coding: utf-8
from urllib.parse import urljoin

from pocsuite3.api import POCBase, Output, register_poc, logger, requests


class DemoPOC(POCBase):
    vulID = ''
    version = '1.0'
    author = ['']
    vulDate = '2021-02-24'
    createDate = '2021-02-24'
    updateDate = '2021-02-24'
    references = ['']
    name = 'VMware vCenter 未授权RCE漏洞'
    appPowerLink = ''
    appName = 'VMware vCenter'
    appVersion = ' 7.0 U1c 之前的 7.0 版本、6.7 U3l 之前的 6.7 版本、 6.5 U3n 之前的 6.5 版本'
    vulType = ''
    desc = '''
    VMware vCenter 未授权RCE漏洞
    from: https://github.com/conjojo/VMware_vCenter_UNAuthorized_RCE_CVE-2021-21972
    '''
    samples = ['']
    install_requires = ['']

    def _verify(self):
        result = {}

        try:
            vul_url = urljoin(
                self.url, "/ui/vropspluginui/rest/services/uploadova")
            resp1 = requests.get(self.url)
            resp2 = requests.get(vul_url)
            if '/vsphere-client' in resp1.text and resp2.status_code == 405:
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

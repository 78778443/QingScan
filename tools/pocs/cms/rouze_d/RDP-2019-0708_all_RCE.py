#!/usr/bin/env python
# coding: utf-8
import os
import sys
import subprocess
from urllib.parse import urlparse
from pocsuite3.api import register_poc
from pocsuite3.api import Output, POCBase
from pocsuite3.api import POC_CATEGORY, VUL_TYPE


class TestPOC(POCBase):
    vulID = '0'
    version = '1.0'
    author = 'hancool'
    vulDate = '2019-5-14'
    createDate = '2019-5-28'
    updateDate = '2019-5-28'
    references = ['https://github.com/zerosum0x0/CVE-2019-0708','https://github.com/robertdavidgraham/rdpscan' ]
    name = 'Remote Desktop Services Remote Code Execution Vulnerability（CVE-2019-0708）Check'
    appPowerLink = 'https://portal.msrc.microsoft.com/en-US/security-guidance/advisory/CVE-2019-0708'
    appName = 'Windows'
    appVersion = '2003,XP,7,Server2008,Server2008 R2'
    vulType = VUL_TYPE.CODE_EXECUTION
    category = POC_CATEGORY.EXPLOITS.REMOTE
    desc = '''
    A remote code execution vulnerability exists in Remote Desktop Services – formerly known as Terminal Services – 
    when an unauthenticated attacker connects to the target system using RDP and sends specially crafted requests. 
    This vulnerability is pre-authentication and requires no user interaction. 
    An attacker who successfully exploited this vulnerability could execute arbitrary code on the target system. 
    An attacker could then install programs; view, change, or delete data; or create new accounts with full user rights.

    PS:
    目前检测脚本是利用 https://github.com/robertdavidgraham/rdpscan 提供的rdpscan来检测是否存在漏洞
    '''

    def _verify(self):
        def check_os_and_rdpscan_exist():
            binfile = ''
            if sys.platform.startswith('linux'):
                binfile = 'rdpscan_linux'
            elif sys.platform.startswith('win'):
                binfile = 'rdpscan_win.exe'
            elif sys.platform.startswith('darwin'):
                binfile = 'rdpscan_mac'

            rdpscan_pathname = os.path.join(os.path.abspath('.'),binfile)
            if not os.path.exists(rdpscan_pathname):
                raise(Exception('{} binfile not found in current path'.format(binfile)))

            return rdpscan_pathname

        def run_rdpscan(binfile,ip,port):
            args = [binfile,'--port',str(port),ip]
            process = subprocess.Popen(args, stdout=subprocess.PIPE, stderr=subprocess.PIPE, shell=False)

            try:
                stdout, stderr = process.communicate()
            except:
                process.kill()
                stdout, stderr = process.communicate()

            stdout = stdout.decode('utf-8').strip()
            returncode = process.returncode
            
            if returncode != 0:
                return False,None
            elif stdout is not None and 'VULNERABLE' in stdout:
                return True,stdout

            return False,None

        binfile = check_os_and_rdpscan_exist()
        result = {}
        pr = urlparse(self.url)
        if pr.port:  # and pr.port not in ports:
            ports = [pr.port]
        else:
            ports = [3389,13389,23389]
        for port in ports:
            try:
                target = '{}:{}'.format(pr.hostname, port)
                status,msg = run_rdpscan(binfile,pr.hostname,port)
                if status:
                    result['VerifyInfo'] = {}
                    result['VerifyInfo']['URL'] = target
                    result['extra'] = {}
                    result['extra']['evidence'] =  msg
                    break
            except:
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

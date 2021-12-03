#!/usr/bin/env python
# coding: utf-8
import subprocess
import re
from urllib.parse import urlparse
from pocsuite3.api import register_poc
from pocsuite3.api import Output, POCBase
from pocsuite3.api import POC_CATEGORY, VUL_TYPE


class TestPOC(POCBase):
    vulID = '0'
    version = '1.0'
    author = 'hancool'
    vulDate = '2019-1-10'
    createDate = '2019-1-10'
    updateDate = '2019-1-10'
    references = ['https://github.com/anexia-it/winshock-test', ]
    name = 'winshock (MS14-066) Check'
    appPowerLink = 'https://docs.microsoft.com/en-us/security-updates/securitybulletins/2014/ms14-066'
    appName = 'windows'
    appVersion = 'All'
    vulType = VUL_TYPE.CODE_EXECUTION
    category = POC_CATEGORY.EXPLOITS.REMOTE
    desc = '''
    This script tries to determine whether the target system has the
    winshock (MS14-066) patches applied or not.
    This is done by checking if the SSL ciphers introduced by MS14-066 are
    available on the system.
    Authors:
        Stephan Peijnik <speijnik@anexia-it.com>
    注意：
    POC借鉴的是https://github.com/anexia-it/winshock-test，将shell脚本改为python;由于是调用的openssl进行远程验证，
    在测试中发现可能会出现openssl卡住而使Pocsuite卡死的情况。
    '''

    def _verify(self):
        # The OpenSSL cipher names for these ciphers are:
        MS14_066_CIPHERS = "DHE-RSA-AES256-GCM-SHA384 DHE-RSA-AES128-GCM-SHA256 AES256-GCM-SHA384 AES128-GCM-SHA256"
        # Ciphers supported by Windows Server 2012R2
        WINDOWS_SERVER_2012R2_CIPHERS = "ECDHE-RSA-AES256-SHA384 ECDHE-RSA-AES256-SHA"

        def shell_execute(args):
            stdout, _ = subprocess.Popen('echo -n | {} 2>&1'.format(' '.join(
                args)), stdout=subprocess.PIPE, stderr=subprocess.STDOUT, shell=True).communicate()
            return stdout

        def check_openssl_ciphers():
            cmd_args = ['openssl', 'ciphers']
            ciphers_openssl = shell_execute(cmd_args).decode('utf-8')
            for cipher_test in MS14_066_CIPHERS.split(' '):
                if cipher_test not in ciphers_openssl:
                    return (False, 'OpenSSL does not support {} cipher'.format(cipher_test))
            return (True, 'ok')

        def check_cipher(cipher_test, url, global_options, windows_server_2012_or_later_test=False):
            cmd_args = ['openssl', 's_client',
                        '-cipher', cipher_test, '-connect', url]
            results = shell_execute(cmd_args).decode('utf-8')
            if 'connect:errno=' in results:
                return (False, 'Connection error')
            elif 'SSL23_GET_SERVER_HELLO:unknown protocol' in results:
                return (False, 'No SSL/TLS support on target port.')
            elif 'SSL_CTX_set_cipher_list:no cipher match' in results:
                return (False, 'Your version of OpenSSL is not supported.')
            elif 'Cipher is {}'.format(cipher_test) in results or 'Cipher    : {}'.format(cipher_test) in results:
                return (True, 'SUPPORTED')
            else:
                return (True, 'UNSUPPORTED')

        def check_ms14_066_cipher(url, global_options):
            support = False
            result = []
            for cipher_test in MS14_066_CIPHERS.split(' '):
                status, response = check_cipher(
                    cipher_test, url, global_options)
                if not status:
                    raise Exception(response)
                result.append('{}:{}'.format(cipher_test, response))
                if response == 'SUPPORTED':
                    support = True
                    if global_options['patched'] == 'no':
                        global_options['patched'] = 'yes'

            return (support, result)

        def check_windows_server_2012_or_later(url, global_options):
            support = False
            result = []
            for cipher_test in WINDOWS_SERVER_2012R2_CIPHERS.split(' '):
                status, response = check_cipher(
                    cipher_test, url, global_options, windows_server_2012_or_later_test=True)
                result.append('{}:{}'.format(cipher_test, response))
                if response == 'SUPPORTED':
                    support = True
                    if global_options['windows_server_2012_or_later'] == 'no':
                        global_options['windows_server_2012_or_later'] = 'yes'
                        break

            return (support, result)

        def check_iis_443(host, global_options):
            cmd_args = ['curl', '-k', '-I', 'https://{}'.format(host)]
            results = shell_execute(cmd_args).decode('utf-8')
            if 'Microsoft-IIS' in results:
                m = re.findall(r'Server: Microsoft-IIS/(.+)', results)
                if m:
                    iis_version = m[0].strip()
                    global_options['iis_detected'] = 'yes'
                    if iis_version == '8.5':
                        global_options['windows_server_2012_or_later'] = 'yes'
                        global_options['windows_server_2012_r2'] = 'yes'
                    elif iis_version == '8.0':
                        global_options['windows_server_2012_or_later'] = 'yes'
                        global_options['windows_server_2012_r2'] = 'no'
                    return True

            return False

        def check(host, port):
            # 全局参数
            global_options = {'patched': 'no', 'windows_server_2012_or_later': 'no',
                              'windows_server_2012_r2': 'no', 'iis_detected': 'no'}
            url = '{}:{}'.format(host, port)
            # 检查是否存在openssl和支持相应的协议
            openssl_support, _ = check_openssl_ciphers()
            if not openssl_support:
                return False
            # 检查ms14-066
            support, result = check_ms14_066_cipher(url, global_options)
            # 如果是端口是443，则检查是否是IIS，并根据IIS来检测是否是windows_server_2012_or_later
            if port == 443:
                check_iis_443(host, global_options)
            if global_options['windows_server_2012_or_later'] == 'no' and global_options['iis_detected'] == 'no':
                support_2, result_2 = check_windows_server_2012_or_later(
                    url, global_options)
                support |= support_2
                result += result_2
            # 根据检测结果，判断patched状态
            if global_options['patched'] == 'yes' and global_options['windows_server_2012_or_later'] == 'no':
                PATCHED = 'YES'
            elif global_options['patched'] == 'yes':
                PATCHED = 'UNKNOWN'
                if global_options['windows_server_2012_r2'] == 'yes':
                    PATCHED = "{}: Windows_Server_2012_R2_detected.".format(
                        PATCHED)
                else:
                    PATCHED = "{}: Windows_Server_2012_or_later_detected.".format(
                        PATCHED)
            else:
                PATCHED = 'NO'
            result.append('patched:{}'.format(PATCHED))
            # 根据对SSL的支持和PATCHED结果，判断是否存在漏洞
            if support and PATCHED == 'NO':
                return (True, ' '.join(result))
            else:
                return (False, ' '.join(result))

        result = {}
        pr = urlparse(self.url)
        if pr.port:  # and pr.port not in ports:
            ports = [pr.port]
        else:
            ports = [3389, 13389, 23389]
        for port in ports:
            try:
                status, msg = check(pr.hostname, port)
                if status:
                    result['VerifyInfo'] = {}
                    result['VerifyInfo']['URL'] = '{}:{}'.format(
                        pr.hostname, port)
                    result['extra'] = {}
                    result['extra']['evidence'] = msg
                    break
            except:
                raise
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

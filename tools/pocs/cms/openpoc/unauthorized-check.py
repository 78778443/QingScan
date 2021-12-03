#!/usr/bin/python
# -*- coding: utf-8 -*-
import socket
import pymongo
import ftplib
from urllib.parse import urlparse
from pocsuite3.api import requests
from pocsuite3.api import register_poc
from pocsuite3.api import Output, POCBase
from pocsuite3.api import logger
from pocsuite3.api import POC_CATEGORY, VUL_TYPE


class TestPOC(POCBase):
    name = 'Unauthorized Check'
    vulID = '0'
    author = ['z3r0yu']
    vulType = VUL_TYPE.LOGIN_BYPASS
    category = POC_CATEGORY.EXPLOITS.REMOTE
    version = '1.0'    # default version: 1.0
    references = ['']
    desc = '''
    redis mongodb mongodb memcached elasticsearch zookeeper ftp CouchDB docker Hadoop --  Unauthorized Check
    modify from https://github.com/cwkiller/unauthorized-check
    '''
    vulDate = '2013-07-29'
    createDate = '2020-9-21'
    updateDate = '2020-9-21'
    appName = 'redis mongodb mongodb memcached elasticsearch zookeeper ftp CouchDB docker Hadoop'
    appVersion = 'All'
    appPowerLink = ''
    samples = ['pymongo']

    def redis(self, ip):
        try:
            socket.setdefaulttimeout(5)
            s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
            s.connect((ip, 6379))
            s.send(bytes("INFO\r\n", 'UTF-8'))
            result = s.recv(1024).decode()
            if "redis_version" in result:
                return ip + ":6379 redis未授权"
            s.close()
            return False
        except Exception as e:
            pass

    def mongodb(self, ip):
        try:
            conn = pymongo.MongoClient(ip, 27017, socketTimeoutMS=4000)
            dbname = conn.list_database_names()
            return ip + ":27017 mongodb未授权"
            conn.close()
        except Exception as e:
            pass

    def memcached(self, ip):
        try:
            socket.setdefaulttimeout(5)
            s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
            s.connect((ip, 11211))
            s.send(bytes('stats\r\n', 'UTF-8'))
            if 'version' in s.recv(1024).decode():
                return ip + ":11211 memcached未授权"
            s.close()
            return False
        except Exception as e:
            pass

    def elasticsearch(self, ip):
        try:
            url = 'http://' + ip + ':9200/_cat'
            r = requests.get(url, timeout=5)
            if '/_cat/master' in r.content.decode():
                return ip + ":9200 elasticsearch未授权"
            return False
        except Exception as e:
            pass

    def zookeeper(self, ip):
        try:
            socket.setdefaulttimeout(5)
            s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
            s.connect((ip, 2181))
            s.send(bytes('envi', 'UTF-8'))
            data = s.recv(1024).decode()
            s.close()
            if 'Environment' in data:
                return ip + ":2181 zookeeper未授权"
        except:
            pass

    def ftp(self, ip):
        try:
            ftp = ftplib.FTP.connect(ip, 21, timeout=5)
            ftp.login('anonymous', 'Aa@12345678')
            return ip + ":21 FTP未授权"
        except Exception as e:
            pass

    def CouchDB(self, ip):
        try:
            url = 'http://' + ip + ':5984'+'/_utils/'
            r = requests.get(url, timeout=5)
            if 'couchdb-logo' in r.content.decode():
                return ip + ":5984 CouchDB未授权"
            return False
        except Exception as e:
            pass

    def docker(self, ip):
        try:
            url = 'http://' + ip + ':2375'+'/version'
            r = requests.get(url, timeout=5)
            if 'ApiVersion' in r.content.decode():
                return ip + ":2375 docker api未授权"
            return False
        except Exception as e:
            pass

    def Hadoop(self, ip):
        try:
            url = 'http://' + ip + ':50070'+'/dfshealth.html'
            r = requests.get(url, timeout=5)
            if 'hadoop.css' in r.content.decode():
                return ip + ":50070 Hadoop未授权"
            return False
        except Exception as e:
            pass

    def _attack(self):
        """attack mode"""
        return self._verify()

    def _verify(self):
        """verify mode"""
        result = {}
        pr = urlparse(self.url)
        ip = pr.hostname
        res_info = []
        try:
            res = self.redis(ip)
            res_info.append(res)
            res = self.mongodb(ip)
            res_info.append(res)
            res = self.memcached(ip)
            res_info.append(res)
            res = self.elasticsearch(ip)
            res_info.append(res)
            res = self.zookeeper(ip)
            res_info.append(res)
            res = self.ftp(ip)
            res_info.append(res)
            res = self.CouchDB(ip)
            res_info.append(res)
            res = self.docker(ip)
            res_info.append(res)
            res = self.Hadoop(ip)
            res_info.append(res)
            res_sring = ""
            for ri in res_info:
                if ri != False:
                    res_string = res_string+";"+ri
            if res_string != "":
                result['VerifyInfo'] = {}
                result['VerifyInfo']['URL'] = ip
                result['VerifyInfo']['Info'] = res_info
        except Exception as ex:
            logger.error(str(ex))
        return self.parse_output(result)

    def parse_output(self, result):
        output = Output(self)
        if result:
            output.success(result)
        else:
            output.fail('not vulnerability')
        return output


register_poc(TestPOC)

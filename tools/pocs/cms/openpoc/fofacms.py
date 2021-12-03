#!/usr/bin/python
# -*- coding: utf-8 -*-
import json
import re
from urllib.parse import urlparse
from pocsuite3.api import requests
from pocsuite3.api import register_poc
from pocsuite3.api import Output, POCBase
from pocsuite3.api import logger
from pocsuite3.api import POC_CATEGORY, VUL_TYPE
from pocsuite3.lib.core.data import paths


class Whatcms(POCBase):
    name = 'Whatcms by Fofa'
    vulID = '0'
    author = ['z3r0yu']
    vulType = VUL_TYPE.OTHER
    category = POC_CATEGORY.EXPLOITS.REMOTE
    version = '1.0'    # default version: 1.0
    references = ['']
    desc = '''
    利用fofa规则来识别cms
    参考 https://x.hacking8.com/post-383.html
    '''
    vulDate = '2013-07-29'
    createDate = '2020-9-29'
    updateDate = '2020-9-29'
    appName = 'All CMS'
    appVersion = 'All'
    appPowerLink = ''
    samples = ['']

    class Fofacms:

        def __init__(self, html, title):
            self.html = html.lower()
            self.title = title.lower()

        def get_result(self, a):
            builts = ["(body)\s*=\s*\"", "(title)\s*=\s*\""]
            if a is True:
                return True
            if a is False:
                return False
            for regx in builts:
                match = re.search(regx, a, re.I | re.S | re.M)
                if match:
                    name = match.group(1)
                    length = len(match.group(0))
                    content = a[length: -1]
                    if name == "body":
                        if content.lower() in self.html:
                            return True
                        else:
                            return False
                    elif name == "title":
                        if content.lower() in self.title:
                            return True
                        else:
                            return False
            raise Exception("不能识别的a:" + str(a))

        def calc_express(self, expr):
            #  title="NBX NetSet" || (header="Alternates" && body="NBX")
            #  1||(2&&3) => 1 2 3 && ||
            # header="X-Copyright: wspx" || header="X-Powered-By: ANSI C"
            # header="SS_MID" && header="squarespace.net"
            expr = self.in2post(expr)
            # print("后缀表达式", expr)

            stack = []
            special_sign = ["||", "&&"]
            if len(expr) > 1:
                for exp in expr:
                    if exp not in special_sign:
                        stack.append(exp)
                    else:
                        a = self.get_result(stack.pop())
                        b = self.get_result(stack.pop())
                        c = None
                        if exp == "||":
                            c = a or b
                        elif exp == "&&":
                            c = a and b
                        stack.append(c)
                if stack:
                    return stack.pop()
            else:
                return self.get_result(expr[0])

        def in2post(self, expr):
            """ :param expr: 前缀表达式
                :return: 后缀表达式

                Example：
                    1||(2&&3) => 1 2 3 && ||
            """
            stack = []  # 存储栈
            post = []  # 后缀表达式存储
            special_sign = ["&&", "||", "(", ")"]
            builts = ["body\s*=\s*\"", "title\s*=\s*\""]

            exprs = []
            tmp = ""
            in_quote = 0  # 0未发现 1发现 2 待验证状态
            for z in expr:
                is_continue = False
                tmp += z
                if in_quote == 0:
                    for regx in builts:
                        if re.search(regx, tmp, re.I):
                            in_quote = 1
                            is_continue = True
                            break
                elif in_quote == 1:
                    if z == "\"":
                        in_quote = 2
                if is_continue:
                    continue
                for i in special_sign:
                    if tmp.endswith(i):

                        if i == ")" and in_quote == 2:
                            # 查找是否有左括号
                            zuo = 0
                            you = 0
                            for q in exprs:
                                if q == "(":
                                    zuo += 1
                                elif q == ")":
                                    you += 1
                            if zuo - you < 1:
                                continue
                        # print(": " + tmp + " " + str(in_quote))
                        length = len(i)
                        _ = tmp[0:-length]
                        if in_quote == 2 or in_quote == 0:
                            if in_quote == 2 and not _.strip().endswith("\""):
                                continue
                            if _.strip() != "":
                                exprs.append(_.strip())
                            exprs.append(i)
                            tmp = ""
                            in_quote = 0
                            break
            if tmp != "":
                exprs.append(tmp)
            if not exprs:
                return [expr]
            # print("分离字符", exprs)
            for z in exprs:
                if z not in special_sign:  # 字符直接输出
                    post.append(z)
                else:
                    # stack 不空；栈顶为（；优先级大于
                    if z != ')' and (not stack or z == '(' or stack[-1] == '('):
                        stack.append(z)  # 运算符入栈

                    elif z == ')':  # 右括号出栈
                        while True:
                            x = stack.pop()
                            if x != '(':
                                post.append(x)
                            else:
                                break

                    else:  # 比较运算符优先级，看是否入栈出栈
                        while True:
                            if stack and stack[-1] != '(':
                                post.append(stack.pop())
                            else:
                                stack.append(z)
                                break
            while stack:  # 还未出栈的运算符，需要加到表达式末尾
                post.append(stack.pop())
            return post

    def read_config(self):
        config_file = paths.FOFA_CMS_RULE
        with open(config_file, 'r') as f:
            mark_list = json.load(f)
        return mark_list

    def fingerprint(self, body):
        mark_list = self.read_config()
        # title
        m = re.search('<title>(.*?)<\/title>', body, re.I | re.M | re.S)
        title = ""
        if m:
            title = m.group(1).strip()
        fofa = self.Fofacms(body, title)
        whatweb = ""
        for item in mark_list:
            express = item["rule"]
            name = item["name"]
            # print("rule:" + express)
            try:
                if fofa.calc_express(express):
                    whatweb = name.lower()
                    break
            except Exception:
                print("fofacms error express:{} name:{}".format(express, name))
        return whatweb

    def _attack(self):
        """attack mode"""
        return self._verify()

    def _verify(self):
        """verify mode"""
        result = {}
        target = self.url

        try:
            resp = requests.get(target).text
            cms_info = self.fingerprint(resp)
            if cms_info != "":
                result['VerifyInfo'] = {}
                result['VerifyInfo']['URL'] = target
                result['VerifyInfo']['Info'] = cms_info
        except Exception as ex:
            logger.error(str(ex))
        return self.parse_output(result)

    def parse_output(self, result):
        output = Output(self)
        if result:
            output.success(result)
        else:
            output.fail('not vulnerability')
        return output


register_poc(Whatcms)

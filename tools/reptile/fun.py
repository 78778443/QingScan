import requests
import json


def dingding(message, token, keyword):
    url = "https://oapi.dingtalk.com/robot/send?access_token="+token
    header = {
        "Content-Type": "application/json;charset=utf-8",
    }
    param = {
        "msgtype": "text",
        "text": {
            "content": keyword + "：" + message
        }
    }
    req = requests.post(url, json.dumps(param), headers=header)
    return req.json()
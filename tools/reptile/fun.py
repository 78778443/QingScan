import requests
import json
import yaml
import pymysql

with open("config.yaml", "r", encoding="utf8") as f:
    config = yaml.load(f, Loader=yaml.SafeLoader)

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

def db():
    mysql_config = config['mysql']
    db = pymysql.connect(
        host=mysql_config['host'],
        port=mysql_config['port'],
        user=mysql_config['username'],
        passwd=mysql_config['password'],
        db=mysql_config['database']
    )
    return db
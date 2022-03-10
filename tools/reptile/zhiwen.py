import re, ssl
import requests
import pymysql
import time
import urllib3
from fun import db
urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)
# 全局取消证书验证
ssl._create_default_https_context = ssl._create_unverified_context

header = {
    "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.45 Safari/537.36 Edg/96.0.1054.29"
}

db = db()
cur = db.cursor(cursor=pymysql.cursors.DictCursor)
sql = "select id,url,user_id from app where `is_delete` = 0"
cur.execute(sql)
# 使用 fetchall() 获取所有查询结果
list = cur.fetchall()
for i in list:
    id = i['id']
    url = i['url']
    user_id = i['user_id']

    req = requests.get(url, headers=header,verify=False)
    if req.status_code == 200:
        star_reg = "<div class='explore-project__meta-social pull-right'>.*?<div class='stars-count' data-count=.*?>(.*?)</div>.*?</div>"
        star_list = re.findall(star_reg, req.text, re.M | re.I | re.S)

        print(req.text)

    time.sleep(3)
# 关闭游标
cur.close()
# 关闭数据库连接
db.close()
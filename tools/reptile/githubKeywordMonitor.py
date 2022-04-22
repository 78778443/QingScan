# github关键字监控

import ssl
import requests
import pymysql
import time
import datetime
import urllib3
from fun import dingding
from fun import db
urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)
# 全局取消证书验证
ssl._create_default_https_context = ssl._create_unverified_context

db = db()
cur = db.cursor(cursor=pymysql.cursors.DictCursor)
sql = "select value from system_config where `key` = 'github_token'"
cur.execute(sql)
# 使用 fetchall() 获取所有查询结果
github_token = cur.fetchall()
url = "https://api.github.com/search/code"
header = {
    "Accept": "application/vnd.github.v3+json",
    "Authorization": "token "+github_token[0]['value'],
    "User-Agent": "https://api.github.com/meta"
}
threeDayAgo = (datetime.datetime.now() - datetime.timedelta(days=15))
# 转换为时间戳
timeStamp = int(time.mktime(threeDayAgo.timetuple()))
# 转换为其他字符串格式
where_scan_time = threeDayAgo.strftime("%Y-%m-%d %H:%M:%S")

sql = "select id,user_id,title,app_id from github_keyword_monitor where scan_time <= '" + where_scan_time + "'"
cur.execute(sql)
# 使用 fetchall() 获取所有查询结果
keyword_list = cur.fetchall()
for i in keyword_list:
    param = {
        "q": i['title'],
        "per_page": 10,
    }
    req = requests.get(url=url, params=param, headers=header)
    list = req.json()['items']
    data = {}
    for j in list:
        data['parent_id'] = i['id']
        data['user_id'] = i['user_id']
        data['app_id'] = i['app_id']
        data['keyword'] = i['title']
        data['name'] = j['repository']['full_name']
        data['path'] = j['path']
        data['html_url'] = j['html_url']
        data['create_time'] = time.strftime("%Y-%m-%d %H:%M:%S", time.localtime())

        sql = "select count(id) as num from github_keyword_monitor_notice where parent_id = "+str(data['parent_id'])+" and user_id = "+str(data['user_id'])+" and keyword = '" + data['keyword'] + "' and name = '"+data['name']+"' and path = '"+data['path']+"' and html_url = '"+data['html_url']+"' and app_id = "+str(data['app_id'])
        cur.execute(sql)
        # 使用 fetchall() 获取所有查询结果
        result = cur.fetchall()
        if result[0]['num'] == 0:
            insert = 'insert into github_keyword_monitor_notice(parent_id,user_id,keyword,name,path,html_url,create_time,app_id) value(%s,%s,%s,%s,%s,%s,%s,%s)'
            cur.execute(insert, (data['parent_id'],data['user_id'],data['keyword'], data['name'], data['path'], data['html_url'], data['create_time'], data['app_id']))
            db.commit()
            # 获取用户的钉钉token
            sql = "select nickname,dd_token from user where  id = " + str(data['user_id'])
            cur.execute(sql)
            # 使用 fetchall() 获取所有查询结果
            user = cur.fetchall()[0]
            if user['dd_token']:
                res = dingding(data['keyword'], user['dd_token'], '最新消息')
                time.sleep(5)
    time.sleep(3)
# 关闭游标
cur.close()
# 关闭数据库连接
db.close()
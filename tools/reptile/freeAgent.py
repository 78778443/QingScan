import re, ssl

# 全局取消证书验证
ssl._create_default_https_context = ssl._create_unverified_context
import requests
import pymysql
import time

url = "https://ip.jiangxianli.com/?page="
head = {
    "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.45 Safari/537.36 Edg/96.0.1054.29"}

db = pymysql.connect(
    host="DB_HOST",
    port=3306,
    user="qingscan",
    passwd="password",
    db="QingScan"
)
# 通过 cursor() 创建游标对象，并让查询结果以字典格式输出
cur = db.cursor(cursor=pymysql.cursors.DictCursor)
cur.execute('truncate table proxy')

page = 1
num = 10
while page <= num:
    req = requests.get(url + str(page), headers=head)
    page += 1
    if req.status_code == 200:
        list_reg = '<tbody><tr>.*?</tr></tbody>'
        list = re.findall(list_reg, req.text, re.M | re.I | re.S)
        if len(list) == 0:
            break

        data_reg = '<button class="layui-btn layui-btn-sm btn-speed " data-url=".*?" data-protocol="http" data-ip="(.*?)" data-port="(.*?)" data-unique-id=".*?">测速 </button><'
        data = re.findall(data_reg, list[0], re.M | re.I | re.S)
        for i in data:
            get_agent_url = 'https://ip.jiangxianli.com/api/web-request-speed?protocol=http&ip=' + i[0] + '&port=' + i[
                1] + '&web_link=http://www.baidu.com'
            result = requests.get(get_agent_url)
            if result.status_code == 200:
                status = 1
            else:
                status = 0
            create_time = time.strftime("%Y-%m-%d %H:%M:%S", time.localtime())
            insert = 'insert into proxy(host,port,status,create_time) value(%s,%s,%s,%s)'
            try:
                cur.execute(insert, (i[0], i[1], status, create_time))
                db.commit()
            except:
                db.rollback()
    time.sleep(3)
# 关闭游标
cur.close()
# 关闭数据库连接
db.close()
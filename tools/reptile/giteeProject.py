import re, ssl

# 全局取消证书验证
ssl._create_default_https_context = ssl._create_unverified_context
import requests
import pymysql
import time
from datetime import datetime

url = "https://gitee.com/explore/all?order=starred&page="
head = {
    "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.45 Safari/537.36 Edg/96.0.1054.29"}

data = {}

db = pymysql.connect(
    host="DB_HOST",
    port=3306,
    user="qingscan",
    passwd="password",
    db="QingScan"
)
# 通过 cursor() 创建游标对象，并让查询结果以字典格式输出
cur = db.cursor(cursor=pymysql.cursors.DictCursor)

page = 1
num = 100
while page <= num:
    req = requests.get(url + str(page), headers=head)
    page += 1

    if req.status_code == 200:
        star_reg = "<div class='explore-project__meta-social pull-right'>.*?<div class='stars-count' data-count=.*?>(.*?)</div>.*?</div>"
        star_list = re.findall(star_reg, req.text, re.M | re.I | re.S)

        reg = '<a title=".*?" target="_blank" class="title project-namespace-path" sa_evt="repoClick" sa_location="开源全部推荐项目" sa_url=".*?" sa_repo_id=".*?" href="(.*?)">(.*?)</a>'
        list = re.findall(reg, req.text, re.M | re.I | re.S)
        j = 0
        for i in list:
            name = i[0].split('/')[1]
            ssh_url = 'git@gitee.com:' + i[0][1:] + '.git'
            ssh_url1 = 'https://gitee.com/' + i[0] + '.git'
            star = star_list[j]
            j += 1

            # 使用 execute() 执行sql
            sql = "select count(id) as num from code where ssh_url in('" + ssh_url + "','" + ssh_url1 + "')"
            cur.execute(sql)
            # 使用 fetchall() 获取所有查询结果
            result = cur.fetchall()
            if result[0]['num'] == 0:
                data['name'] = name
                data['ssh_url'] = ssh_url
                data['pulling_mode'] = 'SSH'
                data['star'] = star

                insert = 'insert into code(name,ssh_url,pulling_mode,star) value(%s,%s,%s,%s)'

                # table = 'code'
                # keys = ', '.join(data.keys())
                # values = ', '.join(['%s'] * len(data))
                # sql = 'INSERT INTO {table}({keys}) VALUES ({values})'.format(table=table, keys=keys, values=values)

                try:
                    cur.execute(insert,(name,ssh_url,'SSH',star))
                    db.commit()
                except:
                    db.rollback()
    time.sleep(3)
# 关闭游标
cur.close()
# 关闭数据库连接
db.close()
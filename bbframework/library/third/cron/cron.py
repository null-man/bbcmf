#coding=utf-8

## +----------------------------------------------------------------------
## | Tool.Pub [ All tools in it! ]
## +----------------------------------------------------------------------
## | Copyright (c) 2016 http://www.tool.pub All rights reserved.
## +----------------------------------------------------------------------
## | Author: Null.Yang <635384073@qq.com> <http://do.rog.cn>
## +----------------------------------------------------------------------
import mysql.connector
import threading
import urllib2
import time
import sqlite3
import os
import json
        
# 访问url方法
def _atkUrl(url):
    strHtml = urllib2.urlopen(url).read()
    return strHtml



# 获取绝对路径
def _getpwd(path):
    return os.path.abspath(os.path.join(os.path.dirname(__file__), str(path)[0:]))



# 新建一个线程
def thrs(path):
    threads = []
    for i in range(1):
        threads.append(threading.Thread(target=open_url(path)))

    for t in threads:
        t.setDaemon(True)
        t.start()
    t.join()



# 打开url
def open_url(path):
    _atkUrl(path)



# 入口函数
if __name__ == '__main__':

    ############### mysql ##################

    # 获取配置
    pwd = _getpwd('database.json')
    f=open(pwd)
    config = json.load(f)

    try:
        cnn=mysql.connector.connect(**config)

        cursor=cnn.cursor()
        try:
            # 获取控制调度的url
            cursor.execute("select * from tp_task_config where key_name='cron_url'")
            for v in cursor:
                cron_url = v[2]

            # 打开已经开启的调度
            cursor.execute("select * from tp_task where is_on=1")

            for url in cursor.fetchall():
                id = str(url[0])
                print cron_url+id
                # 开启一个新线程,打开调度url
                thrs(cron_url+id)

        except mysql.connector.Error as e:
            print('query error!{}'.format(e))
        finally:
            cursor.close()
            cnn.close()

    except Exception, e:
        print(e)


    ################## sqlite3 ##################
    # 获取数据库绝对路径
    # pwd = _getpwd('cron.db')
    # cx = sqlite3.connect(pwd)

    # # 获取调度的url
    # cron_url_data = cx.execute("select * from task_config where key_name='cron_url'")

    # for v in cron_url_data:
    #     cron_url = v[2];

    # cu = cx.execute("select * from task where is_on=1")
    # for url in cu.fetchall():
    #     id = str(url[0])
    #     print cron_url+id
    #     # 开启一个新线程,打开调度url
    #     thrs(cron_url+id)

    # # 关闭数据库
    # cx.close()
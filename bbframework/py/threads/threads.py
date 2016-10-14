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
def _atkUrl(config, url, timeout):
    # sql
    sql = "insert into "+ config.get('prefix') + "threads_log(url, time, keyword) values ('%s', '%s', '%s')"

    while True:
        configs = {}
        # 获取配置数据
        try:
            cnn = mysql.connector.connect(host = config.get('host'), user = config.get('user'), passwd = config.get('password'), db = config.get('database'), port = config.get('port'))
            cursor = cnn.cursor()
        
            try:
                cursor.execute("select * from " + config.get('prefix') + "config")
                
                for v in cursor.fetchall():
                    configs[v[1]] = v[2]                
                
                if configs['switch'] == "1":

                    # 超时处理
                    try:
                        strHtml = urllib2.urlopen(url, timeout = timeout).read()
                        # print strHtml
                    
                    except Exception, e:
                        print e
                        sql = sql%(url, time.strftime( '%Y-%m-%d %X', time.localtime() ), e)
                        
                        try:
                            cursor.execute(sql)
                            # 提交到数据库执行
                            cnn.commit()
                        except Exception, e:
                            print e

                        # 出现异常时候，关闭while
                        break;
                else:
                    pass
                    # print "已关闭"

            except mysql.connector.Error as e:
                print('query error!{}'.format(e))

                sql = sql%(url, time.strftime( '%Y-%m-%d %X', time.localtime() ), 'query error!{}'.format(e))
                try:
                    cursor.execute(sql)
                    # 提交到数据库执行
                    cnn.commit()
                except Exception, e:
                    print e

                # 出现异常时候，关闭while
                break;

            finally:
                cursor.close()
                cnn.close()

        except Exception, e:
            print e
            
            # 出现异常时候，关闭while
            break;
        

# 获取绝对路径
def _getpwd(path):
    return os.path.abspath(os.path.join(os.path.dirname(__file__), str(path)[0:]))



# 新建x个线程
def thrs(config, timeout):
    threads = []

    configs = {}
    # 获取配置数据
    try:
        cnn=mysql.connector.connect(host=config.get('host'), user=config.get('user'), passwd=config.get('password'), db=config.get('database'), port=config.get('port'))

        cursor=cnn.cursor()
        try:
            cursor.execute("select * from " + config.get('prefix') + "config")
            
            for v in cursor.fetchall():
                configs[v[1]] = v[2]   

        except mysql.connector.Error as e:
            print('query error!{}'.format(e))
        finally:
            cursor.close()
            cnn.close()

    except Exception, e:
        print(e)

    for i in range(int(configs['threads'])):
        threads.append(threading.Thread(target=open_url(config, configs['url'], timeout)))
        # print configs['url']

    for t in threads:
        t.setDaemon(True)
        t.start()
    t.join()



# 打开url
def open_url(config, path, timeout):
    _atkUrl(config, path, timeout)



#调用主函数
def main(config_path, timeout = ''):
    import sys

    if config_path == '':
        config_path = 'database.json'
    pwd = os.path.abspath(os.path.join(sys.path[0], config_path))

    f = open(pwd)
    config = json.load(f)

    if timeout == '':
        timeout = 30

    thrs(config, timeout)
            
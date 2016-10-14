#coding=utf-8

# +----------------------------------------------------------------------
# | 复制该文件到对应模块py文件夹下
# | 修改root路径为项目路径(建议用相对路径)
# | database为数据库配置文件
# | database为空时默认读取当前文件夹下的database.json文件
# | 最后执行crontab -e
# | 添加 * * * * * python 该文件的绝对路径
# | 即可完成定时配置
# +----------------------------------------------------------------------

root = '../..'
database = ''

import os
import sys

if not os.path.isabs(root):
	root = os.path.abspath(os.path.join(sys.path[0], root))
sys.path.append(os.path.join(root, "bbframework/py/cron/"))
import cron
cron.main(database)

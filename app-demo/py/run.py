#coding=utf-8

# +----------------------------------------------------------------------
# | 复制该文件到对应模块py文件夹下
# | 修改root路径为项目路径(相对于该文件的路径)
# | database为数据库配置文件
# | database为空时默认读取当前文件夹下的database.json文件
# | 最后执行python 
# | 即可启动多线程任务
# +----------------------------------------------------------------------

root = '../..'
database = ''

import sys
import os

if not os.path.isabs(root):
	root = os.path.abspath(os.path.join(sys.path[0], root))
sys.path.append(os.path.join(root, "bbframework/py/threads/"))
import threads
threads.main(database)
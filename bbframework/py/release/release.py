# -*- coding: utf-8 -*-

import sys
reload(sys)
sys.setdefaultencoding('utf-8')

import glob
import os
import shutil

todir = '/Applications/XAMPP/xamppfiles/htdocs/web/bbframework/release'
todir2 = '/Applications/XAMPP/xamppfiles/htdocs/web/bbframework'

def version_check(version, cur_version):
	return False


def main():

	todirfull = os.path.abspath(todir)
	print todirfull

	bbframework = os.path.dirname(todirfull)

	cur_version = '0.0.0.0'

	if os.path.exists(todirfull):
		cur_version = open(os.path.join(todirfull, 'VERSION')).read()
		print 'current release version is %s, are you sure to modify it ? [Y/N]' % cur_version
		while 1:
			inputStr = raw_input()
			if inputStr == "N" or inputStr == "n":
				print 'Stop to release'
				return
			elif inputStr == "Y" or inputStr == "y":
				print 'Continue to release'
				shutil.rmtree(todirfull)
				break

	version = raw_input('enter release version:')

	if version == '':
		version = cur_version

	os.mkdir(todirfull)

	for i in ['thinkphp','app-hello', 'bbframework', 'app-cmf']:
		shutil.copytree(os.path.join(bbframework, i), os.path.join(todirfull, i))

	for i in ['application', 'public']:
		shutil.copytree(os.path.join(todir2, i), os.path.join(todirfull, i))


	shutil.copy(os.path.join(bbframework, 'public', 'index.php'), os.path.join(todirfull, 'public'))
	# shutil.copytree(os.path.join(bbframework, 'public', 'static', 'demo'), os.path.join(todirfull, 'public', 'static', 'demo'))
	# shutil.copytree(os.path.join(bbframework, 'public', 'static', 'demo'), os.path.join(todirfull, 'app-demo', 'public', 'static', 'demo'))

	open(os.path.join(todirfull, 'VERSION'), 'w').write(version)


	# print cur_version

if __name__ == '__main__':
	main()





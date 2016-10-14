# -*- coding: utf-8 -*-

"""
Copyright (c) 2014-2015 babybus.com

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
"""

"""
@module: zip_package
@copyright: babybus.com 2014-2015
@license: MIT
@author: SuperDo - Zergling
"""

import zipfile
import glob
import os
import sys

package = '../..//pyenv/packages'

def zip_dir(dirname, zipfilename):
    filelist = []
    #Check input ...
    fulldirname = os.path.abspath(dirname)
    fullzipfilename = os.path.abspath(zipfilename)
    print "Start to zip %s to %s ..." % (fulldirname, fullzipfilename)
    if not os.path.exists(fulldirname):
        print "Dir/File %s is not exist, Press any key to quit..." % fulldirname
        inputStr = raw_input()
        return
    if os.path.isdir(fullzipfilename):
        tmpbasename = os.path.basename(dirname)
        fullzipfilename = os.path.normpath(os.path.join(fullzipfilename, tmpbasename))
    # if os.path.exists(fullzipfilename):    
    #     print "%s has already exist, are you sure to modify it ? [Y/N]" % fullzipfilename
    #     while 1:
    #         inputStr = raw_input()
    #         if inputStr == "N" or inputStr == "n" :
    #             return
    #         else:
    #             if inputStr == "Y" or inputStr == "y" :
    #                 print "Continue to zip files..."
    #                 break

    #Get file(s) to zip ...
    if os.path.isfile(dirname):
        filelist.append(dirname)
        dirname = os.path.dirname(dirname)
    else:
        #get all file in directory
        for root, dirlist, files in os.walk(dirname):
            for filename in files:
                filelist.append(os.path.join(root,filename))

    #Start to zip file ...
    destZip = zipfile.ZipFile(fullzipfilename, "w")
    for eachfile in filelist:
        destfile = eachfile[len(dirname):]
        print "Zip file %s..." % destfile
        destZip.write(eachfile, destfile)
    destZip.close()
    print "Zip folder succeed!"
    
def unzip_dir(zipfilename, unzipdirname):
    fullzipfilename = os.path.abspath(zipfilename)
    fullunzipdirname = os.path.abspath(unzipdirname)
    print "Start to unzip file %s to folder %s ..." % (zipfilename, unzipdirname)
    #Check input ...
    if not os.path.exists(fullzipfilename):
        print "Dir/File %s is not exist, Press any key to quit..." % fullzipfilename
        inputStr = raw_input()
        return
    if not os.path.exists(fullunzipdirname):
        os.mkdir(fullunzipdirname)
    else:
        if os.path.isfile(fullunzipdirname):
            print "File %s is exist, are you sure to delet it first ? [Y/N]" % fullunzipdirname
            while 1:
                inputStr = raw_input()
                if inputStr == "N" or inputStr == "n":
                    return
                else:
                    if inputStr == "Y" or inuptStr == "y":
                        os.remove(fullunzipdirname)
                        print "Continue to unzip files ..."
                        break
            
    #Start extract files ...
    srcZip = zipfile.ZipFile(fullzipfilename, "r")
    for eachfile in srcZip.namelist():
        print "Unzip file %s ..." % eachfile
        eachfilename = os.path.normpath(os.path.join(fullunzipdirname, eachfile))
        eachdirname = os.path.dirname(eachfilename)
        if not os.path.exists(eachdirname):
            os.makedirs(eachdirname)
        fd=open(eachfilename, "wb")
        fd.write(srcZip.read(eachfile))
        fd.close()
    srcZip.close()
    print "Unzip file succeed!"

def print_help(toolname):
    print """
    This program can zip given folder to destination file, or unzip given zipped file to destination folder.
    Usage: %s [option] [arg]...
    -h: print this help message and exit (also --help)
    -u unzip: unzip given zipped file to destination folder,
        usage: %s -u/-unzip zipfilename, unzipdirname
    -z zip: zip given folder to destination file
        usage: %s -z/zip dirname, zipfilename
    """  % (toolname, toolname, toolname)

if __name__ == '__main__':
	dirs = filter(os.path.isdir, glob.glob('*'))
	for i in dirs:
		zipfilename = os.path.join(package, i)
		zip_dir(i, zipfilename)

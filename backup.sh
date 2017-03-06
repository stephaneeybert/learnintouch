#!/bin/sh

filename=engine

zipfile=$filename.zip

currentDir=`pwd`

cd ..

zip -r $zipfile $filename -x "*.svn/*" "*.git/*" "*/node_modules/*" "*/bower_components/*" "*/.*" "build.log*"

mv -f $zipfile /home/stephane/backup

cd $currentDir

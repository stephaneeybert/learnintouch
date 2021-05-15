#!/bin/bash

basedir=/usr/local/learnintouch/www

# Entering each website home directory is required so as to point to its specific properties file

cd $basedir/europasprak.com
./backup.sh &

cd $basedir/fhs.europasprak.com
./backup.sh &

cd $basedir/learnintouch.com
./backup.sh &

#cd $basedir/thalasoft.com
#./backup.sh &


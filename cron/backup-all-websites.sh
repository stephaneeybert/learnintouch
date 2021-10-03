#!/bin/bash

basedir=/home/europasprak/dev/learnintouch

# Entering each website home directory is required so as to point to its specific properties file

cd $basedir/www.europasprak
source ./setenv.sh
./backup.sh

cd $basedir/www.fhs
source ./setenv.sh
./backup.sh


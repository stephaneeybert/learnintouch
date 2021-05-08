#!/bin/sh

export NODE_PATH=~/programs/install/lib/node_modules

export NODE_ENV=production

nohup ~/programs/install/bin/node ~/learnintouch/engine/api/js/socket/elearning-server.js 2>&1 >> ~/learnintouch/engine/api/js/socket/nodejs.log &


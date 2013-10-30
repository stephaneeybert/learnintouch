#!/bin/sh

export NODE_PATH=/home/stephane/programs/install/lib/node_modules

export NODE_ENV=production

nohup /home/stephane/programs/install/bin/node /home/stephane/learnintouch/engine/js/socket/elearning-server.js 2>&1 >> /home/stephane/learnintouch/engine/js/socket/nodejs.log &


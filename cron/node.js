#!/bin/sh

if curl --fail http://127.0.0.1:9001/ping || exit 1; then
  echo "NodeJS is responding"
else
  echo "NodeJS is not responding"

  /etc/init.d/learnintouch-nodejs stop
  /etc/init.d/learnintouch-nodejs start
fi

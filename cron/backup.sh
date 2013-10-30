#!/bin/bash

/usr/local/bin/php -d include_path='/home/learnintouch/engine/setup/' -f /home/learnintouch/engine/system/cron/backup.php $PWD/account/setup/specific.php

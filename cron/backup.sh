#!/bin/bash

/home/stephane/programs/install/bin/php -d include_path='/home/stephane/learnintouch/engine/setup/' -f /home/stephane/learnintouch/engine/system/cron/backup.php $PWD/account/setup/specific.php

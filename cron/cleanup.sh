#!/bin/bash

/usr/local/bin/php -d include_path='/home/learnintouch/engine/setup/' -f /home/learnintouch/engine/system/cron/cleanup.php $PWD/account/setup/specific.php

/usr/local/bin/php -d include_path='/home/learnintouch/engine/setup/' -f /home/learnintouch/engine/system/cron/cleanupFiles.php $PWD/account/setup/specific.php


#!/bin/bash

/home/stephane/programs/install/bin/php -d include_path='/home/stephane/dev/php/learnintouch/engine/setup/' -f /home/stephane/dev/php/learnintouch/engine/system/cron/refresh_template_styling_tags.php $PWD/account/setup/specific.php
#/usr/local/bin/php -d include_path='/home/learnintouch/engine/setup/' -f /home/learnintouch/engine/system/cron/refresh_template_styling_tags.php $PWD/account/setup/specific.php

/home/stephane/programs/install/bin/php -d include_path='/home/stephane/dev/php/learnintouch/engine/setup/' -f /home/stephane/dev/php/learnintouch/engine/system/cron/refresh_template_models.php $PWD/account/setup/specific.php
#/usr/local/bin/php -d include_path='/home/learnintouch/engine/setup/' -f /home/learnintouch/engine/system/cron/refresh_template_models.php $PWD/account/setup/specific.php

#permissions.sh

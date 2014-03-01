#!/bin/bash

/home/stephane/programs/install/bin/php -d include_path='/home/stephane/learnintouch/engine/setup/' -f /home/stephane/learnintouch/engine/system/cron/refresh_template_styling_tags.php $PWD/account/setup/specific.php

/home/stephane/programs/install/bin/php -d include_path='/home/stephane/learnintouch/engine/setup/' -f /home/stephane/learnintouch/engine/system/cron/refresh_template_models.php $PWD/account/setup/specific.php

/home/stephane/programs/install/bin/php -d include_path='/home/stephane/learnintouch/engine/setup/' -f /home/stephane/learnintouch/engine/system/cron/refresh_images.php $PWD/account/setup/specific.php


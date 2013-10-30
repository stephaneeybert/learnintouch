#!/bin/bash

php -d include_path='engine/setup/' -f engine/system/cron/update.php $PWD/account/setup/specific.php

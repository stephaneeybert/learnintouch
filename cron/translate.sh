#!/bin/bash

for file in $(find modules system -type f -name "*Utils.php" -not -name ".*"); do
  php -d include_path='setup/' -f system/cron/translate.php /home/stephane/dev/php/sites/thalasoft/websites/thalasoft.com/account/setup/specific.php $file
done

chown -R stephane:stephane *
chmod -R 755 *

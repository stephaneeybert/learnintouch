#!/bin/bash -x

# Expand the secrets
export DB_ROOT_PASSWORD={{DOCKER-SECRET:DB_ROOT_PASSWORD}}
export LEARNINTOUCH_DB_NAME={{DOCKER-SECRET:LEARNINTOUCH_DB_NAME}}
export LEARNINTOUCH_DB_USER={{DOCKER-SECRET:LEARNINTOUCH_DB_USER}}
export LEARNINTOUCH_DB_PASSWORD={{DOCKER-SECRET:LEARNINTOUCH_DB_PASSWORD}}
export WWW_LEARNINTOUCH_DB_PASSWORD={{DOCKER-SECRET:WWW_LEARNINTOUCH_DB_PASSWORD}}
export WWW_EUROPASPRAK_DB_PASSWORD={{DOCKER-SECRET:WWW_EUROPASPRAK_DB_PASSWORD}}
export WWW_FHS_DB_PASSWORD={{DOCKER-SECRET:WWW_FHS_DB_PASSWORD}}
export WWW_FOLKUNIVERSITET_DB_PASSWORD={{DOCKER-SECRET:WWW_FOLKUNIVERSITET_DB_PASSWORD}}
export WWW_THALASOFT_DB_PASSWORD={{DOCKER-SECRET:WWW_THALASOFT_DB_PASSWORD}}
source /usr/local/learnintouch/expand-secrets.sh
#/usr/local/learnintouch/expand-all-secrets.sh TODO DRY ~/dev/docker/projects/learnintouch/learnintouch/start.sh

export MYSQL_HOME=/usr/local/mariadb/install # TODO How to set the PATH in Docker instead ?
export PATH=$PATH:$MYSQL_HOME/bin

/usr/local/php/install/bin/php -d include_path='/usr/local/learnintouch/engine/setup/' -f /usr/local/learnintouch/engine/system/cron/backup.php $PWD/account/setup/specific.php >> /usr/local/learnintouch/logs/cron.log 2>&1

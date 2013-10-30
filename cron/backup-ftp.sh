#!/bin/sh

# To simply log in use the command:
# ncftp -u vps13495.ovh.net -p Q8WZZRTQpGI ftpback-rbx3-160.ovh.net
# Voir aussi la discussion http://forum.ovh.com/showthread.php?t=77995
# To delete use the lftp program
# lftp -u vps13495.ovh.net,Q8WZZRTQpGI ftpback-rbx3-160.ovh.net
# rm -fr ...

host=ftpback-rbx3-160.ovh.net
user=vps13495.ovh.net
password=Q8WZZRTQpGI

putDir() {
  local destinationDir=$1
  local sourceFile=$2
  ncftpput -u $user -p $password -m -v -R $host $destinationDir $sourceFile
  return 1
}

dayofweek=$(date +%u)

sourceDir=/home/learnintouch/www/*/account/*.tar

destinationDir=$dayofweek/

for file in `ls $sourceDir`
do
  putDir $destinationDir $file;
done
exit


#!/bin/sh

tarfile='language-francais.tar'

gzfile=$tarfile'.gz'

echo $tarfile;

tar -cvf $tarfile `find . -name ".*.fr.php"`

gzip $tarfile


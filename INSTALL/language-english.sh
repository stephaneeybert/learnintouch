#!/bin/sh

tarfile='language-english.tar'

gzfile=$tarfile'.gz'

echo $tarfile;

tar -cvf $tarfile `find . -name ".*.en.php"`

gzip $tarfile


#!/bin/sh

tarfile='language-svenska.tar'

gzfile=$tarfile'.gz'

echo $tarfile;

tar -cvf $tarfile `find . -name ".*.se.php"`

gzip $tarfile


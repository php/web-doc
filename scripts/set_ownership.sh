#!/bin/sh

# get paths
. `dirname $0`/../build-ops

if [ `whoami` = 'root' ]
then
  if [ ! -d $FILESDIR ]
  then
    echo "Making files dir: $FILESDIR"
    mkdir $FILESDIR
  fi
  echo "Setting ownership of files dir ($FILESDIR) to $WWWUSER.$WWWGROUP"
  chown -R $WWWUSER $FILESDIR
  chgrp -R $WWWGROUP $FILESDIR

  if [ ! -d $SQLITEDIR ]
  then
    echo "Making SQLite dir: $SQLITEDIR"
    mkdir $SQLITEDIR
  fi
  echo "Setting ownership of SQLite dir ($SQLITEDIR) to $WWWUSER.$WWWGROUP"
  chown -R $WWWUSER $SQLITEDIR
  chgrp -R $WWWGROUP $SQLITEDIR

  echo "Done."
else
  echo "Must be root."
fi

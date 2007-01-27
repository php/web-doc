#!/bin/bash
# +----------------------------------------------------------------------+
# | PHP Documentation Tools Site Source Code                             |
# +----------------------------------------------------------------------+
# | Copyright (c) 1997-2004 The PHP Group                                |
# +----------------------------------------------------------------------+
# | This source file is subject to version 3.0 of the PHP license,       |
# | that is bundled with this package in the file LICENSE, and is        |
# | available at through the world-wide-web at                           |
# | http://www.php.net/license/3_0.txt.                                  |
# | If you did not receive a copy of the PHP license and are unable to   |
# | obtain it through the world-wide-web, please send a note to          |
# | license@php.net so we can mail you a copy immediately.               |
# +----------------------------------------------------------------------+
# | Authors: Jacques Marneweck <jacques@php.net>                         |
# +----------------------------------------------------------------------+
#
# $Id: populatedocs.sh,v 1.9 2007-01-27 18:32:31 sean Exp $

pushd .

cd `dirname $0`/..
./build-ops

if [ ! -d ${CVSDIR} ]
then
  echo "Making CVS directory: ${CVSDIR}"
  /bin/mkdir ${CVSDIR}
fi

echo "Changing to CVS directory: ${CVSDIR}"
cd ${CVSDIR}

echo "Checking out PHP docs..."
/usr/bin/cvs -d :pserver:cvsread@cvs.php.net:/repository co phpdoc-all
echo "Checking out PHP GTK docs..."
/usr/bin/cvs -d :pserver:cvsread@cvs.php.net:/repository co php-gtk-doc
echo "Checking out Smarty docs..."
/usr/bin/cvs -d :pserver:cvsread@cvs.php.net:/repository co smarty/docs
echo "Checking out PEAR docs..."
/usr/bin/cvs -d :pserver:cvsread@cvs.php.net:/repository co peardoc
echo "Checking out php-src..."
/usr/bin/cvs -d :pserver:cvsread@cvs.php.net:/repository co php-src

echo -n "Reverting directory:"
popd


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
# $Id: populatedocs.sh,v 1.7 2005-01-16 10:55:40 nlopess Exp $

pushd .

cd `dirname $0`/..
./build-ops

if [ ! -d ${CVSDIR} ]
then
  /bin/mkdir ${CVSDIR}
fi

cd ${CVSDIR}

echo "Checking out PHP docs..."
/usr/bin/cvs -d :pserver:cvsread@cvs.php.net:/repository co phpdoc-all
echo "Checking out PHP GTK docs..."
/usr/bin/cvs -d :pserver:cvsread@cvs.php.net:/repository co php-gtk-doc
echo "Checking out Smarty docs..."
/usr/bin/cvs -d :pserver:cvsread@cvs.php.net:/repository co smarty/docs
echo "Checking out PEAR docs..."
/usr/bin/cvs -d :pserver:cvsread@cvs.php.net:/repository co peardoc

popd


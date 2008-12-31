#!/bin/bash
# +----------------------------------------------------------------------+
# | PHP Documentation Tools Site Source Code                             |
# +----------------------------------------------------------------------+
# | Copyright (c) 1997-2009 The PHP Group                                |
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
# $Id: populatedocs.sh,v 1.13 2008-12-31 07:02:34 philip Exp $

CVSBIN=/usr/bin/cvs
pushd .

cd `dirname $0`/..
source ./build-ops

if [ ! -d ${CVSDIR} ]
then
  echo "Making CVS directory: ${CVSDIR}"
  /bin/mkdir ${CVSDIR}
fi

echo "Changing to CVS directory: ${CVSDIR}"
cd ${CVSDIR}

echo "Checking out PHP docs..."
if [ -d ${DOCDIR} ]
then
  (cd ${DOCDIR} && ${CVSBIN} up)
else
  ${CVSBIN} -d :pserver:cvsread@cvs.php.net:/repository co -d ${DOCDIR} phpdoc-all
fi

echo "Checking out PHP GTK docs..."
if [ -d ${GTKDIR} ]
then
  (cd ${GTKDIR} && ${CVSBIN} up)
else
  ${CVSBIN} -d :pserver:cvsread@cvs.php.net:/repository co -d ${GTKDIR} php-gtk-doc
fi

echo "Checking out PEAR docs..."
if [ -d ${PEARDIR} ]
then
  (cd ${PEARDIR} && ${CVSBIN} up)
else
  ${CVSBIN} -d :pserver:cvsread@cvs.php.net:/repository co -d ${PEARDIR} peardoc
fi

echo "Checking out php-src..."
if [ -d ${SRCDIR} ]
then
  (cd ${SRCDIR} && ${CVSBIN} up)
else
  BDIR=basename ${SRCDIR}
  ${CVSBIN} -d :pserver:cvsread@cvs.php.net:/repository co -d ${BDIR} php-src
fi

echo -n "Reverting directory:"
popd

# vim: et ts=2 sw=2


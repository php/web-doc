#!/bin/bash
# +----------------------------------------------------------------------+
# | PHP Documentation Tools Site Source Code                             |
# +----------------------------------------------------------------------+
# | Copyright (c) 1997-2014 The PHP Group                                |
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

SVNBIN="/usr/bin/env svn"
pushd .

cd `dirname $0`/..
source ./build-ops

if [ ! -d ${SVNDIR} ]
then
  echo "Making SVN directory: ${SVNDIR}"
  /bin/mkdir ${SVNDIR}
fi

echo "Changing to SVN directory: ${SVNDIR}"
cd ${SVNDIR}

echo "Checking out PHP docs..."
if [ -d ${DOCDIR} ]
then
  (cd ${DOCDIR} && ${SVNBIN} up)
else
  ${SVNBIN} co http://svn.php.net/repository/phpdoc/modules/doc-all ${DOCDIR}
fi

echo -n "Reverting directory:"
popd
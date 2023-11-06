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
# | Authors:          Nilgün Belma Bugüner <nilgun@php.net>              |
# |                   Jacques Marneweck <jacques@php.net>                |
# +----------------------------------------------------------------------+
#
LANGS="de en es fr it ja pl pt_br ro ru tr uk zh"

GITBIN="/usr/bin/env git"
pushd .

cd `dirname $0`/..
source ./build-ops

if [ ! -d ${GITDIR} ]
then
  echo "Making GIT directory: ${GITDIR}"
  /bin/mkdir ${GITDIR}
fi

echo "Changing to GIT directory: ${GITDIR}"
cd ${GITDIR}

echo "Checking out PHP docs..."
if [ -d en ]
then
  for L in $LANGS
  do
    (cd ${L} && ${GITBIN} pull)
  done
else
  for L in $LANGS
  do
    ${GITBIN} clone https://github.com/php/doc-${L}.git ${L}
  done
fi

echo -n "Reverting directory:"
popd

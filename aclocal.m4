dnl aclocal.m4 generated automatically by aclocal 1.4-p4

dnl Copyright (C) 1994, 1995-8, 1999 Free Software Foundation, Inc.
dnl This file is free software; the Free Software Foundation
dnl gives unlimited permission to copy and/or distribute it,
dnl with or without modifications, as long as this notice is preserved.

dnl This program is distributed in the hope that it will be useful,
dnl but WITHOUT ANY WARRANTY, to the extent permitted by law; without
dnl even the implied warranty of MERCHANTABILITY or FITNESS FOR A
dnl PARTICULAR PURPOSE.

dnl ripped from php-src:
AC_DEFUN([DOCWEB_CONFIG_NICE],[
  test -f $1 && mv $1 $1.old
  rm -f $1.old
  cat >$1<<EOF
#! /bin/sh
#
# Created by configure

EOF

  for var in CFLAGS CXXFLAGS CPPFLAGS LDFLAGS LIBS CC CXX; do
    eval val=\$$var
    if test -n "$val"; then
      echo "$var='$val' \\" >> $1
    fi
  done

  for arg in [$]0 "[$]@"; do
    echo "'[$]arg' \\" >> $1
  done
  echo '"[$]@"' >> $1
  chmod +x $1
])



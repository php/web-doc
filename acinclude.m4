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


#!/bin/sh
. `dirname $0`/../build-ops

cd ${CVSDIR}

TRANSPATH=${DOCWEB}/www/trantools;
LANGS=`$PHP -r 'include "phpweb/include/languages.inc"; echo implode( " ", array_keys( $LANGUAGES ) );'`;

cd phpdoc-all

for LANG in $LANGS; do

  $PHP -n -d error_reporting=0 scripts/check-trans.php ${LANG}             > $TRANSPATH/transcheck_${LANG}.html
  $PHP -n -d error_reporting=0 scripts/revcheck.php ${LANG}                > $TRANSPATH/revcheck_${LANG}.html
  $PHP -n -d error_reporting=0 scripts/check-trans-maint.php -n -l ${LANG} -m 1 > $TRANSPATH/maintainer_${LANG}.html
  $PHP -n -d error_reporting=0 scripts/check-trans-params.php ${LANG} 1 1  > $TRANSPATH/params_${LANG}.html
  $PHP -n -d error_reporting=0 scripts/reviewedcheck.php ${LANG}           > $TRANSPATH/reviewedcheck_${LANG}.html

done
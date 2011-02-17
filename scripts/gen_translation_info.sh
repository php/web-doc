#!/bin/sh
. `dirname $0`/../build-ops

cd ${SVNDIR}

TRANSPATH=${DOCWEB}/www/trantools;
LANGS=`$PHP -r 'include "phpweb/include/languages.inc"; echo implode( " ", array_keys( $LANGUAGES ) );'`;

cd phpdoc-all

for LANG in $LANGS; do
  $PHP doc-base/scripts/check-trans.php ${LANG}             > $TRANSPATH/transcheck_${LANG}.html
  $PHP -derror_reporting=0 doc-base/scripts/revcheck.php ${LANG}                > $TRANSPATH/revcheck_${LANG}.html
  $PHP doc-base/scripts/check-trans-maint.php -n -l ${LANG} -m 1 > $TRANSPATH/maintainer_${LANG}.html
  $PHP -derror_reporting=0 doc-base/scripts/check-trans-params.php ${LANG} 1 1  > $TRANSPATH/params_${LANG}.html
  $PHP doc-base/scripts/reviewedcheck.php ${LANG}           > $TRANSPATH/reviewedcheck_${LANG}.html

done


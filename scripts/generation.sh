#!/bin/sh

. `dirname $0`/../build-ops

echo "Generating revcheck databases"

# cd back again 
cd ${SCRIPTSDIR}

# PHP
echo "Generating PHP database"
${PHP} -q ./rev.php php
echo "... done."
echo "Generating PHP pictures"
${PHP} -q ./gen_picture_info.php php
echo "... done"


# PEAR
echo "Generating PEAR database"
${PHP} -q ./rev.php pear
echo "... done."
echo "Generating PEAR pictures"
${PHP} -q ./gen_picture_info.php pear
echo "... done"

echo "Generating global graphs"
${PHP} -q ./gen_picture_info_all_lang.php
echo "... done"

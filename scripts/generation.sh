#!/bin/sh

echo "Generating revcheck databases"

# cd back again 
cd ../scripts

# PHP
echo "Generating PHP database"
php -q ./rev.php php
echo "... done."
echo "Generating PHP pictures"
php -q ./gen_picture_info.php php
echo "... done"


# PEAR
echo "Generating PEAR database"
php -q ./rev.php pear
echo "... done."
echo "Generating PEAR pictures"
php -q ./gen_picture_info.php pear
echo "... done"


# SMARTY
echo "Generating SMARTY database"
php -q ./rev.php smarty
echo "... done."
echo "Generating SMARTY pictures"
php -q ./gen_picture_info.php smarty
echo "... done"



echo "Generating global graphs"
php -q ./gen_picture_info_all_lang.php
echo "... done"

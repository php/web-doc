#!/bin/sh

echo "Generating revcheck databases"

# cd back again 
cd ../scripts

# PHP
echo "Generating PHP database"
/usr/bin/php4 -q ./rev.php php pt_BR zh hk tw cs nl fi fr de el he hu it kr pl ro ru sk sl es sv tr ja
echo "... done."
echo "Generating PHP pictures"
/usr/bin/php4 -q ./gen_picture_info.php php pt_BR zh hk tw cs nl fi fr de el he hu it kr pl ro ru sk sl es sv tr ja
echo "... done"


# PEAR
echo "Generating PEAR database"
/usr/bin/php4 -q ./rev.php pear nl fr hu ru ja
echo "... done."
echo "Generating PEAR pictures"
/usr/bin/php4 -q ./gen_picture_info.php pear pt_BR zh hk tw cs nl fi fr de el he hu it kr pl ro ru sk sl es sv tr ja
echo "... done"


# SMARTY
echo "Generating SMARTY database"
/usr/bin/php4 -q ./rev.php smarty pt_BR fr de ru
echo "... done."
echo "Generating SMARTY pictures"
/usr/bin/php4 -q ./gen_picture_info.php smarty pt_BR zh hk tw cs nl fi fr de el he hu it kr pl ro ru sk sl es sv tr ja
echo "... done"



echo "Generating global graphs"
/usr/bin/php4 -q ./gen_picture_info_all_lang.php
echo "... done"

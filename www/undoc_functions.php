<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4:
+----------------------------------------------------------------------+
| PHP Documentation Site Source Code                                   |
+----------------------------------------------------------------------+
| Copyright (c) 1997-2005 The PHP Group                                |
+----------------------------------------------------------------------+
| This source file is subject to version 3.0 of the PHP license,       |
| that is bundled with this package in the file LICENSE, and is        |
| available at through the world-wide-web at                           |
| http://www.php.net/license/3_0.txt.                                  |
| If you did not receive a copy of the PHP license and are unable to   |
| obtain it through the world-wide-web, please send a note to          |
| license@php.net so we can mail you a copy immediately.               |
+----------------------------------------------------------------------+
| Authors: Matthew Li <mazzanet@php.net>                               |
+----------------------------------------------------------------------+
*/

require_once '../include/init.inc.php';
define('DOCWEB_DAO_DB_FILE', SQLITE_DIR . 'docweb.sqlite');

$entType = SITE;

if (!$sqlite = @sqlite_open(DOCWEB_DAO_DB_FILE, 0666)) {
    echo site_header('docweb.common.header.undoc-functions'); 
    echo '<p>Undocumented function list not found!</p>'; // templating is your friend
    echo site_footer();
    exit();
}

$undocData = array();
$undocCount = 0;
$sql = "
    SELECT
        extension, function
    FROM
        undocumented_functions
";
$undocQ = sqlite_query($sqlite, $sql);
while ($row = sqlite_fetch_array($undocQ)) {
    $undocData[$row['extension']][] = $row['function'];
    $undocCount++;
}

echo site_header('docweb.common.header.undoc-functions');
echo DocWeb_Template::get(
        'undocfuncs.tpl.php',
        array(
            'undocData'    => $undocData,
            'undocCount'   => $undocCount,
        )
    );
echo site_footer();

?>

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
| Authors: Nuno Lopes <nlopess@php.net>                                |
|          Mehdi Achour <didou@php.net>                                |
|          Sean Coates <sean@php.net>                                  |
+----------------------------------------------------------------------+
$Id$
*/

require_once '../include/init.inc.php';

$entType = SITE;
require_once '../include/lib_url_entities.inc.php';

if (!$sqlite = url_ent_sqlite_open()) {
    echo site_header('docweb.common.header.checkent'); 
    echo '<p>checkent not found!</p>'; // @@@ template this
    echo site_footer();
    exit();
}

$sql = "
    SELECT
        start_time, end_time, schemes
    FROM
        meta_info
";
list($startTime, $endTime, $schemes) = sqlite_fetch_array(sqlite_query($sqlite, $sql));

$entData = array();
$sql = "
    SELECT
        url_num, entity, url, check_result, return_val
    FROM
        checked_urls
    WHERE
        check_result > 0
    ORDER BY
        check_result, entity
";
$urlsQ = sqlite_query($sqlite, $sql);
while ($row = sqlite_fetch_array($urlsQ)) {
    $entData[$row['check_result']][] = $row;
}


echo site_header('docweb.common.header.checkent');
echo DocWeb_Template::get(
        'checkent.tpl.php',
        array(
            'startTime'  => $startTime,
            'isComplete' => $endTime ? TRUE : FALSE,
            'schemes'    => $schemes,
            'entData'    => $entData,
            'resultLkp'  => $urlResultLookup,
            'extraCol'   => $urlResultExtraCol,
            'wideMode'   => isset($_REQUEST['wideMode']) && $_REQUEST['wideMode'],
        )
    );
echo site_footer();

?>

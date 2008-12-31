<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4:
+----------------------------------------------------------------------+
| PHP Documentation Site Source Code                                   |
+----------------------------------------------------------------------+
| Copyright (c) 1997-2009 The PHP Group                                |
+----------------------------------------------------------------------+
| This source file is subject to version 3.0 of the PHP license,       |
| that is bundled with this package in the file LICENSE, and is        |
| available at through the world-wide-web at                           |
| http://www.php.net/license/3_0.txt.                                  |
| If you did not receive a copy of the PHP license and are unable to   |
| obtain it through the world-wide-web, please send a note to          |
| license@php.net so we can mail you a copy immediately.               |
+----------------------------------------------------------------------+
| Authors: Sean Coates <sean@php.net>                                  |
+----------------------------------------------------------------------+
$Id$
*/

require_once '../include/init.inc.php';

$entType = SITE;
require_once '../include/lib_url_entities.inc.php';

if (!$sqlite = ent_sqlite_open()) {
    echo site_header('docweb.common.header.entities'); 
    echo '<p>entities not found!</p>'; // @@@ template this
    echo site_footer();
    exit();
}

$entData = array();
$sql = "
    SELECT
        entid, value
    FROM
        ents
    WHERE
        is_file = 0
    ORDER BY
        entid
";
$entsQ = sqlite_query($sqlite, $sql);
while ($row = sqlite_fetch_array($entsQ)) {
    $entData[$row['entid']] = strip_tags($row['value']);
    if (substr($row['entid'], 0, 4) == 'url.') {
        $entData[$row['entid']] = ent_link($entData[$row['entid']]);
    } else {
        $entData[$row['entid']] = ent_anchors(ent_link($entData[$row['entid']]));
    }
}

echo site_header('docweb.common.header.entities');
echo DocWeb_Template::get(
        'entities.tpl.php',
        array(
            'entData'    => $entData,
        )
    );
echo site_footer();

?>

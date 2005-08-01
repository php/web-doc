<?php
/*
+----------------------------------------------------------------------+
| PHP Documentation Site Source Code                                   |
+----------------------------------------------------------------------+
| Copyright (c) 2005 The PHP Group                                     |
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
| Credits: Vincent Gevers <vincent@php.net>                            |
+----------------------------------------------------------------------+
$Id$
*/


require_once('../include/init.inc.php');

@include('note_stats-data.php');

if (!is_array($notesData) || !$notesData) {
    echo DocWeb_Template::get(
        'shared/error_msg.tpl.php',
        array(
            'header' => 'docweb.common.header.stats-not-available',
            'body'   => array(
                '&docweb.common.error.stats-not-available;',
                false
            )
        )
    );
    return;
} elseif ($notesData['last_article'] < 50000) {
    echo DocWeb_Template::get(
        'shared/error_msg.tpl.php',
        array(
            'header' => 'docweb.common.header.stats-not-available-yet',
            'body'   => array(
                '&docweb.common.error.stats-not-available-yet;',
                false
            )
        )
    );
    return;    
}


echo site_header("docweb.common.header.notes-stats");
echo DocWeb_Template::get(
         'notes_stats.tpl.php',
	 $notesData
     );
echo site_footer();

?>							    

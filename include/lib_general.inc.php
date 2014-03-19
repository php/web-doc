<?php
/*
+----------------------------------------------------------------------+
| PHP Documentation Tools Site Source Code                             |
+----------------------------------------------------------------------+
| Copyright (c) 1997-2014 The PHP Group                                |
+----------------------------------------------------------------------+
| This source file is subject to version 3.0 of the PHP license,       |
| that is bundled with this package in the file LICENSE, and is        |
| available through the world-wide-web at the following url:           |
| http://www.php.net/license/3_0.txt.                                  |
| If you did not receive a copy of the PHP license and are unable to   |
| obtain it through the world-wide-web, please send a note to          |
| license@php.net so we can mail you a copy immediately.               |
+----------------------------------------------------------------------+
| Authors:          Yannick Torres <yannick@php.net>                   |
|                   Mehdi Achour <didou@php.net>                       |
|                   Gabor Hojtsy <goba@php.net>                        |
|                   Sean Coates <sean@php.net>                         |
|                   Maciej Sobaczewski <sobak@php.net>                 |
+----------------------------------------------------------------------+
*/

function get_svn_dir($project)
{
    // @@@ make this return something until the function is found
    return $GLOBALS['PROJECTS'][$project][1] . '/';
}

function site_header($full_screen = false)
{
    $TITLE = 'Documentation Tools';
	$SUBDOMAIN = 'doc';
	$LINKS = array(
        array('href' => '/revcheck.php', 'text' => 'Documentation Tools'),
        array('href' => '/dochowto/', 'text' => 'Documentation Howto'),
        array('href' => '/phd.php', 'text' => 'PhD Homepage'),
    );

    require __DIR__ . '/../shared/templates/header.inc';

    if ($full_screen) {
        echo '<section class="fullscreen">';
    }
    else {
        echo '<section class="mainscreen">';
    }
}

function site_footer($SECONDSCREEN = false)
{
    echo '</section>';
    require __DIR__ . '/../shared/templates/footer.inc';
}
<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4:
+----------------------------------------------------------------------+
| PHP Version 4                                                        |
+----------------------------------------------------------------------+
| Copyright (c) 1997-2004 The PHP Group                                |
+----------------------------------------------------------------------+
| This source file is subject to version 3.0 of the PHP license,       |
| that is bundled with this package in the file LICENSE, and is        |
| available through the world-wide-web at the following url:           |
| http://www.php.net/license/3_0.txt.                                  |
| If you did not receive a copy of the PHP license and are unable to   |
| obtain it through the world-wide-web, please send a note to          |
| license@php.net so we can mail you a copy immediately.               |
+----------------------------------------------------------------------+
| Authors:                                                             |
|                   Yannick Torres <yannick@php.net>                   |
|                   Mehdi Achour <didou@php.net>                       |
+----------------------------------------------------------------------+
$Id$
*/

if (SITE == 'www' ) {
    define('RFC_SITE', 'php');
} else {
    define('RFC_SITE', SITE);
}

define('CVS_RFC_DIR', CVS_DIR . $TYPES[RFC_SITE] . '/RFC');

function scan_RFC_dir()
{
    if (!is_dir(CVS_RFC_DIR)) {
        return FALSE;
    }
  
    $dh = opendir(CVS_RFC_DIR);

    $rfc_file = array();

    while (($file = readdir($dh)) !== false) {
        if (
            $file != '.' &&
            $file != '..' &&
            $file != 'CVS' &&
            $file != '.cvsignore' &&
            substr($file, -4) != '.xml' &&
            substr($file, -3) != '.in' &&
            substr($file, -5) != '.html'
        ) {
            $rfc_file['file_name'][] = $file;
        }
    }
    closedir($dh);

    $rfc_file['nb_file'] = sizeof($rfc_file['file_name']);

    return $rfc_file;
}

function clean_file_name($name)
{
    if (substr($name, -4) == '.txt') {
        $name = substr($name, 0, (strlen($name)-4));
    }  
  
    $name = str_replace('_', ' ', $name);

    return $name;
}

function out_put_comment($rows)
{
    $buff = "
      
      <!-- #id_database : " . $rows['id'] . "-->
      <h2>" . htmlentities(format_email($rows['user'])) . "</h2>
      <h3> [ " . date("r", $rows['date']) . " ]</h3>
      <p class=\"comment\">
      " . nl2br(htmlentities($rows['note'])) . "
      </p>
    ";
  
    return $buff;
}

?>


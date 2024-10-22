<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4:
+----------------------------------------------------------------------+
| PHP Documentation Tools Site Source Code                             |
+----------------------------------------------------------------------+
| Copyright (c) 1997-2011 The PHP Group                                |
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
+----------------------------------------------------------------------+
*/

// get paths
$build_ops = dirname(realpath(__FILE__)) . '/../build-ops.php';
if (file_exists($build_ops)) {
    require_once($build_ops);
} else {
    $GIT_DIR = getenv('GIT_DIR');
    if ($GIT_DIR == '') {
        die("Unable to find Git repositories, set `GIT_DIR` environment variable!");
    }
    $GIT_DIR .= (substr($GIT_DIR, -1) == '/' ? '' : '/');
    define('GIT_DIR', $GIT_DIR);

    $SQLITE_DIR = getenv('SQLITE_DIR');
    if ($SQLITE == '') {
        die("Don't know where to place SQLite database, set `SQLITE_DIR` enviromment variable!");
    }
    $SQLITE_DIR .= (substr($SQLITE_DIR, -1) == '/' ? '' : '/');
    define('SQLITE_DIR', $SQLITE_DIR);
}

// Cache is considered stale after (seconds):
define('CACHE_BUGS_COUNT', 300); // 300 = 5mins

// project & language config
require_once('lib_proj_lang.inc.php');

// general support library
require_once('lib_general.inc.php');

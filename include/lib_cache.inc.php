<?php
/*
+----------------------------------------------------------------------+
| PHP Documentation Site Source Code                                   |
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
| Authors:          Mehdi Achour <didou@php.net>                       |
+----------------------------------------------------------------------+
$Id$
*/

define('CACHE_DIR', PATH_ROOT . '/cache/');
define('CACHE_EXT', '.cache');

function is_cached($file) 
{
    return file_exists(CACHE_DIR . $file . CACHE_EXT);
}

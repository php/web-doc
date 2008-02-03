#!/usr/local/bin/php
<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4:
+----------------------------------------------------------------------+
| PHP Documentation Site Source Code                                   |
+----------------------------------------------------------------------+
| Copyright (c) 1997-2005 The PHP Group                                |
+----------------------------------------------------------------------+
| This source file is subject to version 3.01 of the PHP license,      |
| that is bundled with this package in the file LICENSE, and is        |
| available at through the world-wide-web at                           |
| http://www.php.net/license/3_01.txt.                                 |
| If you did not receive a copy of the PHP license and are unable to   |
| obtain it through the world-wide-web, please send a note to          |
| license@php.net so we can mail you a copy immediately.               |
+----------------------------------------------------------------------+
| Authors: Etienne Kneuss <colder@php.net>                             |
+----------------------------------------------------------------------+
$Id$
*/

$PWD = dirname(__FILE__) . "/";
$path = $PWD.'../sqlite/tests.sqlite';

$idx = sqlite_open($path, 0666);

if (!$idx) {
    die('Could not open '.$path);
}

sqlite_query($idx, file_get_contents($PWD ."../sql/tests.sql"));
sqlite_close($idx);

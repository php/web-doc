#!/usr/local/bin/php
<?php
/**
 * +----------------------------------------------------------------------+
 * | PHP Documentation Site Source Code                                   |
 * +----------------------------------------------------------------------+
 * | Copyright (c) 1997-2004 The PHP Group                                |
 * +----------------------------------------------------------------------+
 * | This source file is subject to version 3.0 of the PHP license,       |
 * | that is bundled with this package in the file LICENSE, and is        |
 * | available at through the world-wide-web at                           |
 * | http://www.php.net/license/3_0.txt.                                  |
 * | If you did not receive a copy of the PHP license and are unable to   |
 * | obtain it through the world-wide-web, please send a note to          |
 * | license@php.net so we can mail you a copy immediately.               |
 * +----------------------------------------------------------------------+
 * | Authors: Jacques Marneweck <jacques@php.net>                         |
 * +----------------------------------------------------------------------+
 *
 * $Id$
 */

$PWD = dirname(__FILE__) . "/";
$idx = sqlite_open("$PWD../sqlite/rfc.sqlite", 0666);
if (!$idx) {
    die("could not open $PWD../sqlite/rfc.sqlite");
}
sqlite_query($idx, file_get_contents("$PWD../sql/rfc.sql"));
sqlite_close($idx);

/* vim: set noet ts=4 sw=4 ft=php: : */

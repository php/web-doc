<?php
/**
 * +----------------------------------------------------------------------+
 * | PHP Documentation Site Source Code                                   |
 * +----------------------------------------------------------------------+
 * | Copyright (c) 1997-2005 The PHP Group                                |
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

require_once 'cvs-auth.inc';

$user = $pw = false;

/**
 * Credential checking of the $_COOKIE['MAGIC_COOKIE']
 */
if (isset($_COOKIE['MAGIC_COOKIE'])) {
	list($user, $pw) = explode(":", base64_decode($_COOKIE['MAGIC_COOKIE']));

	if (!$user || !$pw || !verify_password($user,stripslashes($pw))) {
		Header ("Location: http://master.php.net/manage/users.php");
		exit;
	}
} else {
	Header ("Location: http://master.php.net/manage/users.php");
	exit;
}

/* vim: set noet ts=4 sw=4 ft=php: : */

<?php
/**
 * +----------------------------------------------------------------------+
 * | PHP Documentation Site Source Code                                   |
 * +----------------------------------------------------------------------+
 * | Copyright (c) 1997-2011 The PHP Group                                |
 * +----------------------------------------------------------------------+
 * | This source file is subject to version 3.0 of the PHP license,       |
 * | that is bundled with this package in the file LICENSE, and is        |
 * | available at through the world-wide-web at                           |
 * | http://www.php.net/license/3_0.txt.                                  |
 * | If you did not receive a copy of the PHP license and are unable to   |
 * | obtain it through the world-wide-web, please send a note to          |
 * | license@php.net so we can mail you a copy immediately.               |
 * +----------------------------------------------------------------------+
 * | Authors: Sara Golemon <pollita@php.net>                              |
 * |          Wez Furlong <wez@php.net>                                   |
 * |          Jacques Marneweck <jacques@php.net>                         |
 * +----------------------------------------------------------------------+
 *
 * $Id$
 */

require_once(dirname(__FILE__) . '/../build-ops.php');

function auth_vs_svn($user, $pass, &$errors = null)
{
    $errors = array();

    $post = http_build_query(
        array(
            'token' => getenv('AUTH_TOKEN'),
            'username' => $user,
            'password' => $pass,
        )    
    );   

    $opts = array(
        'method'    => 'POST',
        'header'    => 'Content-type: application/x-www-form-urlencoded',
        'content'   => $post,
    );   

    $ctx = stream_context_create(array('http' => $opts));

    $s = file_get_contents('https://master.php.net/fetch/cvsauth.php', false, $ctx);

    $a = @unserialize($s);
    if (!is_array($a)) {
        $errors[] = "Failed to get authentication information.\nMaybe master is down?\n";
        return false;
    }    
    if (isset($a['errno'])) {
        $errors[] = "Authentication failed: {$a['errstr']}\n";
        return false;
    }    

    return true;
}


/**
 * Check local credential cache before sending extra queries
 * to master.php.net
 */
function auth_vs_local ($idx, $user, $pass) {

	$res = sqlite_query ($idx, "SELECT * FROM auth WHERE username='" . sqlite_escape_string($user) . "'");

	if (!$res) {
		return false;
	}

	if (!$data = sqlite_fetch_array($res, SQLITE_ASSOC)) {
		return false;
	}

	if ($data['time'] + 60*60*24 < time()) {
		return false;
	}

	if (sha1($pass) == $data['password']) {
		return true;
	}

	return false;
}

function verify_password($user, $pass) {
	global $idx;

	if (!$user || !$pass) {
		return false;
	}

	if (auth_vs_local($idx, $user, $pass)) {
		return true;
	}

	if (auth_vs_svn($user, $pass)) {
		return true;
	}

	return false;
}

/* vim: set noet ts=4 sw=4 ft=php: : */

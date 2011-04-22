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
 * | Authors: Jacques Marneweck <jacques@php.net>                         |
 * |          Nuno Lopes <nlopess@php.net>                                |
 * +----------------------------------------------------------------------+
 *
 * $Id$
 */

require_once 'cvs-auth.inc';

if (!$idx = sqlite_open(SQLITE_DIR . 'users.sqlite')) {
    die ('auth DB not available');
}

$user = null; // Register globals...

//list of docweb admins that have 'special' rights
$admins = array(
    'didou',
    'goba',
    'jacques',
    'nlopess',
    'philip',
    'sean',
    'vincent',
    'mazzanet',
    'colder',
    'bjori',
);

/**
 * Credential checking of the $_COOKIE['MAGIC_COOKIE']
 */
function auth()
{
    $return = urlencode($_SERVER['REQUEST_URI']);

    if (isset($_POST['username']) && isset($_POST['passwd'])) {
        if (!verify_password($_POST['username'], $_POST['passwd'])) {
            header ('Location: https://doc.php.net/login.php?return='.$return);
            exit;
        }
    } else {
        header ('Location: https://doc.php.net/login.php?return='.$return);
        exit;
    }
}


/**
 * Checks if a user has admin rights
 */
function is_admin()
{
    return in_array($GLOBALS['user'], $GLOBALS['admins']);
}


/**
 * read user info from the DB
 */
function user_info($u = false)
{
    global $idx, $user;

    $u = $u ? $u : $user;
    $result = @sqlite_unbuffered_query($idx, "SELECT * FROM users WHERE username='" . sqlite_escape_string($u) . "'");
    if (!$result) {
        return false;
    }

    return sqlite_fetch_array($result, SQLITE_ASSOC);
}


/**
 * returns the user's real name
 */
function user_name($user = false)
{
    $user = $user ? $user : $GLOBALS['user'];

    // first check if the name is in the DB
    if ($info = user_info($user))
        return $info['name'];

    //no, it isn't. fetch it from the master server
    return master_user_name($user);
}


/**
 * Fetch the user's real name from the master server
 */
function master_user_name($nick)
{
    // LOGINFIXME: Do this query through the proper API
    return "unknown";
    $magic_cookie = (!empty($_COOKIE['MAGIC_COOKIE'])) ?
            $_COOKIE['MAGIC_COOKIE'] :
                        '' ; // need a generic key here!!

    if (!$fp = @fsockopen('master.php.net', 80))
        return $nick;

    fputs($fp, "GET /manage/users.php?username=$nick HTTP/1.0\r\n".
           "Host: master.php.net\r\n".
           "Cookie: MAGIC_COOKIE=$magic_cookie\r\n".
           "\r\n");

    $txt = @fread($fp, 50000);
    fclose($fp);

    // if we found a name, cache it in the DB
    if (preg_match('@<th[^>]+>Name:</th>\s+<td><input[^>]+value="([^"]+)"@', $txt, $match)) {
        sqlite_query($GLOBALS['idx'], "INSERT INTO users (username, name) VALUES ('$nick', '$match[1]')"); //the server has no sqlite_exec support yet (still php4)
        return $match[1];
    }

    return $nick;
}

?>

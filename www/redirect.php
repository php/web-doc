<?php
/*
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
| Authors:          Sean Coates <sean@php.net>                         |
+----------------------------------------------------------------------+
*/

require_once(__DIR__ . '/../include/lib_proj_lang.inc.php');

/* mime types for downloading files */
$mime_types = array(
    'gz'     => 'application/x-gunzip',
    'tgz'    => 'application/x-tar-gz',
    'tar.gz' => 'application/x-tar-gz',
    'zip'    => 'application/x-zip-compressed'
);


function part_is_project($part) {
    return isset($GLOBALS['PROJECTS'][$part]);
}

function part_is_language($part) {
    return isset($GLOBALS['LANGUAGES'][$part]);
}

function part_get_filename($part) {
    // strip query string
    $filename = preg_replace('/\?.*$/', '', $part);

    // remove webroot-escape attempts
    $filename = str_replace('..', '', $filename);

    // remove obsolete slashes
    $filename = preg_replace('#/{2,}#', '/', $filename);

    // strip ending slashes
    $filename = rtrim($filename, '/');

    $filename = './'.$filename;

    if (is_dir($filename)) {
        return $filename.'/index.php';
    }

    return $filename;
}

function part_is_valid_uri($part) {
    if (file_exists($uri = part_get_filename($part)))
        return $uri;
    return false;
}

$parts = explode('/', $_SERVER['REQUEST_URI'], 4);
$uri = '';

foreach ($parts as $part) {

    if (part_is_project($part)) {
        $project = $part;

    } elseif (part_is_language($part)) {
        $language = $part;

    } elseif ($part) {
        $uri .= "/$part";
    }
}

// exception for the /user/xx shortcut
$parts = explode('/', $uri);
if ((count($parts) == 3 || count($parts) == 4) &&
    $parts[1] == 'user')
{
    $userid = $parts[2];
    if (isset($parts[3]) && $parts[3] == 'edit') {
        $doEdit = true;
    }
    $uri    = './users.php';

// generic uri validator
} elseif (!($uri = part_is_valid_uri($uri))) {
    unset($uri);
}


// we have a valid URI, answer the request
if (isset($uri)) {
    header($_SERVER['SERVER_PROTOCOL']." 200 Found (magic redirect)");

    // If it's a PHP file include it, otherwise pass it through
    if (substr($uri, -4) == '.php') {
        require($uri);
        return;
    } else {
        // the file can't be a directory nor a php file
        // Validate the mime type
        $mime = false;
        foreach ($mime_types as $ext => $type) {

            if (substr($uri, -strlen($ext)) == $ext) {
                $mime = $type;
                break;
            }

        }
        if ($mime !== false) {
            header("Content-Type: $mime");
            readfile($uri);
            return;
        }
    }
}
// script has not exited yet, an error must have occured, display 404.
// no resource found:
header($_SERVER['SERVER_PROTOCOL']." 404 Not Found");
$_SERVER["REDIRECT_STATUS"] = '404';
$uri = '/';
require('error.php');

?>

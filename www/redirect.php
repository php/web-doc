<?php
/*
+----------------------------------------------------------------------+
| PHP Documentation Tools Site Source Code                             |
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
| Authors:          Sean Coates <sean@php.net>                         |
+----------------------------------------------------------------------+
$Id$
*/

require_once('../include/lib_proj_lang.inc.php');

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
    $filename = str_replace(array('..', '//'), array('', '/'), $filename);
    
    // fake DirectoryIndex
    if (substr($filename, -1) == '/' || $filename == '') {
        $filename .= 'index.php';
    }

    $filename = "./$filename";
    return $filename;
}

function part_is_valid_uri($part) {
    return file_exists(part_get_filename($part));
}

$parts = explode('/', $_SERVER['REQUEST_URI'], 4);
$uri = '';

foreach ($parts as $part) {

    if (part_is_project($part)) {
        $project = $part;

    } elseif (part_is_language($part)) {
        $language = $part;

    } else {
        $uri .= "/$part";
    }
}

if (!$uri || !part_is_valid_uri($uri)) {
    unset($uri);
}


// we have a valid URI, answer the request
if (isset($uri)) {
  header($_SERVER['SERVER_PROTOCOL']." 200 Found (magic redirect)");
  require(part_get_filename($uri));
} else {
  // no resource found:
  header($_SERVER['SERVER_PROTOCOL']." 404 Not Found");
  $_SERVER["REDIRECT_STATUS"] = '404';
  $uri = '/';
  require('error.php');
}

?>

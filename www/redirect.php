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
    return in_array($part, array_keys($GLOBALS['PROJECTS']));
}

function part_is_language($part) {
    return in_array($part, array_keys($GLOBALS['LANGUAGES']));
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

if (
    // /proj/lang/URI
    count($parts) == 4 &&
    part_is_project($parts[1]) && 
    part_is_language($parts[2]) &&
    part_is_valid_uri($parts[3])
) {
    $project  = $parts[1];
    $language = $parts[2];
    $uri      = $parts[3];
} elseif (
    // /lang/proj/URI
    count($parts) == 4 &&
    part_is_language($parts[1]) &&
    part_is_project($parts[2]) &&
    part_is_valid_uri($parts[3])
) {
    $language = $parts[1];
    $project  = $parts[2];
    $uri      = $parts[3];
} elseif (
    // /lang (no trailing slash)
    count($parts) == 2
    &&
    (part_is_language($parts[1]) || part_is_project($parts[1])) 
) {
    // redirect (add trailing slash)
    // NOTE: we lose get/post data, here
    header("Location: /{$parts[1]}/");
    exit();
} elseif (
    count($parts) == 3
    &&
    (
        (
            // /lang/proj (no trailing slash)
            part_is_language($parts[1]) &&
            part_is_project($parts[2])
        )
        ||
        (
            // /proj/lang (no trailing slash)
            part_is_project($parts[1]) &&
            part_is_language($parts[2])
        )
    )
) {
    // redirect (add trailing slash)
    // NOTE: we lose get/post data, here
    header("Location: /{$parts[1]}/{$parts[2]}/");
    exit();
} elseif (
    // /proj/URI
    part_is_project($parts[1]) &&
    part_is_valid_uri(implode('/', array_slice($parts, 2)))
) {
    $project = $parts[1];
    $uri     = implode('/', array_slice($parts, 2));
} elseif (
    // /lang/URI
    part_is_language($parts[1]) &&
    part_is_valid_uri(implode('/', array_slice($parts, 2)))
) {
    $language = $parts[1];
    $uri      = implode('/', array_slice($parts, 2));
}

// we have a valid URI, answer the request
if (isset($uri)) {
  header($_SERVER['SERVER_PROTOCOL']." 200 Found (magic redirect)");
  require(part_get_filename($uri));
} else {
  // no resource found:
  header($_SERVER['SERVER_PROTOCOL']." 404 Not Found");
  $_SERVER["REDIRECT_STATUS"] = '404';
  require('error.php');
}

?>

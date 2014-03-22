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
require_once(dirname(realpath(__FILE__)) . '/../build-ops.php');

// Cache is considered stale after (seconds):
define('CACHE_BUGS_COUNT', 300); // 300 = 5mins

// project & language config
require_once('lib_proj_lang.inc.php');

// get defaults
list($defaultProject)    = array_keys($PROJECTS);
list($defaultLanguage)   = array_keys($LANGUAGES);
$defaultFallbackProject  = 'www';
$defaultFallbackLanguage = 'en';

// Only allow $_SERVER under apache to make cli scripts work
if (!isset($inCli) OR $inCli != true) {
    $inCli = false;

    // set up constants (use defaults if necessary)
    define('SITE',  isset($project)  ? $project  : $defaultProject);
    define('LANGC', isset($language) ? $language : $defaultLanguage);
    define('URI',   isset($uri)      ? preg_replace('@^[/\.]+@', '/' ,$uri) : htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES));
    define('LANGD', $LANGUAGES[LANGC]);

    if (isset($project)) {
        $baseURL[] = $project;
    }

    if (isset($language) && $language != 'all' && $language != 'en') {
        $baseURL[] = $language;
    }

    $baseURL = isset($baseURL) ? '/' . implode('/', $baseURL) : '';

    // actually define the constant
    define('BASE_URL', $baseURL);

}

// general support library
require_once('lib_general.inc.php');
<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4:
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
| Authors:          Yannick Torres <yannick@php.net>                   |
|                   Mehdi Achour <didou@php.net>                       |
|                   Gabor Hojtsy <goba@php.net>                        |
|                   Sean Coates <sean@php.net>                         |
+----------------------------------------------------------------------+
$Id$
*/

error_reporting(E_ALL);

// get paths
require_once('../build-ops.php');

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
    define('URI',   isset($uri)      ? $uri      : $_SERVER['REQUEST_URI']);
    define('LANGD', $LANGUAGES[LANGC]);

    // set up BASE_URL
    if (substr($_SERVER['REQUEST_URI'], -1, 1) == '/') {
        // is a directory, use verbatim
        $baseURL = $_SERVER['REQUEST_URI'];
    } else {
        // not a dir, use the dirname
        $baseURL = dirname($_SERVER['REQUEST_URI']);
    }

    // this very dirty fix makes /rfc work
    $baseURL = str_replace('/rfc', '', $baseURL);

    // actually define the constant (trim off any trailing slashes):
    define('BASE_URL', rtrim($baseURL, '/'));

}

// general support library
require_once('lib_general.inc.php');

if ($inCli != true) {
    // language & template constants
    define('DOCWEB_ENTITIY_PREFIX', 'docweb');
    define('DOCWEB_PARAM_ENTITIY_PREFIX', 'param');

    // language engine
    require_once('docweb_language.class.php');
    $Language =& new DocWeb_Language(LANGC);

    // template engine
    require_once('docweb_template.class.php');
}

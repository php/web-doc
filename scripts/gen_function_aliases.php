<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4:
+----------------------------------------------------------------------+
| PHP Documentation Site Source Code                                   |
+----------------------------------------------------------------------+
| Copyright (c) 2005 The PHP Group                                     |
| Copyright (c) 1997-2004 Dave Barr                                    |
+----------------------------------------------------------------------+
| This source file is subject to version 3.0 of the PHP license,       |
| that is bundled with this package in the file LICENSE, and is        |
| available through the world-wide-web at the following url:           |
| http://www.php.net/license/3_0.txt.                                  |
| If you did not receive a copy of the PHP license and are unable to   |
| obtain it through the world-wide-web, please send a note to          |
| license@php.net so we can mail you a copy immediately.               |
+----------------------------------------------------------------------+
| Author:        Dave Barr <dave@php.net>                              |
| DocWeb Port:   Sean Coates <sean@php.net>                            |
+----------------------------------------------------------------------+
$Id$
*/

set_time_limit(0);
$scriptBegin = time();
$inCli = true;
require_once '../include/init.inc.php';
require_once '../include/lib_meta_info.inc.php';
require_once '../include/docweb_dao_metainfo.class.php';

echo "Generating Function Aliases data...\n";

$DAO = new DocWeb_DAO_MetaInfo(TRUE);

$DAO->metaLogStartTime('aliases');
$DAO->purgeAliases();

// Special places to look for aliases */
$special = array(
    'info'   => 'ZendEngine2/zend_builtin_functions.c',
    'apache' => 'sapi/apache/php_apache.c',
);

$phpsrc = SRC_DIR;

// search the extensions
$exts = array();
$dir = opendir("$phpsrc/ext");
while ($entry = readdir($dir)) {
    if (in_array($entry, array('.','..'))) {
        continue;
    }

    if (is_dir("$phpsrc/ext/$entry")) {
        $exts[] = $entry;
    }
}
closedir($dir);

$aliases = array();
$total = 0;

foreach ($exts as $ext) {
    $extdir = "$phpsrc/ext/$ext";
    $dir = opendir($extdir);
    while ($entry = readdir($dir)) {
        if (in_array($entry, array('.','..'))) {
            continue;
        }

        if (is_file("$extdir/$entry") &&
            substr("$extdir/$entry", -2) == ".c") {
            
            // file is a C file, check it for function aliases
            find_alias_file("$extdir/$entry", $ext);
        }
    }
    closedir($dir);
}

foreach ($special as $ext => $filename) {
    if (is_file("$phpsrc/$filename")) {
        find_alias_file("$phpsrc/$filename", $ext);
    }
}

ksort($aliases, SORT_STRING);

foreach ($aliases AS $ext => $aliasData) {
    foreach ($aliasData AS $alias => $func) {
        echo "[$ext] $alias -> $func\n";
        $DAO->storeFunctionAlias($ext, $alias, $func);
    }
}

$DAO->metaLogEndTime('aliases');

echo "** Done.\n";
?>


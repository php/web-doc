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

echo "Generating Missing Examples data...\n";

$DAO = new DocWeb_DAO_MetaInfo(TRUE);

$reference = CVS_DIR . '/phpdoc-all/en/reference';

$excludefuncs = array(
    'overload' => 'The example is in the introductory section',
    'mysql-db-query' => 'Deprecated function',
    'mysql-change-user' => 'PHP 3',
);

$exts = array();
$dir = opendir($reference);

while ($entry = readdir($dir)) {
    if ($entry == "." || $entry == "..") {
        continue;
    }

    // entry is a directory, and has a valid functions sub-directory
    if (is_dir("$reference/$entry") && is_dir("$reference/$entry/functions")) {
        $exts[] = $entry;
    }
}

closedir($dir);

sort($exts, SORT_STRING);

$extfuncs = array();
$functotal = 0;

foreach ($exts as $ext) {
    $extfuncs[$ext] = array();

    $funcdir = "$reference/$ext/functions";
    $dir = opendir($funcdir);

    while ($entry = readdir($dir)) {
        $function = substr($entry, 0, -4);

        // found a file in the functions directory, and it's an .xml file
        if (
            is_file("$funcdir/$entry") &&
            substr($entry, -4) == ".xml" &&
            strstr(substr($entry, 0, -4), ".") === false &&
            !isset($excludefuncs[$function])
        ) {
            $file = file_get_contents("$funcdir/$entry");
            $ufunction = str_replace('-', '_', $function);
            
            $alias = $DAO->isAlias($ufunction);

            if (
                strstr($file, "<example") === false &&
                strstr($file, "<informalexample") === false
            ) {
                // this function doesn't have an example

                // check if this function is an alias
                if (!$alias) {
                    $extfuncs[$ext][] = $ufunction;
                }
            }

            if (!$alias) {
                $functotal++;
            }
        }
    }

    sort($extfuncs[$ext], SORT_STRING);
    closedir($dir);
}

$notmissing = array();
$extcount = 0;
$total = 0;


foreach ($extfuncs as $name => $ext) {
    $exttotal = count($ext);

    if ($exttotal == 0) {
        $notmissing[] = $name;
    }
    else {
        $extcount++;
        $total += $exttotal;
    }

}

$DAO->purgeExamples();
foreach (array_diff($extfuncs, $notmissing) AS $ext => $extData) {
    foreach ($extData AS $func) {
        echo "[$ext] $func\n";
        $DAO->storeMissingExample($ext, $func);
    }
}

echo "** Done.\n";

?>
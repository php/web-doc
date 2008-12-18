<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4:
+----------------------------------------------------------------------+
| PHP Documentation Site Source Code                                   |
+----------------------------------------------------------------------+
| Copyright (c) 1997-2008 The PHP Group                                |
+----------------------------------------------------------------------+
| This source file is subject to version 3.0 of the PHP license,       |
| that is bundled with this package in the file LICENSE, and is        |
| available at through the world-wide-web at                           |
| http://www.php.net/license/3_0.txt.                                  |
| If you did not receive a copy of the PHP license and are unable to   |
| obtain it through the world-wide-web, please send a note to          |
| license@php.net so we can mail you a copy immediately.               |
+----------------------------------------------------------------------+
| Authors: Philip Olson <philip@php.net>                               |
+----------------------------------------------------------------------+
$Id$

Usage: This exists to show the outputted files from the various
       translation scripts found in phpdoc.

       See Also: docweb/scripts/gen_translation_info.sh
                 phpdoc/scripts/check-trans-* and revcheck.php

TODO:  Integrate with docweb, and make prettier (valid) output
*/

$files = glob("*.html");

if (count($files) < 1) {
	echo "Where did all the generated translation files go?";
	exit;
}

foreach ($files as $filename) {

	preg_match("@(.+)_(.+)\.html@", $filename, $matches);
	
	if (empty($matches[1]) || empty($matches[2])) {
		continue;
	}
	
	$name  = $matches[1];
	$cc    = $matches[2];

	$keepers[$cc][] = $filename;
}

foreach ($keepers as $cc => $kept) {
	
	if (!is_array($kept) || empty($kept)) {
		continue;
	}

	echo "<h3>$cc</h3>\n";
	echo "<ul>\n";

	foreach ($kept as $filename) {
		echo "<li><a href='$filename'>$filename</a></li>\n";
	}

	echo "</ul>\n";
}

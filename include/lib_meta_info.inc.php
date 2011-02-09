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
| Authors:     Dave Barr <dave@php.net>                                |
| DocWeb port: Sean Coates <sean@php.net>                              |
+----------------------------------------------------------------------+
$Id$
*/

/**
 * Finds aliases in the passed file
 *
 * @param string $filename filename to parse
 * @param string $ext      extension to which this file belongs
 *
 */
function find_alias_file($filename, $ext)
{
    global $aliases, $total;

    $file = file_get_contents($filename);

    $matchRegex = "/(?:PHP|ZEND)_FALIAS\(\s*(\w+)\s*,\s*(\w+)\s*,/";
    if (preg_match_all($matchRegex, $file, $matches)) {
        foreach ($matches[1] as $k => $alias) {
            $func  = $matches[2][$k];

            if (!(isset($aliases[$ext]) && is_array($aliases[$ext]))) {
                 $aliases[$ext] = array();
            }

            $aliases[$ext][$alias] = $func;
            $total++;
        }
    }
}
 

?>

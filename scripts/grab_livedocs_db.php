<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4:
+----------------------------------------------------------------------+
| PHP Documentation Site Source Code                                   |
+----------------------------------------------------------------------+
| Copyright (c) 2005 The PHP Group                                     |
+----------------------------------------------------------------------+
| This source file is subject to version 3.0 of the PHP license,       |
| that is bundled with this package in the file LICENSE, and is        |
| available at through the world-wide-web at                           |
| http://www.php.net/license/3_0.txt.                                  |
| If you did not receive a copy of the PHP license and are unable to   |
| obtain it through the world-wide-web, please send a note to          |
| license@php.net so we can mail you a copy immediately.               |
+----------------------------------------------------------------------+
| Authors: Sean Coates <sean@php.net>                                  |
+----------------------------------------------------------------------+
$Id$
*/

/* Note: If someone has a more efficient way of doing this,
 *       please fee free to step up. I'm just re-using what livedocs
 *       already seems to do well... only the `ents` table is required.
 *         -S
 */

set_time_limit(0);
$scriptBegin = time();
$inCli = true;
require_once '../include/init.inc.php';
require_once '../include/lib_url_entities.inc.php';

echo "Grabbing livedocs DB...\n";

// open local file
if (!$fw = @fopen(ENTITY_SQLITE_FILE, 'w')) {
    die("Error opening local file: " . ENTITY_SQLITE_FILE ."\n");
}

// open remote livedocs DB file
if (!$fr = @fopen(REMOTE_ENTITY_SQLITE_FILE, 'r')) {
    die("Error opening remote file: " . REMOTE_ENTITY_SQLITE_FILE ."\n");
}

$bytesRead = 0;
$chunkSize = 32 * 1024; // read (up to) 32k chunks
while ($d = fread($fr, $chunkSize)) {
    $bytesRead += strlen($d);
    fwrite($fw, $d);
}

fclose($fw);
fclose($fr);

$scriptTime = time() - $scriptBegin;
$bytesSec = number_format(round($bytesRead / $scriptTime));
$bytesRead = number_format($bytesRead);
echo "Completed in $scriptTime seconds\n";
echo "$bytesRead bytes read (~$bytesSec bytes/sec)\n";

?>

<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4:
+----------------------------------------------------------------------+
| PHP Documentation Site Source Code                                   |
+----------------------------------------------------------------------+
| Copyright (c) 1997-2005 The PHP Group                                |
+----------------------------------------------------------------------+
| This source file is subject to version 3.0 of the PHP license,       |
| that is bundled with this package in the file LICENSE, and is        |
| available at through the world-wide-web at                           |
| http://www.php.net/license/3_0.txt.                                  |
| If you did not receive a copy of the PHP license and are unable to   |
| obtain it through the world-wide-web, please send a note to          |
| license@php.net so we can mail you a copy immediately.               |
+----------------------------------------------------------------------+
| Authors: Georg Richter <georg@php.net>                               |
|          Gabor Hojsty <goba@php.net>                                 |
| Docweb port: Nuno Lopes <nlopess@php.net>                            |
|              Mehdi Achour <didou@php.net>                            |
|              Sean Coates <sean@php.net>                              |
+----------------------------------------------------------------------+
$Id$
*/

set_time_limit(0);
$inCli = true;
require_once '../include/init.inc.php';

// determine type (and display usage on fail)
switch (isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : false) {
    case 'phpdoc':
        $filename = CVS_DIR . '/phpdoc-all/entities/global.ent';
        $entType = 'php';
        break;

    case 'peardoc':
        $filename = CVS_DIR . '/peardoc/global.ent';
        $entType = 'pear';
        break;

    case 'smarty':
        $filename = CVS_DIR . '/smarty/docs/entities/global.ent';
        $entType = 'smarty';
        break;
    
    default:
        echo "Usage: {$_SERVER['argv'][0]} phpdoc|peardoc|smarty\n";
        die();
}

require_once '../include/lib_url_entities.inc.php';

// create a new database (remove old first, if exists)
if (is_file(URL_ENT_SQLITE_FILE) && !unlink(URL_ENT_SQLITE_FILE)) {
    echo "Error removing old database.\n";
    die();
}
if (!($sqlite = sqlite_open(URL_ENT_SQLITE_FILE, 0666))) {
    echo "Error creating database.\n";
}

// Table creation
$sqlCreateMeta = "
    CREATE
    TABLE
        meta_info
        (
            start_time DATETIME,
            end_time DATETIME,
            schemes VARCHAR(100)
        );
";
sqlite_query($sqlite, $sqlCreateMeta);
$sqlCreateChecked = "
    CREATE
    TABLE
        checked_urls
        (
            url_num INT,
            entity VARCHAR(255),
            url VARCHAR(255),
            check_result INT,
            return_val VARCHAR(255)
        );
";
sqlite_query($sqlite, $sqlCreateChecked);

// read entities
if (!$file = @file_get_contents($filename)) {
    echo "No entities found.\n";
    die();
}
$array = explode('<!-- Obsoletes -->', $file);

// Find entity names and URLs
$schemes_preg = '(?:' . join('|', $schemes) . ')';
preg_match_all("@<!ENTITY\s+(\S+)\s+([\"'])({$schemes_preg}://[^\\2]+)\\2\s*>@U", $array[0], $entities_found);

// These are the useful parts
$entity_names = $entities_found[1];
$entity_urls  = $entities_found[3];

echo "Found: ". count($entity_urls) ."URLs\n"; 

// log start time && schemes in DB
$sql = "
    INSERT
    INTO
        meta_info (start_time, end_time, schemes)
    VALUES
        (". time() .", NULL, '". sqlite_escape_string(implode(',', $schemes)) ."')
";
sqlite_query($sqlite, $sql);

// Walk through entities found
foreach ($entity_urls as $num => $entity_url) {
    echo "Checking: $entity_url\n";
    url_store_result($sqlite, $num, $entity_names[$num], $entity_url, check_url($num, $entity_url));
}

// log end time in DB
$sql = "
    UPDATE
        meta_info
    SET
        end_time = ". time() ."
";
sqlite_query($sqlite, $sql);

echo "Done.\n";

?>

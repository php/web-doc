<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4:
+----------------------------------------------------------------------+
| PHP Documentation Site Source Code                                   |
+----------------------------------------------------------------------+
| Copyright (c) 1997-2009 The PHP Group                                |
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
$scriptBegin = time();
$inCli = true;
require_once '../include/init.inc.php';

// determine type (and display usage on fail)
switch (isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : false) {
    case 'phpdoc':
        $filename = SVN_DIR . '/' . DOC_DIR.  '/entities/global.ent';
        $entType = 'php';
        break;

    case 'peardoc':
        $filename = SVN_DIR . '/' . PEAR_DIR. '/global.ent';
        $entType = 'pear';
        break;

    case 'gtk':
        $filename = SVN_DIR . GTK_DIR. '/manual/global.ent';
        $entType = 'gtk';
        break;

    default:
        echo "Usage: {$_SERVER['argv'][0]} phpdoc|peardoc|gtk\n";
        die();
}

require_once '../include/lib_url_entities.inc.php';

echo "DocWeb URL Entity Checker.\n";
echo "Using forks? ". (NUM_ALLOWED_FORKS ? 'yes: '. NUM_ALLOWED_FORKS : 'no') . "\n";
echo "Checking " . $entType . "\n\n";

// create a new database (remove old first, if exists)
if (is_file(URL_ENT_SQLITE_FILE) && !unlink(URL_ENT_SQLITE_FILE)) {
    echo "Error removing old database.\n";
    die();
}

if (!$sqlite = url_ent_sqlite_open())
{
    echo "Error opening database.\n";
    die();
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

echo "Found: ". count($entity_urls) ." URLs\n"; 

// log start time && schemes in DB
$sql = "
    INSERT
    INTO
        meta_info (start_time, end_time, schemes)
    VALUES
        (". time() .", NULL, '". sqlite_escape_string(implode(',', $schemes)) ."')
";
sqlite_query($sqlite, $sql);

if (URL_ALLOW_FORK) {
    // use the forking method ... MUCH faster
    declare(ticks=1);
    $children = 0;
    for ($num=0; $num<count($entity_urls); $num++) {
        $url  = $entity_urls[$num];
        $name = $entity_names[$num];
        if ($children < NUM_ALLOWED_FORKS) {
            $pid = pcntl_fork();
            if ($pid) {
                // parent
                //echo "Forked: $pid\n";
                ++$children;
            } else {
                // child
                echo "[$num] (". getmypid() .") Checking: $url\n";
                url_store_result(FALSE, $num, $name, $url, check_url($num, $url));
                exit();
            }
        } else {
            // enough $children
            $status = 0;
            $child = pcntl_wait($status);
            --$children;
            echo "Child: $child exited with status $status ($children remain)\n";
        }        
    }

    while ($children) {
        $status = 0;
        $child = pcntl_wait($status);
        --$children;
        echo "Child: $child exited with status $status ($children remain)\n";
    }
    
} else {
    // no forking
    // walk through entities found
    foreach ($entity_urls as $num => $entity_url) {
        echo "[$num] Checking: $entity_url\n";
        url_store_result($sqlite, $num, $entity_names[$num], $entity_url, check_url($num, $entity_url));
    }
    ++$num; // (for the count)
}

// log end time in DB
$sql = "
    UPDATE
        meta_info
    SET
        end_time = ". time() ."
";
sqlite_query($sqlite, $sql);

$elapsed = time() - $scriptBegin;

echo "\n";
echo "Checked $num URLs.\n";
echo "Completed in $elapsed seconds.\n";

?>

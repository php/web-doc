<?php
/*
+----------------------------------------------------------------------+
| PHP Version 4                                                        |
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
| Original Authors: Thomas Schöfbeck <tom at php dot net>              |
|                   Gabor Hojtsy    <goba at php dot net>              |
|                   Mark Kronsbein    <mk at php dot net>              |
|                   Jan Fabry     <cheezy at php dot net>              |
| SQLite version Authors:                                              |
|                   Mehdi Achour   <didou at php dot net>              |
+----------------------------------------------------------------------+

*/

error_reporting(E_ALL);

/**
*	Usage
**/

// keep this call, we need it
$self = array_shift($argv);

if ($argc < 2) {
?>
  Usage:
    <?php echo $self;?> type [lang1 [lang2 [lang3 [..]]]]

  Checks the revision of translated files against the actual english
  xml files, and create an sqlite database to generate statisctics
  
  type should be a registered documentation type in 
  <?php echo dirname($self);?>/common.php

  langN should be a valid language code used in the documentation
  repository

  Read more about Revision comments and related functionality in 
  the PHP Documentation Howto :
    http://php.net/manual/howto/translation-revtrack.html (9.4.2)

<?php
exit(0);
}

/**
*	Configuration
**/

// define some common variables
$inCli = true;
include '../include/init.inc.php';

// grab the documentation type
$TYPE = array_shift($argv);

if (!in_array($TYPE, array('php', 'smarty', 'pear'))) {
    echo "Error: The revcheck script is not available yet for $TYPE\n";
    exit(0);
}

$DOCS = CVS_DIR . get_cvs_dir($TYPE);

// $argv was shifted before
$LANGS = $argv;

// generate all languages
if (count($LANGS) == 0) {
    include "../include/lib_proj_lang.inc.php";
    $LANGS = array_keys($LANGUAGES);
}

// Test the languages :
$langc = count($LANGS);
for ($i = 0; $i < $langc; $i++) {
    if (!is_dir($DOCS . $LANGS[$i])) {
        echo "Error: the \"{$LANGS[$i]}\" lang doesn't exist for $TYPE, skipping..\n";
        unset($LANGS[$i]);
    }
}
if (count($LANGS) == 0)
{
    echo "Error: No language to revcheck, exiting.\n";
    exit(0);
}

$SQL_BUFF = "INSERT INTO dirs (id, name) VALUES (1, '/');\n";

$CREATE =<<<SQL

CREATE TABLE description (
  lang TEXT,
  intro TEXT,
  date TEXT,
  charset TEXT,
  UNIQUE (lang)
);

CREATE TABLE translators (
  lang TEXT,
  nick TEXT,
  name TEXT,
  mail TEXT,
  cvs TEXT,
  editor TEXT
);

CREATE TABLE wip (
  lang TEXT,
  name TEXT,
  person TEXT,
  type TEXT
);

CREATE TABLE dirs (
  id INT,
  name TEXT,
  UNIQUE (name)
);

CREATE TABLE files (
    lang TEXT,
    dir TEXT,
    name TEXT,
    revision TEXT,
    size TEXT,
    mdate TEXT,
    maintainer TEXT,
    status TEXT
);

SQL;

/**
*	Functions
**/

function parse_translation($lang)
{
    global $SQL_BUFF, $DOCS;
    echo "Parsing intro..\n";

    // Path to find translation.xml file, set default values,
    // in case we can't find the translation file
    $translation_xml = $DOCS . $lang . "/translation.xml";

    $intro = "No intro available for the $lang translation of the manual";
    $charset  = 'iso-8859-1';

    if (file_exists($translation_xml)) {
        // Else go on, and load in the file, replacing all
        // space type chars with one space
        $txml = join("", file($translation_xml));
        $txml = preg_replace("/\\s+/", " ", $txml);

        // Get intro text
        if (preg_match("!<intro>(.+)</intro>!s", $txml, $match)) {
            $intro = trim($match[1]);
        }

        // Get encoding for the output, from the translation.xml
        // file encoding (should be the same as the used encoding
        // in HTML)
        if (preg_match("!<\?xml(.+)\?>!U", $txml, $match)) {
            $xmlinfo = parse_attr_string($match);
            if (isset($xmlinfo[1]["encoding"])) {
                $charset = $xmlinfo[1]["encoding"];
            }
        }
    }

    $SQL_BUFF .= "INSERT INTO description VALUES ('$lang', '" . sqlite_escape_string($intro) . "', DATE(), '$charset');\n";

    if (isset($txml)) {
        // Find all persons matching the pattern
        if (preg_match_all("!<person (.+)/\\s?>!U", $txml, $matches)) {
            $default = array('cvs' => 'n/a', 'nick' => 'n/a', 'editor' => 'n/a', 'email' => 'n/a', 'name' => 'n/a');
            $persons = parse_attr_string($matches[1]);

            foreach ($persons as $person) {
                $person = array_merge($default, $person);
                $SQL_BUFF .= "INSERT INTO translators VALUES ('$lang', '" . sqlite_escape_string($person['nick']) . "', '" . sqlite_escape_string($person['name']) . "', '" . sqlite_escape_string($person['email']) . "', '" . sqlite_escape_string($person['cvs']) . "', '" . sqlite_escape_string($person['editor']) . "');\n";
            }
        }

        // Get list of work in progress files
        if (preg_match_all("!<file(.+)/\\s?>!U", $txml, $matches)) {
            $files = parse_attr_string($matches[1]);
            foreach ($files as $file) {
                $SQL_BUFF .= "INSERT INTO wip VALUES ('$lang', '" . sqlite_escape_string($file['name']) . "', '" . sqlite_escape_string($file['person']) . "', '" . sqlite_escape_string(isset($file['type']) ? $file['type'] : 'translation') . "');\n";
            }
        }
    }
} // parse_translation() function end()


// Get a multidimensional array with tag attributes
function parse_attr_string($tags_attrs)
{
    $tag_attrs_processed = array();

    // Go through the tag attributes
    foreach ($tags_attrs as $attrib_list) {

        // Get attr name and values
        preg_match_all("!(.+)=\\s*([\"'])\\s*(.+)\\2!U", $attrib_list, $attribs);

        // Assign all attributes to one associative array
        $attrib_array = array();
        foreach ($attribs[1] as $num => $attrname) {
            $attrib_array[trim($attrname)] = trim($attribs[3][$num]);
        }
        // Collect in order of tags received
        $tag_attrs_processed[] = $attrib_array;
    }
    // Retrun with collected attributes
    return $tag_attrs_processed;
}

function dir_sort($a, $b) {
    global $DOCS, $dir;
    $a = $DOCS . 'en' . $dir . '/' . $a;
    $b = $DOCS . 'en' . $dir . '/' . $b;
    if (is_dir($a) && is_dir($b)) {
        return 0;
    } elseif (is_file($a) && is_file($b)) {
        return 0;
    } elseif (is_file($a) && is_dir($b)) {
        return -1;
    } elseif (is_dir($a) && is_file($b)) {
        return 1;
    } else {
        return -1;
    }
}

function do_revcheck($dir = '') {
    global $LANGS, $DOCS, $SQL_BUFF;
    static $id = 1;
    global $idx;

    if ($dh = opendir($DOCS . 'en/' . $dir)) {

        $entriesDir = array();
        $entriesFiles = array();

        while (($file = readdir($dh)) !== false) {
            if (
            (!is_dir($DOCS . 'en' . $dir.'/' .$file) && !in_array(substr($file, -3), array('xml','ent')) && substr($file, -13) != 'PHPEditBackup' )
            || ($file == "functions.xml" && strpos($dir, '/reference') !== false)
            || $dir == '/chmonly') {
                continue;
            }

            if ($file != '.' && $file != '..' && $file != 'CVS' && $dir != '/functions') {

                if (is_dir($DOCS . 'en' . $dir.'/' .$file)) {
                    $entriesDir[] = $file;
                } elseif (is_file($DOCS . 'en' . $dir.'/' .$file)) {
                    $entriesFiles[] = $file;
                }
            }
        }

        // Files first
        if (sizeof($entriesFiles) > 0 ) {

            foreach($entriesFiles as $file) {

                $path = $DOCS . 'en' . $dir . '/' . $file;

                $size = intval(filesize($path) / 1024);
                $date = filemtime($path);
                $revision = get_original_rev($path);
                $revision = ($revision == 0) ? 'NULL' : "'$revision'";

                $SQL_BUFF .= "INSERT INTO files VALUES ('en', '$id', '$file', $revision, '$size','$date', NULL, NULL);\n";

                foreach ($LANGS as $lang) {

                    $path = $DOCS . $lang . $dir . '/' . $file;
                    if (is_file($path)) {

                        $size = intval(filesize($path) / 1024);
                        $date = filemtime($path);
                        list($revision, $maintainer, $status) = get_tags($path);
                        echo " Adding file: $lang$dir/$file\n";
                        $SQL_BUFF .= "INSERT INTO files VALUES ('$lang', '$id', '$file', $revision, '$size', $date, $maintainer, $status);\n";
                    } else {
                        $SQL_BUFF .= "INSERT INTO files VALUES ('$lang', '$id', '$file', NULL, NULL, NULL, NULL, NULL);\n";
                    }
                }
            }
        }

        // Directories..
        if (sizeof($entriesDir) > 0) {

            usort($entriesDir, 'dir_sort');
            reset($entriesDir);

            foreach ($entriesDir as $Edir) {

                $path = $DOCS . 'en/' . $dir . '/' . $Edir;
                $id++;
                echo "Adding directory: $dir/$Edir (id: $id)\n";

                $SQL_BUFF .= "INSERT INTO dirs VALUES (" . $id . ", '$dir/$Edir');\n";
                do_revcheck($dir . '/' . $Edir);

            }
        }
    }
    closedir($dh);
}


function get_tags($file)
{
    // Read the first 500 chars. The comment should be at
    // the begining of the file
    $fp = @fopen($file, "r") or die ("Unable to read $file.");
    $line = fread($fp, 500);
    fclose($fp);

    // No match before the preg
    $match = array ();


    // Check for the translations "revision tag"
    if (preg_match("/<!--\s*EN-Revision:\s*\d+\.(\d+)\s*Maintainer:\s*(\\S*)\s*Status:\s*(.+)\s*-->/U",
    $line, $match)) {
        // note the simple quotes
        return array("'" . trim($match[1]) . "'", "'" . trim($match[2]) . "'", "'" . trim($match[3]) . "'");
    }

    // The tag with revision number is not found so search
    // for n/a revision comment (comment where revision is not known)
    if (preg_match("'<!--\s*EN-Revision:\s*(n/a)\s*Maintainer:\s*(\\S*)\s*Status:\s*(.+)\s*-->'U",
    $line, $match)) {
        // note the simple quotes
        return array("'" . trim($match[1]) . "'", "'" . trim($match[2]) . "'", "'" . trim($match[3]) . "'");
    }

    // Nothing, return with NULL values
    return array ("NULL", "NULL", "NULL");

} // get_tags() function end

function get_original_rev($file)
{
    // Read the first 500 chars. The comment should be at
    // the begining of the file
    $fp = @fopen($file, "r") or die ("Unable to read $file.");
    $line = fread($fp, 500);
    fclose($fp);

    // Return if this was needed (it should be there)
    // . is for $ in the preg!
    preg_match("/<!-- .Revision: \d+\.(\d+) . -->/", $line, $match);
    if (!empty($match)) {
        return $match[1];
    } else {
        return 0;
    }
}


function getmicrotime()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

/**
*	Script execution
**/

$time_start = getmicrotime();

$db_name = SQLITE_DIR . 'rev.' . $TYPE . '.sqlite';
$tmp_db = SQLITE_DIR . 'rev.' . $TYPE . '.tmp.sqlite';

// 1 - Drop the old database and create the new one
if (is_file($tmp_db)) {

    echo "Temporary database found : remove.\n";

    if (!@unlink($tmp_db)) {
        echo "Error : Can't remove temporary database\n";
        exit(0);
    }
}


// 2 - Create the new database
$idx = sqlite_open($tmp_db, 0666);

if (!$idx) {
    die("could not open $tmp_name");
}
sqlite_query($idx, $CREATE);

// 3 - Fill in the description table while cleaning the langs
// without revision.xml file
foreach ($LANGS as $id => $lang) {
    echo "Fetching the $lang description\n";
    parse_translation($lang);
}

// 4 - Recurse in the manual seeking for files and fill $SQL_BUFF
do_revcheck();

// 5 - Query $SQL_BUFF and exit

sqlite_query($idx, 'BEGIN TRANSACTION');
sqlite_query($idx, $SQL_BUFF);
sqlite_query($idx, 'COMMIT');
sqlite_close($idx);

echo "Copying temporary database to final database\n";

copy($tmp_db, $db_name);
@unlink($tmp_db);

$time_end = getmicrotime();
$time = $time_end - $time_start;

echo "Time of generation : " . $time . " s\n";
echo "End\n";
exit(1);

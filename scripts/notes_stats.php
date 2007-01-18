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
| Authors: Mehdi Achour <didou@php.net> (Original Author)              |
|          Vincent Gevers <vincent@php.net>                            |
| Credits: Sean Coates <sean@php.net>                                  |
+----------------------------------------------------------------------+
$Id$
*/

require_once '../build-ops.php';
require_once '../include/docweb_template.class.php';

// Define some globally used vars

// This setting is used in the output script
$minact = 100;

$DBFile = SQLITE_DIR . 'notes_stats.sqlite';


if (is_readable($DBFile)) {
    $sqlite = sqlite_open($DBFile, 0666);

// asuming it's not created yet
} else {
    $sqlite = create_db($DBFile);
}

if (!isset($_ENV['SKIP_NNTP'])) {

$s = nntp_connect("news.php.net") or die("failed to connect to news server\n");
$res = nntp_cmd($s, 'GROUP php.notes', 211) or die("failed to get infos on news group\n");

$first = sqlite_single_query($sqlite, 'SELECT last_article FROM info');
list($last) = explode(' ', $res);

if ($first > $last)
    die("Nothing I can do, no new notes available\n");

echo "Fetching items: $first-$last\n";
nntp_cmd($s, "XOVER $first-$last", 224) or die("failed to XOVER the new items\n");

$sql = '';
$last_update = time();

for ($i = $first; $i <= $last; ++$i) {
    $line = fgets($s, 4096);
    $n = $subj = $author = $odate = null;

    $line_parts = explode("\t", $line, 5);

    if(isset($line_parts[0])) $n      = $line_parts[0];
    if(isset($line_parts[1])) $subj   = $line_parts[1];
    if(isset($line_parts[2])) $author = $line_parts[2];
    if(isset($line_parts[3])) $odate  = $line_parts[3];

    /* check if the server has closed the connection
       the program will continue to fetch data later */
    if (feof($s)) {
        break;
    }

    echo "\r$i";

    /*
     * What should be matched:
     * note ID deleted from SECTION by EDITOR
     * note ID rejected from SECTION by EDITOR
     * note ID modified in SECTION by EDITOR
     */

    if (preg_match('/^note (\d+) (.+) (?:from|in) (.+) by (.+)/S', $subj, $d)) {
        if ($d[2] == 'approved') {
            continue;
        }

        if ($d[2] == 'rejected and deleted') {
            $d[2] = 'rejected';
        }

        if (substr($d[3], 0, -4)) {
            $d[3] = str_replace('.php', '', $d[3]);
        }

        $d[] = strtotime($odate);
        $sql .= make_sql($d);

    } // end if(preg_match

} // end for loop

@fclose($s);

// using $i to allow a fetching resume
$sql .= "UPDATE info SET last_article=$i, build_date=$last_update;";

sqlite_query($sqlite, 'BEGIN TRANSACTION');
sqlite_query($sqlite, $sql);
sqlite_query($sqlite, 'COMMIT TRANSACTION');
sqlite_close($sqlite);

} // (end SKIP_NNTP block)

/* write the output to the /www folder */
include './notes_stats_output.php';

$fp = fopen(PATH_ROOT . '/www/notes_stats-data.php', 'w');
fputs($fp, $out);
fclose($fp);

/* end of the script */



/* Open a connection to a NTTP server */
function nntp_connect($server, $port = 119) {

    if (!$socket = fsockopen($server, $port, $errno, $errstr, 30)) {
        echo "error connecting to nntp server: $errstr\n";
        return false;
    }

    if (substr(fgets($socket, 1024), 0, 4) != "200 ") {
        echo "unexpected greeting: $hello\n";
        return false;
    }

    return $socket;
}


/* issue a NTTP command */
function nntp_cmd($conn, $command, $expected) {
    if (strlen($command) > 510){
        die("command too long: $command");
    }

    fputs($conn, "$command\r\n");
    list($code,$extra) = explode(' ', fgets($conn, 1024), 2);

    return $code == $expected ? $extra : false;
}


/* create a new DB and table schema */
function create_db($DBFile) {
    echo "Creating the database: $DBFile\n";

    $sqlite = sqlite_open($DBFile, 0666);

    $sql = <<< SQL
CREATE TABLE info (
  last_article INTEGER,
  build_date INTEGER
);

CREATE TABLE notes (
  note INTEGER,
  action TEXT,
  manpage TEXT,
  who TEXT,
  time INTEGER
);

INSERT INTO info VALUES(1, 0);
SQL;

    sqlite_query($sqlite, $sql);
    return $sqlite;
}


/* makes a sql insert statment from an array */
function make_sql($array) {
    array_shift($array);
    return 'INSERT INTO notes VALUES ("' . implode('", "', $array) . '");';
}

?>

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
| Credits: Sean Coates <sean@php.net>
+----------------------------------------------------------------------+
$Id$
*/

/*
 * Warning: This script uses a lot of memory 
 *
 * Usage:
 * $ php notes_stats.php [mbox-file] > notes.php
 */
 
/*
 * TODO:
 * - Database improvements
 * - Speed improvements
 * - Nicer layout
 */

// minimum amount actions
$minact = 100;
// after how many secs should the list be chopped
$after = 182.5*24*60*60; // half year

$inputs = array(); // pair subjects w/ dates from multiple sources

$time_start = getmicrotime();

//$inCli = true;
require_once '../build-ops.php';

$DBFile = SQLITE_DIR . 'notes_stats.sqlite';

if (!is_readable($DBFile)) {
// asuming it's not created yet

echo 'Creating the database: ' . $DBFile;

$sqlite = sqlite_open($DBFile, 0666);

// Table creation
$sqlCreateInfo = "
    CREATE
    TABLE
        notes_info
        (
            last_article INT,
            build_date DATETIME,
            subjects INT
        );
";
sqlite_query($sqlite, $sqlCreateInfo);
// total user stats (als required for proper working)
$sqlCreateStats = "
    CREATE
    TABLE
        notes_stats
        (
            username VARCHAR(255),
            deleted INT,
            rejected INT,
            modified INT,
            total INT
        );
";
sqlite_query($sqlite, $sqlCreateStats);
// older than $after
$sqlCreateStatsOld = "
    CREATE
    TABLE
        notes_stats_old
        (
            username VARCHAR(255),
            deleted INT,
            rejected INT,
            modified INT,
            total INT
        );
";
sqlite_query($sqlite, $sqlCreateStatsOld);
// not more than $after
$sqlCreateStatsNew = "
    CREATE
    TABLE
        notes_stats_new
        (
            username VARCHAR(255),
            deleted INT,
            rejected INT,
            modified INT,
            total INT
        );
";
sqlite_query($sqlite, $sqlCreateStatsNew);
$sqlCreateFiles = "
    CREATE
    TABLE
        notes_files
        (
            page VARCHAR(255),
            total INT
        );
";
sqlite_query($sqlite, $sqlCreateFiles);

// only if this is a fresh db
$sql = "
    INSERT
    INTO
        notes_info (last_article, build_date, subjects)
    VALUES
        (1, ".time().", 0)
";
sqlite_query($sqlite, $sql);

sqlite_close($sqlite);

clearstatcache();
if (is_readable($DBFile)) {
    echo "\nOK, everything went fine while creating the database\n";
} else {
    echo "\nError: Please check the " . SQLIE_DIR . " dir for write access\n";
    exit;
}

}
// done creating a fresh db

// everyting is ok, open the db
$sqlite = sqlite_open($DBFile, 0666);

$bytesRead = 0;

$s = nntp_connect("news.php.net") or die("failed to connect to news server");
$res = nntp_cmd($s,"GROUP php.notes",211) or die("failed to get infos on news group");

$sql = "SELECT
            last_article
        FROM
             notes_info
";         
list($first) = sqlite_fetch_array(sqlite_query($sqlite, $sql));

$new = explode(" ", $res);
$last =  $new[0];

//$first = 82000;
//$last =  84164;

if ($first == $last)
    die("Nothing I can do...\n");

echo "Fetching items: $first-$last\n";
$res = nntp_cmd($s,"XOVER $first-$last", 224) or die("failed to XOVER the new items");

$files = $team = $tmp = array();

$in_old = false;
$tmp['o'] = array();
$tmp['n'] = array();
for ($i = $first; $i < $last; $i++) {
    $line = fgets($s, 4096);
    $GLOBALS['bytesRead'] += strlen($line);
    list($n,$subj,$author,$odate) = explode("\t", $line, 5);

    if (feof($s)) {
        die("EOF\n");
    }
echo "$i: $subj @ $odate\n";   

/*
 * What should be matched:
 * note ID deleted from SECTION by EDITOR
 * note ID rejected from SECTION by EDITOR
 * note ID modified in SECTION by EDITOR
 * note ID moved from SECTION to SECTION by EDITOR (not matched yet)
 */

$reg = '/^note (\d*) (.*) (?:from|in) (\S*) by (\w*)/';

    if (preg_match($reg, $subj, $d)) {
        if ($d[2] == 'rejected and deleted')
            $d[2] = 'rejected';
        if ($d[2] == 'approved')
            continue;
         
        if(calc_time($odate)) {
            // 'new' before $after
            @$team['n'][$d[4]]['total']++;
            @$team['n'][$d[4]][$d[2]]++; 
            @$tmp['n'][$d[4]]++;
            @$files['n'][$d[3]]++;
        } else {
            // 'old' after $after
            @$team['o'][$d[4]][$d[2]]++; 
            @$tmp['o'][$d[4]]++;
            @$team['o'][$d[4]]['total']++; 
            @$files['o'][$d[3]]++;
        }
        
        // the normal arrays
        @$team[$d[4]]['total']++;
        @$team[$d[4]][$d[2]]++; 
        @$tmp[$d[4]]++;
        @$files[$d[3]]++;
 
    } // end if(preg_match
    if ($i == $last)
        break;
} // end for loop

$dlDone = getmicrotime();

ksort($team);
arsort($files);
arsort($tmp);
arsort($tmp['n']);
arsort($tmp['o']);

// SELECT all users first, check if they are in the db :: UPDATE } else { INSERT

$sql = "SELECT
            username
        FROM
            notes_stats
";

$result = sqlite_query($sqlite, $sql);

$users = array();
while ($row = sqlite_fetch_array($result, SQLITE_ASSOC)) {
    $users[] = $row['username'];
}     


// Total editor stats
foreach ($tmp as $user => $total) {
    if($user == 'o' or $user =='n')
       continue;

if (in_array($user, $users)) {
// update
$sql = "
        UPDATE
            notes_stats
        SET
            deleted = deleted + '".(isset($team[$user]['deleted']) ? $team[$user]['deleted'] : '0')."',
            rejected = rejected + '".(isset($team[$user]['rejected']) ? $team[$user]['rejected'] : '0')."',
            modified = modified + '".(isset($team[$user]['modified']) ? $team[$user]['modified'] : '0')."',
            total = total + '".$total."'
        WHERE
            username = '" . $user . "'
";
} else {
// insert       
$sql = "
        INSERT
        INTO
            notes_stats (username, deleted, rejected, modified, total)
        VALUES
            (
            '".escape($user)."',
            '".(isset($team[$user]['deleted']) ? $team[$user]['deleted'] : '0')."',
            '".(isset($team[$user]['rejected']) ? $team[$user]['rejected'] : '0')."',
            '".(isset($team[$user]['modified']) ? $team[$user]['modified'] : '0')."',
            '".$total."'
            )
";
}

sqlite_query($sqlite, $sql);
   
}

// Last half year (with more than $minact actions counted)
foreach ($tmp['n'] as $user => $total) {

if (in_array($user, $users)) {
// update
$sql = "
        UPDATE
            notes_stats_new
        SET
            deleted = deleted + '".(isset($team['n'][$user]['deleted']) ? $team['n'][$user]['deleted'] : '0')."',
            rejected = rejected + '".(isset($team['n'][$user]['rejected']) ? $team['n'][$user]['rejected'] : '0')."',
            modified = modified + '".(isset($team['n'][$user]['modified']) ? $team['n'][$user]['modified'] : '0')."',
            total = total + '".$total."'
        WHERE
            username = '" . $user . "'
";
} else {
// insert       
$sql = "
        INSERT
        INTO
            notes_stats_new (username, deleted, rejected, modified, total)
        VALUES
            (
            '".escape($user)."',
            '".(isset($team['n'][$user]['deleted']) ? $team['n'][$user]['deleted'] : '0')."',
            '".(isset($team['n'][$user]['rejected']) ? $team['n'][$user]['rejected'] : '0')."',
            '".(isset($team['n'][$user]['modified']) ? $team['n'][$user]['modified'] : '0')."',
            '".$total."'
            )
";
}

sqlite_query($sqlite, $sql);

}

// Before the last half year (with more than $minact actions counted)
foreach ($tmp['o'] as $user => $total) {


if (in_array($user, $users)) {
// update
$sql = "
        UPDATE
            notes_stats_old
        SET
            deleted = deleted + '".(isset($team['o'][$user]['deleted']) ? $team['o'][$user]['deleted'] : '0')."',
            rejected = rejected + '".(isset($team['o'][$user]['rejected']) ? $team['o'][$user]['rejected'] : '0')."',
            modified = modified + '".(isset($team['o'][$user]['modified']) ? $team['o'][$user]['modified'] : '0')."',
            total = total + '".$total."'
        WHERE
            username = '" . $user . "'
";
} else {
// insert       
$sql = "
        INSERT
        INTO
            notes_stats_old (username, deleted, rejected, modified, total)
        VALUES
            (
            '".escape($user)."',
            '".(isset($team['o'][$user]['deleted']) ? $team['o'][$user]['deleted'] : '0')."',
            '".(isset($team['o'][$user]['rejected']) ? $team['o'][$user]['rejected'] : '0')."',
            '".(isset($team['o'][$user]['modified']) ? $team['o'][$user]['modified'] : '0')."',
            '".$total."'
            )
";
}

sqlite_query($sqlite, $sql);

}

// SELECT all sections first, check if they are in the db :: UPDATE } else { INSERT

$sql = "SELECT
            page
        FROM
            notes_files
";

$result = sqlite_query($sqlite, $sql);

$pages = array();
while ($row = sqlite_fetch_array($result, SQLITE_ASSOC)) {
    $pages[] = $row['page'];
}

// Manual pages most active top 20

$i = 0;
    foreach($files as $page => $total) {
    if($page == 'o' or $page =='n' or $page == '')
       continue;
       
        $i++;
        
if (in_array($page, $pages)) {
// update
$sql = "UPDATE
            notes_files
        SET
            total = total + " . (isset($total) ? $total : '0') . "
        WHERE
            page = '" . $page . "'
";
} else {        
// insert
$sql = "INSERT
        INTO
            notes_files (page, total)
        VALUES
            ('" . $page . "', " . $total . ")
";
}
sqlite_query($sqlite, $sql);

}       

// update information
$sql = "
    UPDATE
        notes_info
    SET
        last_article = ". $last .",
        subjects =  subjects + " . (is_array($files) ? array_sum($files) : '0') . ",
        build_date = " . time() . "
";
sqlite_query($sqlite, $sql);

$scriptTime = number_format(getmicrotime() - $time_start, 4);
$bytesSec = number_format(round($GLOBALS['bytesRead'] / ($dlDone - $time_start)));
$bytesRead = number_format($bytesRead);
echo "Completed in $scriptTime seconds\n";
echo "$bytesRead bytes read (~$bytesSec bytes/sec)\n";

sqlite_close($sqlite);


function escape ($data)
{
    return sqlite_escape_string($data);
}

function nntp_connect($server,$port=119) {
  $s = fsockopen($server,$port,$errno,$errstr,30);

  if (!$s) {
    echo "<!-- error connecting to nntp server: $errstr -->\n";
    return false;
  }
  $hello = fgets($s, 1024);
  if (substr($hello,0,4) != "200 ") {
    echo "<!-- unexpected greeting: $hello -->\n";
    return false;
  }
  #echo "<!-- $hello -->\n";
  return $s;
}

function nntp_cmd($conn,$command,$expected) {
  if (strlen($command) > 510) die("command too long: $command");
  fputs($conn, "$command\r\n");
  $res = fgets($conn, 1024);
  $GLOBALS['bytesRead'] += strlen($res);
  list($code,$extra) = explode(" ", $res, 2);
  return $code == $expected ? $extra : false;
}

function getmicrotime() { 
    list($usec, $sec) = explode(" ", microtime()); 
    return ((float)$usec + (float)$sec); 
} 

function calc_time ($before) {
global $after;

// simple caching
if(@$in_old == true) 
   return false;

if (!is_numeric($before)) {
    $before = strtotime($before);
}

$afterall =  $before - (time() - $after);

if($afterall > 0) {
    // more then $after
    $in_old = false;    
    return true;
} else {
    // older then $after
    $in_old = true;
    return false;
}

}


?>

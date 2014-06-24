<?php
/*
+----------------------------------------------------------------------+
| PHP Documentation Tools Site Source Code                             |
+----------------------------------------------------------------------+
| Copyright (c) 1997-2014 The PHP Group                                |
+----------------------------------------------------------------------+
| This source file is subject to version 3.0 of the PHP license,       |
| that is bundled with this package in the file LICENSE, and is        |
| available through the world-wide-web at the following url:           |
| http://www.php.net/license/3_0.txt.                                  |
| If you did not receive a copy of the PHP license and are unable to   |
| obtain it through the world-wide-web, please send a note to          |
| license@php.net so we can mail you a copy immediately.               |
+----------------------------------------------------------------------+
| Authors:          Yannick Torres <yannick@php.net>                   |
|                   Mehdi Achour <didou@php.net>                       |
|                   Maciej Sobaczewski <sobak@php.net>                 |
+----------------------------------------------------------------------+
*/

// A file is criticaly "outdated' if
define("ALERT_SIZE",   3); // translation is  3 or more kB smaller than the en one
define("ALERT_DATE", -30); // translation is 30 or more days older than the en one

// Return an array of directory containing outdated files
function get_dirs($idx, $lang) {
    $sql = 'SELECT
        distinct b.name AS name, 
        a.dir AS dir
    FROM 
        files a, 
        dirs b 
    LEFT JOIN
        files c 
    ON
        c.name = a.name 
    AND
        c.dir = a.dir 
    WHERE 
        b.id = a.dir 
    AND
        a.lang="' . $lang . '" 
    AND
        c.lang="en" 
    AND
        a.revision != c.revision 
    ORDER BY
        b.name';

    $result = $idx->query($sql);

    $tmp = array();
    while ($r = $result->fetchArray()) {
        $tmp[$r['dir']] = $r['name'];
    }

    return $tmp;
}

// return an array with the outdated files; can be optionally filtered by user or dir
function get_outdated_files($idx, $lang, $filter = null, $value = null)
{
    $sql = 'SELECT a.status, a.name AS file, a.maintainer, c.revision AS en_rev, a.revision AS trans_rev, b.name AS name, a.dir AS dir
    FROM files a, dirs b
    LEFT JOIN files c ON c.name = a.name AND c.dir = a.dir
    WHERE b.id = a.dir AND a.lang="' . $lang . '" AND a.revision != c.revision AND c.lang="en" ';

    if ($filter == 'dir') {
        $sql .= 'AND a.dir = '.(int)$value;
    }
    elseif ($filter == 'translator') {
        $sql .= 'AND a.maintainer = "'.SQLite3::escapeString($value).'"';
    }

    $sql .= ' ORDER BY b.name';

    $result = $idx->query($sql);
    $tmp = array();
    while ($r = $result->fetchArray()) {
        $tmp[] = array(
        'name' => $r['name'],
        'en_rev' => $r['en_rev'],
        'trans_rev' => $r['trans_rev'],
        'status' => $r['status'],
        'maintainer' => $r['maintainer'],
        'file' => $r['file']);
    }

    return $tmp;
}

// Return an array of available languages for manual
function revcheck_available_languages($idx)
{
    $result = $idx->query('SELECT lang FROM description');
    while ($row = $result->fetchArray(SQLITE3_NUM)) {
		$tmp[] = $row[0];
	}

	return $tmp;
}


// Return en integer
function count_en_files($idx)
{
    $sql = "SELECT COUNT(name) FROM files WHERE lang = 'en'";
    $res = $idx->query($sql);
    $row = $res->fetchArray();
    return $row[0];
}

function get_missfiles($idx, $lang)
{
    $sql = 'SELECT
        d.name as dir, 
        b.size as size, 
        a.name as file 
    FROM
        files a, 
        dirs d 
    LEFT JOIN
        files b 
    ON 
        a.dir = b.dir 
    AND
        a.name = b.name 
    WHERE 
        a.lang="' . $lang . '" 
    AND
        b.lang="en" 
    AND
        a.revision IS NULL
    AND
        a.size IS NULL
    AND
        a.dir = d.id';
    $result = $idx->query($sql);

    while ($r = $result->fetchArray()) {
        $tmp[] = array('dir' => $r['dir'], 'size' => $r['size'], 'file' => $r['file']);
    }

    return $tmp;
}

function get_oldfiles($idx, $lang)
{
    $sql = 'SELECT
     dir, file, size

     FROM
     old_files

     WHERE
     lang="' . $lang . '"';

    $result = $idx->query($sql);

    $tmp = array();
    $special_files = array(
        'translation.xml'=>1,
    );

    while ($r = $result->fetchArray()) {
        if (isset($special_files[$r['file']])) continue; // skip some files
        $tmp[] = array('dir' => $r['dir'], 'size' => $r['size'], 'file' => $r['file']);
    }
    return $tmp;
}

function get_misstags($idx, $lang)
{
    $sql = 'SELECT
     d.name AS dir, b.size AS en_size, a.size AS trans_size, a.name AS name
     FROM files a, dirs d 
     LEFT JOIN files b ON a.dir = b.dir AND a.name = b.name 
     WHERE a.lang="'.$lang.'" AND b.lang="en" AND (a.revision IS NULL OR a.revision = "n/a")
     AND a.size IS NOT NULL AND a.dir = d.id';

    $result = $idx->query($sql);
    while($row = $result->fetchArray()) {
        $tmp[] = $row;
    }

    return $tmp;
}

/**
 * Returns translators' stats of specified $lang
 * Replaces old translator_get_wip(), translator_get_old(),
 * translator_get_critical() and translator_get_uptodate() functions
 *
 * @param string  $status  one of [uptodate, old, critical, wip]
 * @return array
 */
function get_translators_stats($idx, $lang, $status) {
    if ($status == 'wip') { // special case, ehh; does anyone still use this status?
        $sql = "SELECT COUNT(name) AS total, person AS maintainer
        FROM wip
        WHERE lang = '$lang'
        GROUP BY maintainer";
    }
    else {
        $sql = "SELECT COUNT(a.name) AS total, a.maintainer
        FROM files a
        LEFT JOIN files b ON a.name = b.name AND a.dir = b.dir
        WHERE a.lang = '$lang' AND b.lang = 'en' AND a.size IS NOT NULL AND ";

        if ($status == 'uptodate') {
            $sql .= 'a.revision = b.revision';
        }
        elseif ($status == 'old') {
            $sql .= 'b.revision != a.revision AND b.size - a.size < ' . ALERT_SIZE . ' AND (b.mdate - a.mdate) / 86400  < ' . ALERT_DATE;
        }
        elseif ($status == 'critical') {
            $sql .= 'b.revision != a.revision AND (b.size - a.size >= ' . (1024 * ALERT_SIZE) . ' OR (b.mdate - a.mdate) / 86400 >= ' . ALERT_DATE . ')';
        }

        $sql .= ' GROUP BY a.maintainer';
    }

    $result = $idx->query($sql);

    $tmp = array();
    while ($r = $result->fetchArray()) {
        $tmp[$r['maintainer']] = $r['total'];
    }

    return $tmp;
}

function get_translators($idx, $lang)
{
    $sql = "SELECT nick, name, mail, svn FROM translators WHERE lang = '$lang' ORDER BY nick COLLATE NOCASE";
    $persons = array();
    $result = $idx->query($sql);
    while ($r = $result->fetchArray()) {
        $persons[$r['nick']] = array('name' => $r['name'], 'mail' => $r['mail'], 'svn' => $r['svn']);
    }
    return $persons;
}

/**
 * Returns statistics of specified $lang
 * Replaces old get_stats_uptodate(), get_stats_old(),
 * get_stats_critical(), get_stats_wip(), get_stats_notrans()
 * and get_stats_notag() functions
 *
 * @param string  $status  one of [uptodate, old, critical, wip, notrans, norev]
 * @return array
 */
function get_stats($idx, $lang, $status) {
    if ($status == 'wip') { // special case, ehh; does anyone still use this status?
        $sql = "SELECT COUNT(*) AS total, 0 AS size
        FROM wip
        WHERE lang = '$lang'";
    }
    else {
        $sql = "SELECT COUNT(a.name) AS total, SUM(b.size) AS size
        FROM files a
        LEFT JOIN files b ON a.name = b.name AND a.dir = b.dir
        WHERE a.lang = '$lang' AND b.lang = 'en' AND ";

        if ($status == 'uptodate') {
            $sql .= 'a.revision = b.revision';
        }
        elseif ($status == 'old') {
            $sql .= 'b.revision != a.revision AND b.size - a.size < ' . ALERT_SIZE . ' AND (b.mdate - a.mdate) / 86400  < ' . ALERT_DATE . ' AND a.size IS NOT NULL';
        }
        elseif ($status == 'critical') {
            $sql .= 'b.revision != a.revision AND (b.size - a.size >= ' . (1024 * ALERT_SIZE) . ' OR (b.mdate - a.mdate) / 86400 >= ' . ALERT_DATE . ') AND a.revision != "n/a" AND a.size IS NOT NULL';
        }
        elseif ($status == 'norev') {
            $sql .= '(a.revision IS NULL OR a.revision = "n/a") AND a.size IS NOT NULL';
        }
        elseif ($status == 'notrans') {
            $sql .= 'a.revision IS NULL AND a.size IS NULL';
        }
    }

    $result = $idx->query($sql)->fetchArray();

    return array($result['total'], $result['size']);
}

function gen_date($file)
{
    $unix = filemtime($file);
    return '<time class="gen-date" datetime="'.date(DATE_W3C, $unix).'">Generated: '.date('d M Y H:i:s', $unix).'</time>';
}
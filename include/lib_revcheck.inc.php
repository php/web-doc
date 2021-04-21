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
| Authors:          Nilgün Belma Bugüner <nilgun@php.net>              |
                    Yannick Torres <yannick@php.net>                   |
|                   Mehdi Achour <didou@php.net>                       |
|                   Maciej Sobaczewski <sobak@php.net>                 |
+----------------------------------------------------------------------+
*/

// Return an array of directory containing outdated files
function get_dirs($idx, $lang) {
    $sql = "SELECT
        b.path AS dir,
        a.name AS name
    FROM
        files a,
        dirs b,
        enfiles c
    WHERE
        a.lang= '$lang'
    AND
         a.id = b.id
    AND
         c.id = a.id
    AND
         a.name = c.name
    AND
        a.revision != c.revision
    AND
        a.revision != 'n/a'
    ORDER BY
        b.path";

    $result = $idx->query($sql);

    $tmp = array();
    while ($r = $result->fetchArray()) {
        $tmp[$r['dir']] = $r['dir'];
    }

    return $tmp;
}

// return an array with the outdated files; can be optionally filtered by user or dir
function get_outdated_files($idx, $lang, $filter = null, $value = null)
{
    $sql = "SELECT a.status, a.name AS file, a.maintainer, c.revision AS en_rev, a.revision AS trans_rev, b.path AS dir
    FROM files a, dirs b, enfiles c
    WHERE a.lang = '$lang'
     AND a.revision != c.revision AND a.revision != 'n/a'
     AND c.name = a.name AND c.id = a.id AND b.id = a.id";

    if ($filter == 'dir') {
        $sql .= " AND b.path = '$value'";
    }
    elseif ($filter == 'translator') {
        $sql .= ' AND a.maintainer = "'.SQLite3::escapeString($value).'"';
    }

    $sql .= ' ORDER BY b.path';

    $result = $idx->query($sql);
    $tmp = array();
    while ($r = $result->fetchArray()) {
        $tmp[] = array(
        'file' => $r['file'],
        'en_rev' => $r['en_rev'],
        'trans_rev' => $r['trans_rev'],
        'status' => $r['status'],
        'maintainer' => $r['maintainer'],
        'name' => $r['dir']);
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
    $sql = "SELECT COUNT(name) FROM enfiles";
    $res = $idx->query($sql);
    $row = $res->fetchArray();
    return $row[0];
}

function get_missfiles($idx, $lang)
{
    $sql = 'SELECT
        d.path as dir,
        b.size as size,
        b.revision as revision,
        a.name as file
    FROM
        utfiles a,
        dirs d
    LEFT JOIN
        enfiles b
    ON
        a.id = b.id
    AND
        a.name = b.name
    WHERE
        a.lang="' . $lang . '"
    AND
        a.id = d.id';
    $result = $idx->query($sql);

    while ($r = $result->fetchArray()) {
        $tmp[] = array('dir' => $r['dir'], 'size' => $r['size'], 'revision' => $r['revision'], 'file' => $r['file']);
    }

    return $tmp;
}

function get_oldfiles($idx, $lang)
{
    $sql = 'SELECT
     path, file, size

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
        $tmp[] = array('dir' => $r['path'], 'size' => $r['size'], 'file' => $r['file']);
    }
    return $tmp;
}

function get_misstags($idx, $lang)
{
    $sql = 'SELECT
     d.path AS dir, a.size AS en_size, b.size AS trans_size, a.name AS name
     FROM enfiles a, dirs d
     LEFT JOIN files b ON a.id = b.id AND a.name = b.name AND a.id = d.id
     WHERE b.lang="'.$lang.'" AND (b.revision IS NULL OR b.revision = "n/a")
     AND b.size IS NOT NULL
     ORDER BY dir, name';
    $tmp = NULL;
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
        LEFT JOIN enfiles b ON a.name = b.name AND a.id = b.id
        WHERE a.lang = '$lang' AND a.size IS NOT NULL AND ";

        if ($status == 'uptodate') {
            $sql .= 'a.revision = b.revision';
        }
        elseif ($status == 'outdated') {
            $sql .= 'b.revision != a.revision';
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
    $sql = "SELECT nick, name, mail, karma FROM translators WHERE lang = '$lang' ORDER BY nick COLLATE NOCASE";
    $persons = array();
    $result = $idx->query($sql);
    while ($r = $result->fetchArray()) {
        $persons[$r['nick']] = array('name' => $r['name'], 'mail' => $r['mail'], 'karma' => $r['karma']);
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
        FROM files a, dirs b, enfiles c
        WHERE lang = '$lang' AND a.status ='wip' AND b.id = a.id AND b.id = c.id AND a.name = c.name";
    }
    elseif ($status == 'notrans') {
        $sql = "SELECT COUNT(a.name) AS total, SUM(c.size) AS size FROM utfiles a, dirs b, enfiles c WHERE a.lang = '$lang' AND b.id = a.id AND b.id = c.id AND a.name = c.name";
    }
    else {
        $sql = "SELECT COUNT(a.name) AS total, SUM(b.size) AS size
        FROM enfiles a
        LEFT JOIN files b ON a.name = b.name AND a.id = b.id
        WHERE b.lang = '$lang' AND ";

        if ($status == 'uptodate') {
            $sql .= 'a.revision = b.revision';
        }
        elseif ($status == 'outdated') {
            $sql .= 'b.revision != a.revision AND a.revision != "n/a" AND a.size IS NOT NULL';
        }
        elseif ($status == 'norev') {
            $sql .= '(a.revision IS NULL OR a.revision = "n/a") AND a.size IS NOT NULL';
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

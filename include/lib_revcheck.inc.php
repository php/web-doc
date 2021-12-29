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
        d.path AS dir
    FROM
        translated a,
        dirs d
    WHERE
        a.lang = '$lang'
    AND a.id = d.id
    AND (a.syncStatus = 'TranslatedOld'
    OR   a.syncStatus = 'TranslatedWip')
    ORDER BY
        d.id";

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
    FROM translated a, dirs b, enfiles c
    WHERE a.lang = '$lang'
      AND c.name = a.name AND b.id = a.id AND b.id = c.id
      AND (a.syncStatus = 'TranslatedOld'
       OR  a.syncStatus = 'TranslatedWip')";

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
    $result = $idx->query('SELECT lang FROM descriptions');
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
    $sql = "SELECT
        d.path as dir,
        a.name as file,
        b.revision as revision,
        a.size as size
    FROM
        Untranslated a,
        enfiles b,
        dirs d
    WHERE
        a.lang = '$lang'
    AND
        a.name = b.name
    AND
        a.id = b.id
    AND
        a.id = d.id";
    $result = $idx->query($sql);

    while ($r = $result->fetchArray()) {
        $tmp[] = array('dir' => $r['dir'], 'size' => $r['size'], 'revision' => $r['revision'], 'file' => $r['file']);
    }

    return $tmp;
}

function get_oldfiles($idx, $lang)
{
    $sql = "SELECT path, name, size
     FROM  notinen
     WHERE lang = '$lang'";

    $result = $idx->query($sql);
    $tmp = array();

    while ($r = $result->fetchArray()) {
        $tmp[] = array('dir' => $r['path'], 'size' => $r['size'], 'file' => $r['name']);
    }
    return $tmp;
}

function get_misstags($idx, $lang)
{
    $sql = "SELECT d.path AS dir, a.size AS en_size, b.size AS trans_size, a.name AS name
     FROM enfiles a, translated b, dirs d
     WHERE b.lang = '$lang' AND b.syncStatus = 'RevTagProblem'
     AND a.id = b.id AND a.name = b.name AND a.id = d.id
     ORDER BY dir, name";
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
        $sql = "SELECT files_wip AS total, nick AS maintainer
        FROM translators
        WHERE lang = '$lang'
        GROUP BY maintainer";
    } elseif ($status == 'uptodate') {
        $sql = "SELECT files_uptodate AS total, nick AS maintainer
        FROM translators
        WHERE lang = '$lang'
        GROUP BY maintainer";
    } elseif ($status == 'outdated') {
        $sql = "SELECT files_outdated AS total, nick AS maintainer
        FROM translators
        WHERE lang = '$lang'
        GROUP BY maintainer";
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
    $sql = "SELECT nick, name, mail, vcs FROM translators WHERE lang = '$lang' ORDER BY nick COLLATE NOCASE";
    $persons = array();
    $result = $idx->query($sql);
    while ($r = $result->fetchArray()) {
        $persons[$r['nick']] = array('name' => $r['name'], 'mail' => $r['mail'], 'karma' => $r['vcs']);
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
    $sql = "SELECT COUNT(a.name) AS total, SUM(b.size) AS size
        FROM translated a, enfiles b
        WHERE a.lang = '$lang' AND a.id = b.id AND a.name = b.name AND ";
    if ($status == 'wip') {
         $sql .= "a.syncStatus = 'TranslatedWip'";
    } elseif ($status == 'notrans') {
        $sql = "SELECT COUNT(name) AS total, SUM(size) AS size
        FROM Untranslated
        WHERE lang = '$lang'";
    } elseif ($status == 'uptodate') {
        $sql .= "a.syncStatus = 'TranslatedOk'";
    } elseif ($status == 'outdated') {
        $sql .= "syncStatus = 'TranslatedOld'";
    } elseif ($status == 'norev') {
        $sql .= "syncStatus = 'RevTagProblem'";
    } else { //notinen
        $sql = "SELECT COUNT(name) AS total, SUM(size) AS size
        FROM notinen WHERE lang = '$lang'";

    }
    $result = $idx->query($sql)->fetchArray();
    return array($result['total'], $result['size']);
}

function showdiff ()
{
    if (isset($_GET['f'])) {
        $gitfile = $_GET['f'];
        if (isset($_GET['hbp']))
            $h = $_GET['hbp'];
        if (isset($_GET['c']))
            $c = $_GET['c'];
        $cwd = getcwd();
        chdir( GIT_DIR . 'en' );
        $arg_h = escapeshellarg($h);
        $arg_f = escapeshellarg($gitfile);
        $file = `git diff {$arg_h} -- {$arg_f}`;
        chdir( $cwd );
        $raw = htmlspecialchars( $file, ENT_XML1, 'UTF-8' );
        $trans = [ " " => "&nbsp;" ];
        $lines = explode ( "\n" , $raw );
        echo "<div style='font:87% monospace;overflow-wrap:break-word;'>";
        foreach ( $lines as $line ) {
            $inline = strtr( $line , $trans );
            $fc = substr( $inline , 0 , 1 );
            if ( $c == 'on' ) {
                if ( $fc == "+" ) {
                    echo "<div style='color:darkgreen;background-color:#e6ffec;'>";
                } elseif ( $fc == "-" ) {
                    echo "<div style='color:firebrick;background-color:#ffebe9;'>";
                } elseif ( $fc == "@" ) {
                   echo "<div style='color:darkblue;background-color:#ddf4ff;'>";
                } else
                     echo "<div style='color:gray;line-height:1.2;'>";
                echo "$inline</div>\n";
            } else
                if ( ($fc == "+") || ($fc == "-") || ($fc == "@"))
                    echo "<div style='background-color:#f0f0f0;'>$inline</div>\n";
                else
                    echo "<div style='color:gray;line-height:1.2;'>$inline</div>\n";
       }
       echo "</div><p></p>";
   }
}

function gen_date($file)
{
    $unix = filemtime($file);
    return '<time class="gen-date" datetime="'.date(DATE_W3C, $unix).'">Generated: '.date('d M Y H:i:s', $unix).'</time>';
}

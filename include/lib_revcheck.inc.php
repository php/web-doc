<?php
/*
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
| Authors:          Yannick Torres <yannick@php.net>                   |
|                   Mehdi Achour <didou@php.net>                       |
|                   Maciej Sobaczewski <sobak@php.net>                 |
+----------------------------------------------------------------------+
*/

// A file is criticaly "outdated' if
define("ALERT_REV",   10); // translation is 10 or more revisions behind the en one
define("ALERT_SIZE",   3); // translation is  3 or more kB smaller than the en one
define("ALERT_DATE", -30); // translation is 30 or more days older than the en one

// Return an array of directory containing outdated files
function get_dirs($idx, $lang)
{
    $sql = 'SELECT
        distinct b.name AS name, 
        a.dir as dir 
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

// return an array with the outdated files in $dir
function get_outdated_files($idx, $lang, $dir)
{
    $sql = 'SELECT
        a.status as status, 
        a.name as file, 
        a.maintainer as maintainer, 
        c.revision as en_rev, 
        a.revision as trans_rev,
        b.name AS name, 
        a.dir as dir 
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
        a.dir = "' . (int)$dir . '"
    AND
        a.revision != c.revision ORDER BY b.name';
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

// return an array with the outdated files maintained by $user
function get_outdated_translator_files($idx, $lang, $user)
{
    $sql = 'SELECT
        a.status as status,
        a.name as file,
        a.maintainer as maintainer,
        c.revision as en_rev,
        a.revision as trans_rev,
        b.name AS name,
        a.dir as dir
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
        a.maintainer = \'' . SQLite3::escapeString($user) . '\'
    AND
        a.revision != c.revision ORDER BY b.name';
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

// Return an array of available languages for a revchecked documentation $type
function revcheck_available_languages($idx)
{
    $tmp = array();

    if (!$idx) {
        return FALSE;
    }

    $sql = 'SELECT distinct lang FROM description';
    $result = @$idx->query($sql);;

    if (!$result) {
        return FALSE;
    }

    if ($result) {
        while ($r = $result->fetchArray()) {
            $tmp[] = $r['lang'];
        }
    }
    return $tmp;
}


// Return en integer
function get_nb_EN_files($idx)
{
    $sql = "SELECT COUNT(*) AS total FROM files WHERE lang = 'en'";
    $res = $idx->query($sql);;
    $row = $result->fetchArray();
    return $row['total'];
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
        a.revision is NULL 
    AND
        a.size is NULL 
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
     WHERE a.lang="'.$lang.'" AND b.lang="en" AND a.revision IS NULL 
     AND a.size IS NOT NULL AND a.dir = d.id';

    $result = $idx->query($sql);
    while($row = $result->fetchArray()) {
        $tmp[] = $row;
    }

    return $tmp;
}

// Return an integer
function get_nb_LANG_files($idx)
{
    $sql = '
    SELECT
        lang,
        COUNT(*) as total
    FROM
        files
    WHERE
        lang != \'en\'
    GROUP BY
        lang
    ORDER BY
        lang
    ';

    $result = $idx->query($sql);
    while ($row = $result->fetchArray()) {
        $files[$row['lang']] = $row['total'];
    }
    return $files;
}

// Return a string
function translator_get_wip($idx, $lang)
{
    $sql = 'SELECT
        COUNT(name) AS total,
        person as nick
    FROM
        wip
    WHERE
        lang="' . $lang . '"
    GROUP BY
        nick
    ORDER BY
        nick';
    $result = $idx->query($sql);
    $tmp = array();
    while ($r = $result->fetchArray()) {
        $tmp[$r['nick']] = $r['total'];
    }
    return $tmp;
}

function translator_get_old($idx, $lang)
{
    $sql = 'SELECT
        COUNT(a.name) AS total,
        a.maintainer as maintainer
    FROM
        files a
    LEFT JOIN
        files b
    ON
        a.name = b.name
    AND 
        a.dir = b.dir
    WHERE
        a.lang="' . $lang . '"
    AND
        b.lang="en"
    AND
        b.revision - a.revision < ' . ALERT_REV . '
    AND
        b.revision != a.revision
    AND
        b.size - a.size < ' . ALERT_SIZE . '
    AND
        (b.mdate - a.mdate) / 86400  < ' . ALERT_DATE . '
    AND
        a.size is not NULL
    GROUP BY
        a.maintainer';

    $result = $idx->query($sql);
    $tmp = array();
    while ($r = $result->fetchArray()) {
        $tmp[$r['maintainer']] = $r['total'];
    }
    return $tmp;
}


function translator_get_critical($idx, $lang)
{
    $sql = 'SELECT
        COUNT(a.name) AS total,
        a.maintainer as maintainer
    FROM
        files a
    LEFT JOIN
        files b
    ON
        a.name = b.name
    AND 
        a.dir = b.dir
    WHERE
        a.lang="' . $lang . '"
    AND
        b.lang="en"
    AND
        (
            b.revision - a.revision >= ' . ALERT_REV . '
    OR
        (
             b.revision != a.revision
         AND
             (
                     b.size - a.size >= ' . (1024 * ALERT_SIZE) . '
                  OR 
                     (b.mdate - a.mdate) / 86400 >= ' . ALERT_DATE . '
            )
             )
    )
    AND
        a.size is not NULL
    GROUP BY
        a.maintainer
    ORDER BY
        a.maintainer';
    $result = $idx->query($sql);
    $tmp = array();
    while ($r = $result->fetchArray()) {
        $tmp[$r['maintainer']] = $r['total'];
    }
    return $tmp;
}

function translator_get_uptodate($idx, $lang)
{
    $sql = 'SELECT
        COUNT(a.name) AS total,
        a.maintainer as maintainer
    FROM
        files a
    LEFT JOIN
        files b
    ON
        a.name = b.name
    AND 
        a.dir = b.dir
    WHERE
        a.lang="' . $lang . '"
    AND
        b.lang="en"
    AND 
        a.revision = b.revision
    GROUP BY
        a.maintainer
    ORDER BY
        a.maintainer';
    $result = $idx->query($sql);
    $tmp = array();
    while ($r = $result->fetchArray()) {
        $tmp[$r['maintainer']] = $r['total'];
    }
    return $tmp;
}


function get_translators($idx, $lang)
{
    $sql = 'SELECT
                nick,
                t.name as name,
                mail,
                svn
            FROM
                translators t
                WHERE lang="' . $lang . '"';
    $persons = array();
    $result = $idx->query($sql);
    while ($r = $result->fetchArray()) {
        $persons[$r['nick']] = array('name' => $r['name'], 'mail' => $r['mail'], 'svn' => $r['svn']);
    }
    return $persons;
}

function get_nb_LANG_files_Translated($idx, $lang)
{
    $sql = '
    SELECT
        b.lang as language,
        COUNT(*) as total
    FROM
        files a 
    LEFT JOIN  
        files b
    WHERE
        b.lang="' . $lang . '" 
    AND
        a.lang=\'en\' 
    AND
        b.name = a.name 
    AND
        b.dir = a.dir 
    AND
        a.revision = b.revision
    GROUP BY
        b.lang
  ';

    $result = $idx->query($sql);
    return $result->fetchArray();
}

// Return an array
function get_stats_uptodate($idx, $lang)
{
    $sql = 'SELECT
        COUNT(a.name) as total, 
        SUM(c.size) as size 
    FROM 
        files a 
    LEFT JOIN
        files c 
    ON
        c.name = a.name 
    AND
        c.dir = a.dir 
    WHERE 
        a.lang="' . $lang . '"
    AND
        c.lang="en"
    AND
        a.revision = c.revision'; 

    $result = $idx->query($sql);
    $r = $result->fetchArray();
    $result = array($r['total'], $r['size']);
    return $result;
}

function get_stats_critical($idx, $lang)
{
    $sql = 'SELECT
        COUNT(a.name) as total,
        sum(b.size) as size
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
        a.lang="' . $lang .'" 
    AND
        b.lang="en"
    AND
        (
            b.revision - a.revision >= ' . ALERT_REV . '
        OR
            (
                 b.revision != a.revision
             AND
                 (
                     (b.size - a.size) >= ' . ALERT_SIZE . '
                  OR
                     (b.mdate - a.mdate) / 86400 >= ' . ALERT_DATE . '
                )
             )
        )
    AND
        a.size is not NULL
    AND
        a.dir = d.id';

    $result = $idx->query($sql);

    $r = $result->fetchArray();
    $result = array($r['total'], $r['size']);
    return $result;
}

// Return an array
function get_stats_old($idx, $lang)
{
    $sql = 'SELECT
        COUNT(a.name) as total,
        sum(b.size) as size
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
        a.lang="' . $lang .'" 
    AND
        b.lang="en"
    AND
        (b.revision - a.revision) < ' . ALERT_REV . '
    AND
        b.revision != a.revision
    AND
        (b.size - a.size) < ' . ALERT_SIZE . '
    AND
        (b.mdate - a.mdate) / 86400  <= ' . ALERT_DATE . '
    AND
        a.size is not NULL 
    AND
        a.dir = d.id';

    $result = $idx->query($sql);

    $r = $result->fetchArray();
    $result = array($r['total'], $r['size']);
    return $result;
}


function get_stats_notrans($idx, $lang)
{
    $sql = 'SELECT
        COUNT(a.name) as total, 
        sum(b.size) as size 
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
        a.revision is NULL
    AND
        a.size is NULL 
    AND
        a.dir = d.id';

    $result = $idx->query($sql);

    $r = $result->fetchArray();

    return array($r['total'], $r['size']);
    if (sqlite_num_rows($result)) {
        $r = $result->fetchArray();
        return array($r['total'], $r['size']);
    } else {
        return array(0,0);
    }
}

function get_stats_wip($idx, $lang)
{
    $sql = 'SELECT
        COUNT(*) as total,
        0 as size
    FROM
        wip
    WHERE
        lang = "' . $lang . '"';

    $result = $idx->query($sql);
    $r = $result->fetchArray();
    return array($r['total'], $r['size']);
}


// Return an array
function get_stats_notag($idx, $lang)
{
    $sql = 'SELECT
        COUNT(a.name) as total,
        sum(b.size) as size
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
        a.lang="' . $lang .'" 
    AND
        b.lang="en"
    AND
        a.revision is NULL
    AND
        a.size is not NULL
    AND
        a.dir = d.id';

    $result = $idx->query($sql);

    $r = $result->fetchArray();
    $result = array($r['total'], $r['size']);
    return $result;
}

function gen_date($file)
{
    $unix = filemtime($file);
    return '<time class="gen-date" datetime="'.date(DATE_W3C, $unix).'">Generated: '.date('d M Y H:i:s', $unix).'</time>';
}
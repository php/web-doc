<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4:
+----------------------------------------------------------------------+
| PHP Documentation Tools Site Source Code                             |
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
| Authors:          Yannick Torres <yannick@php.net>                   |
|                   Mehdi Achour <didou@php.net>                       |
+----------------------------------------------------------------------+
*/

// A file is criticaly "outdated' if
define("ALERT_REV",   10); // translation is 10 or more revisions behind the en one
define("ALERT_SIZE",   3); // translation is  3 or more kB smaller than the en one
define("ALERT_DATE", -30); // translation is 30 or more days older than the en one

// Revision marks used to flag files
define("REV_UPTODATE", 1); // actual file
define("REV_NOREV",    2); // file with revision comment without revision
define("REV_CRITICAL", 3); // criticaly old / small / outdated
define("REV_OLD",      4); // outdated file
define("REV_NOTAG",    5); // file without revision comment
define("REV_NOTRANS",  6); // file without translation
define("REV_CREDIT",   7); // only used in translators list
define("REV_WIP",      8); // only used in translators list



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
    $res = sqlite_query($idx, $sql);
    if (sqlite_num_rows($res)) {
        $tmp = array();
        while ($r = sqlite_fetch_array($res, SQLITE_ASSOC)) {
            $tmp[$r['dir']] = $r['name'];
        }
        return $tmp;
    } else {
        return array();
    }
}

// return an array with the outdated files in $dir
function get_outdated_files($idx, $lang, $dir)
{
    $sql = 'SELECT
        a.status as status, 
        a.name as file, 
        a.maintainer as maintainer, 
        c.revision as en_rev, 
        c.size as en_size, 
        a.size as trans_size,
        a.revision as trans_rev,
        a.mdate as trans_date,
        c.mdate as en_date,
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
        a.dir = "' . $dir . '"
    AND
        a.revision != c.revision order by b.name';
    $result = sqlite_query($idx, $sql);
    $tmp = array();
    while ($r = sqlite_fetch_array($result, SQLITE_ASSOC)) {
        $tmp[] = array(
        'name' => $r['name'],
        'en_date' => $r['en_date'],
        'trans_date' => $r['trans_date'],
        'en_rev' => $r['en_rev'],
        'en_size' => $r['en_size'],
        'trans_size' => $r['trans_size'],
        'trans_rev' => $r['trans_rev'],
        'status' => $r['status'],
        'maintainer' => $r['maintainer'],
        'file' => $r['file']);
    }

    return $tmp;

}

// Return an array with the revchecked documentation types
function revcheck_available_types()
{
    $buff = '';
    $a = glob(SQLITE_DIR . 'rev.*.sqlite');
    $buff .= "The revision tracking is actually available for the following documentation types : <ul>";
    foreach($a as $b) {
        $b = str_replace(SQLITE_DIR . 'rev.', '', $b);
        $b = str_replace('.sqlite', '', $b);
        if (!file_exists('./images/icons/' . $b . '.png'))
            continue;
        $buff .= "<li><a href=\"/".$b."/revcheck.php\"><img src=\"/images/icons/".$b.".png\" alt=\"".$b."\" /></a></li>";
    }
    $buff .= "</ul>";
    return $buff;
}

// Return an array of available languages for a revchecked documentation $type
function revcheck_available_languages($idx)
{
    $tmp = array();

    if (!$idx) {
        return FALSE;
    }

    $sql = 'SELECT distinct lang FROM description';
    $result = @sqlite_query($sql, $idx);

    if (!$result) {
        return FALSE;
    }

    if ($result) {
        while ($r = sqlite_fetch_array($result, SQLITE_ASSOC)) {
            $tmp[] = $r['lang'];
        }
    }
    return $tmp;
}


// Return en integer
function get_nb_EN_files($idx)
{
    $sql = "select COUNT(*) as total FROM files WHERE lang = 'en'";
    $res = sqlite_query($idx, $sql);
    $row = sqlite_fetch_array($res, SQLITE_ASSOC);
    $files_EN = $row['total'];
    return $files_EN;
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
    $result = sqlite_query($idx, $sql);
    $num = sqlite_num_rows($result);
    if ($num == 0) {
        return false;
    } else {
        $tmp = array();
        while ($r = sqlite_fetch_array($result, SQLITE_ASSOC)) {
            $tmp[] = array('dir' => $r['dir'], 'size' => $r['size'], 'file' => $r['file']);
        }
        return $tmp;
    }
}

function get_description($idx, $lang)
{
    $sql = 'SELECT * FROM description WHERE lang = "' . $lang . '";';
    $result = sqlite_query($idx, $sql);
    $row = sqlite_fetch_array($result, SQLITE_ASSOC);
    return $row['intro'];
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

    $res = sqlite_query($idx, $sql);
    while ($row = sqlite_fetch_array($res, SQLITE_ASSOC)) {
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
    $result = sqlite_query($idx, $sql);
    $tmp = array();
    while ($r = sqlite_fetch_array($result, SQLITE_ASSOC)) {
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
        php("intval",(b.mdate - a.mdate) / 86400)  < ' . ALERT_DATE . '
    AND
        a.size is not NULL
    GROUP BY
        a.maintainer';

    $result = sqlite_query($idx, $sql);
    $tmp = array();
    while ($r = sqlite_fetch_array($result, SQLITE_ASSOC)) {
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
                     php("intval",(b.mdate - a.mdate) / 86400) >= ' . ALERT_DATE . '
         	)
             )
	)
    AND
        a.size is not NULL
    GROUP BY
        a.maintainer
    ORDER BY
        a.maintainer';
    $result = sqlite_query($idx, $sql);
    $tmp = array();
    while ($r = sqlite_fetch_array($result, SQLITE_ASSOC)) {
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
    $result = sqlite_query($idx, $sql);
    $tmp = array();
    while ($r = sqlite_fetch_array($result, SQLITE_ASSOC)) {
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
                cvs
            FROM
                translators t
                WHERE lang="' . $lang . '"';
    $persons = array();
    $result = sqlite_query($idx, $sql);
    while ($r = sqlite_fetch_array($result, SQLITE_ASSOC)) {
        $persons[$r['nick']] = array('name' => $r['name'], 'mail' => $r['mail'], 'cvs' => $r['cvs']);
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

    $res = sqlite_query($idx, $sql);
    $result = sqlite_fetch_array($res, SQLITE_ASSOC);

    return $result;
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

    $result = sqlite_query($idx, $sql);
    $r = sqlite_fetch_array($result, SQLITE_ASSOC);
    $result = array($r['total'], $r['size']);
    return $result;
}

// Return an array
function mtime($dir, $file, $lang)
{
    return intval((time() - @filemtime(ROOT_PATH . "/cvs/phpdoc-all/$lang$dir/$file")) / 86400);
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
                     php("intval",(b.mdate - a.mdate) / 86400) >= ' . ALERT_DATE . '
                )
             )
        )
    AND
        a.size is not NULL
    AND
        a.dir = d.id';

    sqlite_create_function($idx, 'get_mtime', 'mtime', 3);
    $result = sqlite_query($idx, $sql);

    $r = sqlite_fetch_array($result, SQLITE_ASSOC);
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
        php("intval",(b.mdate - a.mdate) / 86400)  <= ' . ALERT_DATE . '
    AND
        a.size is not NULL 
    AND
        a.dir = d.id';

    sqlite_create_function($idx, 'get_mtime', 'mtime', 3);

    $result = sqlite_query($idx, $sql);

    $r = sqlite_fetch_array($result, SQLITE_ASSOC);
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

    $result = sqlite_query($idx, $sql);
    if (sqlite_num_rows($result)) {
        $r = sqlite_fetch_array($result, SQLITE_ASSOC);
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

    $result = sqlite_query($idx, $sql);
    $r = sqlite_fetch_array($result, SQLITE_ASSOC);
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

    $result = sqlite_query($idx, $sql);

    $r = sqlite_fetch_array($result, SQLITE_ASSOC);
    $result = array($r['total'], $r['size']);
    return $result;
}

?>

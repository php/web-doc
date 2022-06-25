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
    $sql = "SELECT a.status, a.name AS file, a.maintainer, a.additions, a.deletions, c.revision AS en_rev, a.revision AS trans_rev, b.path AS dir
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
        'name' => $r['dir'],
        'additions' => $r['additions'],
        'deletions' => $r['deletions']);
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
        if (!$file) return;
        $raw = htmlspecialchars( $file, ENT_XML1, 'UTF-8' );
        $lines = explode ( "\n" , $raw );
        echo "<div style='font: .75rem monospace; overflow-wrap:break-word; line-height: 1.8; border: 1px solid #ccc; border-radius: 4px;'>";

        $codeStyles = 'flex-grow: 1; min-width: 0; white-space: pre-wrap; padding: 0 4px;';
        $lineNumberStyles = 'flex: 0 0 40px; text-align: right; user-select: none; padding: 0 4px;';

        // Base gray palette
        $addBg = 'background-color: #f0f0f0;';
        $addAccentBg = 'background-color: #d8d8d8;';
        $delBg = $addBg;
        $delAccentBg = $addAccentBg;
        $tagBg = $addBg;
        $tagAccentBg = $addAccentBg;

        // Override palette for colored diff
        if ($c == 'on') {
            $addBg = 'background-color: #e6ffec;';
            $addAccentBg = 'background-color: #ccffd8;';
            $delBg = 'background-color: #ffebe9;';
            $delAccentBg = 'background-color: #ffd7d5;';
            $tagBg = 'background-color: #eff0f6;';
            $tagAccentBg = 'background-color: #d4d8e7;';
        }

        echo "<div style='padding: 12px;'>$gitfile</div>";

        // Count how many lines to skip diff header
        $diffStartLine = substr_count($raw, "\n", 0, strpos($raw, " @@"));

        foreach (array_slice($lines, $diffStartLine) as $line) {
            $fc = substr($line, 0, 1);

            $code = substr($line, 1);
            if ($code === '') {
                $code = "<br>";
            }

            echo "<div style='display: flex;'>";

            if ($fc == "+") {
                echo "<div style='$lineNumberStyles $addAccentBg'></div>";
                echo "<div style='$lineNumberStyles $addAccentBg'>$newLineNumber</div>";
                echo "<div style='$addAccentBg flex: 0 0 20px; text-align: center; user-select: none;'>$fc</div>";
                echo "<div style='$codeStyles $addBg'>" . $code . "</div>\n";

                $newLineNumber++;
            } else if ($fc == "-") {
                echo "<div style='$lineNumberStyles $delAccentBg'>$oldLineNumber</div>";
                echo "<div style='$lineNumberStyles $delAccentBg'></div>";
                echo "<div style='$delAccentBg flex: 0 0 20px; text-align: center; user-select: none;'>$fc</div>";
                echo "<div style='$codeStyles $delBg'>" . $code . "</div>\n";

                $oldLineNumber++;
            } else if ($fc == "@") {
                preg_match('/-(\d+),\d+ \+(\d+)/', $line, $matches);
                $oldLineNumber = $matches[1];
                $newLineNumber = $matches[2];

                echo "<div style='$lineNumberStyles $tagAccentBg color: #57606a; padding-top: 8px; padding-bottom: 8px;'>...</div>";
                echo "<div style='$lineNumberStyles $tagAccentBg color: #57606a; padding-top: 8px; padding-bottom: 8px;'>...</div>";
                echo "<div style='flex: 0 0 20px; text-align: center; $tagAccentBg'> </div>";
                echo "<div style='$codeStyles $tagBg color: #57606a; padding: 8px;'>$line</div>\n";
            } else {
                echo "<div style='$lineNumberStyles color: gray;'>$oldLineNumber</div>";
                echo "<div style='$lineNumberStyles color: gray;'>$newLineNumber</div>";
                echo "<div style='flex: 0 0 20px; text-align: center; user-select: none;'></div>";
                echo "<div style='$codeStyles color: gray;'>" . $code . "</div>\n";

                $oldLineNumber++;
                $newLineNumber++;
            }

            echo '</div>';
       }
       echo "</div><p></p>";
   }
}

function gen_date($file)
{
    $unix = filemtime($file);
    return '<time class="gen-date" datetime="'.date(DATE_W3C, $unix).'">Generated: '.date('d M Y H:i:s', $unix).'</time>';
}

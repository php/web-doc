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

$TRANSLATION_STATUSES = [
    'TranslatedOk'  => 'Up to date',
    'TranslatedOld' => 'Outdated',
    'TranslatedWip' => 'Work in progress',
    'RevTagProblem' => 'No revision tag',
    'NotInEnTree'   => 'Not in EN tree',
    'Untranslated'  => 'Available for translation',
];

function get_language_intro($idx, $lang) {
    $result = $idx->query("SELECT intro FROM languages WHERE lang = '$lang'");
    $answer = $result->fetchArray();
    return is_array($answer) ? $answer[0] : null;
}

// Return an array of directory containing outdated files
function get_dirs($idx, $lang) {
    $sql = <<<SQL
        SELECT
            path AS dir
        FROM
            files
        WHERE
            lang = '$lang'
        AND
            (status = 'TranslatedOld' OR status = 'TranslatedWip')
        ORDER BY
            path
    SQL;

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
    $value = SQLite3::escapeString($value ?? '');

    $sql_filter = match ($filter) {
        'dir' => "AND path = '{$value}'",
        'translator' => "AND maintainer = '{$value}'",
        default => ''
    };

    $sql = <<<SQL
        SELECT
            status,
            name AS file,
            path AS name,
            maintainer,
            adds AS additions,
            dels AS deletions,
            hashLast as en_rev,
            hashRvtg as trans_rev
        FROM
            files
        WHERE
            lang = '{$lang}'
        AND
            (status = 'TranslatedOld' OR status = 'TranslatedWip')
          {$sql_filter}
        ORDER BY
            path
    SQL;

    $result = $idx->query($sql);
    $tmp = array();
    while ($r = $result->fetchArray(SQLITE3_ASSOC)) {
        $tmp[] = $r;
    }

    return $tmp;
}

// Return an array of available languages for manual
function revcheck_available_languages($idx)
{
    $result = $idx->query('SELECT lang FROM languages');
    while ($row = $result->fetchArray(SQLITE3_NUM)) {
		$tmp[] = $row[0];
	}

	return $tmp;
}

function get_missfiles($idx, $lang)
{
    $sql = <<<SQL
        SELECT
            path AS dir,
            name AS file,
            hashLast AS revision,
            size / 1024 AS size
        FROM
            files
        WHERE
            lang = '{$lang}'
        AND
            status = 'Untranslated'
    SQL;

    $result = $idx->query($sql);

    while ($r = $result->fetchArray(SQLITE3_ASSOC)) {
        $tmp[] = $r;
    }

    return $tmp;
}

function get_oldfiles($idx, $lang)
{
    $sql = <<<SQL
        SELECT
            path AS dir,
            name AS file,
            size / 1024 AS size
        FROM
            files
        WHERE
            lang = '$lang'
        AND
            status = 'NotInEnTree'
    SQL;

    $result = $idx->query($sql);
    $tmp = array();

    while ($r = $result->fetchArray(SQLITE3_ASSOC)) {
        $tmp[] = $r;
    }
    return $tmp;
}

function get_misstags($idx, $lang)
{
    $sql = <<<SQL
        SELECT path AS dir, name AS name
          FROM files
         WHERE lang = '{$lang}' AND status = 'RevTagProblem'
         ORDER BY dir, name
    SQL;
    $tmp = NULL;
    $result = $idx->query($sql);
    while($row = $result->fetchArray()) {
        $tmp[] = $row;
    }

    return $tmp;
}

function get_translators($idx, $lang)
{
    $sql = <<<SQL
        SELECT
            nick, name, email AS mail, vcs AS karma,
            countOk, countOld, countOther
        FROM
            translators
        WHERE
            lang = '{$lang}'
        ORDER BY
            nick COLLATE NOCASE
    SQL;

    $result = $idx->query($sql);
    while ($r = $result->fetchArray(SQLITE3_ASSOC)) {
        $persons[$r['nick']] = $r;
    }
    return $persons;
}

/*
 * Returns statistics for specified language
 */
function get_lang_stats($idx, $lang) {
    $sql = <<<SQL
        SELECT
            status,
            COUNT(*) AS total,
            SUM(size) / 1024 AS size
        FROM
            files
        WHERE
            lang = '{$lang}'
        GROUP BY
            status
    SQL;

    $result = $idx->query($sql);

    $stats = [];
    $total = [ 'total' => 0, 'size' => 0 ];

    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $stats[$row['status']] = $row;
        if ($row['status'] != 'NotInEnTree') {
            $total['total'] += $row['total'];
            $total['size'] += $row['size'];
        }
    }

    if ($total['total'] > 0) {
        $stats['total'] = $total;
    }

    return $stats;
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
        $safedir = 'safe.directory=' . GIT_DIR . 'en';
        chdir( GIT_DIR . 'en' );
        $arg_h = escapeshellarg($h);
        $arg_f = escapeshellarg($gitfile);
        $file = `git -c {$safedir} diff --ignore-space-at-eol {$arg_h} -- {$arg_f}`;
        if ($file == null)
            $file = `git -c {$safedir} diff {$arg_h} -- {$arg_f}`;
        $hash = `git -c {$safedir} log -n 1 --pretty=format:%H -- {$arg_f}`;
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

        echo "<div style='padding: 12px;'>$gitfile<br/>$hash</div>";

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
                echo "<div style='$codeStyles $addBg'>" .  $code . "</div>\n";

                $newLineNumber++;
            } else if ($fc == "-") {
                echo "<div style='$lineNumberStyles $delAccentBg'>$oldLineNumber</div>";
                echo "<div style='$lineNumberStyles $delAccentBg'></div>";
                echo "<div style='$delAccentBg flex: 0 0 20px; text-align: center; user-select: none;'>$fc</div>";
                echo "<div style='$codeStyles $delBg'>" .  $code . "</div>\n";

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
                echo "<div style='$codeStyles color: gray;'>" .  $code . "</div>\n";

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

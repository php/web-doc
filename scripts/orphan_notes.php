<?php
/**
 * +----------------------------------------------------------------------+
 * | PHP Documentation Site Source Code                                   |
 * +----------------------------------------------------------------------+
 * | Copyright (c) 1997-2009 The PHP Group                                |
 * +----------------------------------------------------------------------+
 * | This source file is subject to version 3.0 of the PHP license,       |
 * | that is bundled with this package in the file LICENSE, and is        |
 * | available at through the world-wide-web at                           |
 * | http://www.php.net/license/3_0.txt.                                  |
 * | If you did not receive a copy of the PHP license and are unable to   |
 * | obtain it through the world-wide-web, please send a note to          |
 * | license@php.net so we can mail you a copy immediately.               |
 * +----------------------------------------------------------------------+
 * | Authors: Nuno Lopes <nlopess@php.net>                                |
 * +----------------------------------------------------------------------+
 *
 * $Id$
 */

/*
 * This script searches for orphan notes in the phpdoc manual
 * You need a rsync'ed phpweb dir
 */


$inCli = true;
include '../include/init.inc.php';

$manual_dir = CVS_DIR .'/phpweb/manual/en';
$notes_dir  = CVS_DIR .'/phpweb/backend/notes';


/* Collect manual IDs */
function recurse_manual($dir) {
    global $files, $len;

    if (!$dh = opendir($dir)) {
        exit;
    }

    while (($file = readdir($dh)) !== false) {

        if($file == '.' || $file == '..') {
            continue;
        }

        $path = $dir.'/'.$file;

        if(is_dir($path)) {
            recurse_manual($path);
        } else {
            $files[substr(md5(substr($path, $len, -4)), 0, 16)] = 1;
        }
    }

    closedir($dh);
}


/* Search for bogus notes IDs */
function recurse_notes($dir) {
    global $array, $files, $n_files, $n_notes;

    if (!$dh = opendir($dir)) {
        exit;
    }

    while (($file = readdir($dh)) !== false) {

        if($file == '.' || $file == '..' || substr($file, -4) == '.bz2' ||
           $file == 'last-updated' || $file == 'sections') {
            continue;
        }

        $path = $dir.'/'.$file;

        if(is_dir($path)) {
            recurse_notes($path);
        } else {
            if(isset($files[$file])) {
                continue;
            }

            $fp = fopen($path, 'r');

            while (!feof($fp)) {
                $line = chop(fgets($fp, 12288));
                if ($line == '') { continue; }

                list($id, $sect) = explode('|', $line);
                $array[$sect][] = $id;

                ++$n_notes;
            } // file orphan

            ++$n_files;
        } // file
    } // main while

    closedir($dh);
}


/* output HTML */
function output_html() {
    global $array, $n_notes, $n_files;

    echo "<?php include_once '../include/init.inc.php'; echo site_header('docweb.common.header.orphan-notes'); ?><p>&nbsp;</p>";

    if(count($array) == 0) {
        echo '<p>Currently, there are no orphan notes!</p>';
        echo '<p>Last Check: ' . date('r') . '</p>';
        echo '<?php echo site_footer(); ?>';
        return;
    }

    echo '<table class="Tc"><tr class="blue"><th>Old ID</th>'.
         '<th>Notes IDs</th><th>Move to new ID:</th></tr>';

    foreach($array as $id => $notes) {
        echo '<tr class="old"><td>'.$id.'</td><td>' .
             preg_replace('/(\d+)/', '<a href="https://master.php.net/manage/user-notes.php?keyword=$1">$1</a>', implode(', ', $notes)) .
             '</td><td><form action="https://master.php.net/manage/user-notes.php?action=mass" method="post">'.
             '<input type="hidden" name="step" value="1" /><input type="hidden" name="old_sect" value="' . $id . '" />'.
             '<input type="text" name="new_sect" value="" size="30" maxlength="80" /><input type="submit" value="&gt;" /></form></td></tr>';

    }

    echo "</table><p>&nbsp;</p><p><b>Total Notes</b>: $n_notes<br/><b>Total files</b>: $n_files</p>".
         '<p>Last Check: ' . date('r') . '</p><?php echo site_footer(); ?>';
}


/* begin main program */
$len = strlen("$manual_dir/");
$n_notes = $n_files = 0;

recurse_manual($manual_dir);
recurse_notes($notes_dir);

output_html();

?>

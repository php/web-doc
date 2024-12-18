<?php
error_reporting(E_ALL ^ E_NOTICE);

require_once __DIR__ . '/../include/init.inc.php';
require_once __DIR__ . '/../include/lib_revcheck.inc.php';

if (isset($_GET['lang']) && in_array($_GET['lang'], array_keys($LANGUAGES))) {
    $lang = $_GET['lang'];
}
else {
    $lang = 'en';
}

if ($lang != 'en' ) $lang_name = $LANGUAGES[$lang];

$tool = '';

if (isset($_GET['p'])) {
    $tool = $_GET['p'];
}

// Prevent viewing other tools in EN
if ($lang == 'en') {
    $tool = 'default';
}

if (!defined('SQLITE_DIR')) {
    site_header();
    echo "<p>Unable to find SQLite database with revisions.</p>";
    site_footer();
    die;
}

$DBLANG = SQLITE_DIR . 'status.sqlite';

$dbhandle = new SQLite3($DBLANG);
if (!$dbhandle) {
    site_header();
    echo "<p>Database connection couldn't be established</p>";
    site_footer();
    die;
}

// Check if db connection can be established and if revcheck for requested lang exists
$lang_intro = get_language_intro($dbhandle, $lang);

if ($lang !== 'en' && is_null($lang_intro)) {
    site_header();
    echo "<p>This revision check doesn't exist yet.</p>";
    site_footer();
    die;
}

site_header();
switch($tool) {
 case 'translators':
    $translators = get_translators($dbhandle, $lang);

    if (empty($translators)) {
        echo '<p>Error: no translators info found in database.</p>';
    }
    else {
        foreach($translators as $nick =>$data) {
            $files_w[$nick] = array('uptodate' => '', 'outdated' => '', 'norev' => '', 'wip' => '');
            $files_w[$nick]['uptodate'] = $data['countOk'];
            $files_w[$nick]['wip'] = $data['countOther'];
            $files_w[$nick]['outdated'] = $data['countOld'];
        }

        echo <<<TRANSLATORS_HEAD
<table class="c">
<tr>
<th rowspan="2">Name</th>
<th rowspan="2">Nick</th>
<th rowspan="2">Karma</th>
<th colspan="7">Files maintained</th>
</tr>
<tr>
<th>upto-<br>date</th>
<th>out-<br>dated</th>
<th>wip</th>
<th>sum</th>
</tr>
TRANSLATORS_HEAD;

        foreach ($translators as $nick => $data) {
            echo '<tr>',
            '<td><a href="mailto:'.$data['mail'].'">'.$data['name'].'</a></td>',
            '<td><a href="/revcheck.php?p=files&amp;user='.$nick.'&amp;lang='.$lang.'">'.$nick.'</a></td>',
            '<td>'.(($data['karma'] == 'yes') ? '✓' : '&nbsp;').'</td>',
            '<td>' , @$files_w[$nick]['uptodate'], '</td>',
            '<td>' , $files_w[$nick]['outdated'], '</td>',
            '<td>', $files_w[$nick]['wip'], '</td>',
            '<th>' , @array_sum($files_w[$nick]), '</th>',
            '</tr>';
        }
        echo '</table>';
     }
 echo gen_date($DBLANG);
 break;

 case 'missfiles':
     $missfiles = get_missfiles($dbhandle, $lang);
     if (!$missfiles) {
         echo '<p>All files translated? Would be nice... but it\'s probably an error :(</p>';
     } else {
         $num = count($missfiles);
         $last_dir = false;
         $first_dir = false;
         echo '<p>Choose a directory:</p>';
         echo '<form method="get" action="revcheck.php"><p><select name="dir">';
         foreach ($missfiles as $miss) {
             if (isset($_GET['dir']) && $_GET['dir'] == $miss['dir']) {
                 $selected = ' selected="selected"';
             } else {
                 $selected = '';
             }
             if (!$last_dir || $last_dir != $miss['dir']) {
                 echo '<option value="'.$miss['dir'].'"'.$selected.'>'.$miss['dir'].'</option>';
                 $last_dir = $miss['dir'];
                 if (!$first_dir) $first_dir = $last_dir;
             }
         }
         echo '</select>';
         echo '<input type="hidden" name="p" value="missfiles">';
         echo '<input type="hidden" name="lang" value="'.$lang.'">';
         echo '<input type="submit" value="See untranslated files"></p></form>';

         echo '<table class="c">';
         echo '<tr><th rowspan="1">Available for translation</th><th>Commit Hash</th><th colspan="1">kB</th></tr>';

         $last_dir = false;
         $total_size = 0;
         $dir = isset($_GET['dir']) ? $_GET['dir'] : $first_dir;
         foreach ($missfiles as $miss) {
             if ($dir == $miss['dir']) {
                 if (!$last_dir || $last_dir != $miss['dir']) {
                     echo '<tr><th colspan="3">'.$miss['dir'].'</th></tr>';
                     $last_dir = $miss['dir'];
                 }
                 $key = $miss['dir'] == '' ? "/" : $miss['dir']."/". $miss['file'];
                 echo "<tr><td><a href='https://github.com/php/doc-en/blob/{$miss['revision']}/$key'>{$miss['file']}</a></td><td>{$miss['revision']}</td><td>{$miss['size']}</td></tr>";
                 $total_size += $miss['size'];
                 // flush every 200 kbytes
                 if (($total_size % 200) == 0)
                     flush();
             }
         }
         echo "<tr><th colspan='3'>Total Size: $total_size kB</th></tr>";
         echo '</table>';
     }
 echo gen_date($DBLANG);
 break;

 case 'oldfiles':
     $oldfiles = get_oldfiles($dbhandle, $lang);
     if (!$oldfiles) {
         echo '<p>Good, it seems that this translation doesn\'t contain any file which is not present in English tree.</p>';
     } else {
         $num = count($oldfiles);
         echo '<table class="c">';
         echo '<tr><th rowspan="1">Not in EN tree ('.$num.' files):</th><th colspan="1">kB</th></tr>';

         $last_dir = false;
         $total_size = 0;
         foreach ($oldfiles as $old) {
         if (!$last_dir || $last_dir != $old['dir']) {
         echo '<tr><th colspan="2">'.$old['dir'].'</th></tr>';
         $last_dir = $old['dir'];
     }
     echo '<tr><td>', $old['file'], '</td><td>'.$old['size'].'</td></tr>';
     $total_size += $old['size'];
     // flush every 200 kbytes
     if (($total_size % 200) == 0)
         flush();
 }
 echo '<tr><th colspan="2">Total Size ('.$num.' files): '.$total_size.' kB</th></tr>';
 echo '</table>';
 }
 echo gen_date($DBLANG);
 break;

 case 'misstags':
     $misstags = get_misstags($dbhandle, $lang);

     if ($misstags == NULL) {
         echo '<p>Good, all files contain revision numbers.</p>';
     } else {
         $num = count($misstags);
         echo '<table class="c">';
         echo '<tr><th>Files without EN-Revision number ('.$num.' files):</th></tr>';

         $last_dir = false;

         foreach ($misstags as $row) {
             if (!$last_dir || $last_dir != $row['dir']) {
             echo '<tr><th>'.$row['dir'].'</th></tr>';
             $last_dir = $row['dir'];
          }
          echo '<tr><td>'.$row['name'].'</td></tr>';
     }
     echo '</table>';
 }
 echo gen_date($DBLANG);
 break;

 case 'filesummary':
     $stats = get_lang_stats($dbhandle, $lang);

     echo '<table class="c">';
     echo '<tr><th>File status type</th><th>Number of files</th><th>Percent of files</th><th>Size of files (kB)</th><th>Percent of size</th></tr>';

     foreach ($TRANSLATION_STATUSES as $status => $description) {
         echo
            '<tr>',
            '<td>', $description, '</td>',
            '<td>', $stats[$status]['total'] ?? 0, '</td>',
            '<td>',
            sprintf('%.2f%%', 100 * (($stats[$status]['total'] ?? 0) / $stats['total']['total'])),
            '</td>',
            '<td>', $stats[$status]['size'] ?? 0, '</td>',
            '<td>',
            sprintf('%.2f%%', 100 * (($stats[$status]['size'] ?? 0) / $stats['total']['size'])),
            '</td>',
            '</tr>';
     }
     echo
        '<tr>',
        '<th>Total</th>',
        '<th>', $stats['total']['total'] ?? 0, '</th>',
        '<th>100.00%</th>',
        '<th>', $stats['total']['size'] ?? 0, '</th>',
        '<th>100.00%</th>',
        '</tr>';
     echo '</table>';

     echo gen_date($DBLANG);
 break;


 case 'files' :
     // we need a dir to browse
     $dirs = get_dirs($dbhandle, $lang);
     $users = get_translators($dbhandle, $lang);
     echo '<p>This tool allows you to check which files in your translation need updates. To show the list ';
     echo 'choose a directory (it doesn\'t work recursively) or translator.</p>';
     echo '<p>When you click on the filename you will see the plaintext diff showing changes between revisions, so ';
     echo 'you will know what has changed in the English version and which information you need to update.';
     echo 'You can also click on [diff] to show the colored diff.</p>';
     echo '<p>Choose a directory:</p>';
     echo '<form method="get" action="revcheck.php"><p><select name="dir">';
     foreach ($dirs as $id => $name) {
         if (isset($_GET['dir']) && $_GET['dir'] == $id) {
         $selected = ' selected="selected"';
         } else {
             $selected = '';
         }
         echo '<option value="'.$id.'"'.$selected.'>'.$name.'</option>';
     }
     echo '</select>';
     echo '<input type="hidden" name="p" value="files">';
     echo '<input type="hidden" name="lang" value="'.$lang.'">';
     echo '<input type="submit" value="See outdated files"></p></form>';

     echo '<p>Or choose a translator:</p>';
     echo '<form method="get" action="revcheck.php"><p><select name="user">';
     foreach ($users as $id => $user) {
         if (isset($_GET['user']) && $_GET['user'] == $id) {
         $selected = ' selected="selected"';
         } else {
             $selected = '';
         }
         echo '<option value="'.$id.'"'.$selected.'>'.$id.'</option>';
     }
     echo '</select>';
     echo '<input type="hidden" name="p" value="files">';
     echo '<input type="hidden" name="lang" value="'.$lang.'">';
     echo '<input type="submit" value="See outdated files"></p></form>';

     // Get outdated files filtered as requested
     if (isset($_GET['user'])) {
         $outdated = get_outdated_files($dbhandle, $lang, 'translator', $_GET['user']);
     } elseif (isset($_GET['dir'])) {
         $outdated = get_outdated_files($dbhandle, $lang, 'dir', $_GET['dir']);
     } else {
         $outdated = get_outdated_files($dbhandle, $lang, 'all');
     }

     if (empty($outdated)) {
         echo '<p>Good, it seems that all files are up to date for these conditions.</p>';
     } else {
        echo <<<END_OF_MULTILINE
<table>
<tr>
<th rowspan="2">Translated file</th>
<th rowspan="2">Changes</th>
<th colspan="2">Revision</th>
<th rowspan="2">Maintainer</th>
<th rowspan="2">Status</th>
</tr>
<tr>
<th>en</th>
<th>$lang</th>
</tr>
<tr><th colspan="6">{$outdated[0]['name']}</th></tr>
END_OF_MULTILINE;
        $last_dir = false;
        $prev_name = $outdated[0]['name'];

        foreach ($outdated as $r) {
            if ($r['name'] != $prev_name) {
            echo '<tr><th colspan="6">'.$r['name'].'</th></tr>';
            $prev_name = $r['name'];
        }

        // Make the maintainer a link, if we have that maintainer in the list
        if ($r['maintainer'] && $r["maintainer"] != 'nobody') {
            $r["maintainer"] = '<a href="?p=translators&amp;lang=' . $lang . '">' . $r["maintainer"] . '</a>';
        }

        // Make a link to the GIT repository's diff script
        $key = $r['name'] . '/' . $r['file'];
        if ($r['name'] == '/')
            $key = $r['file'];
        //plaintext -color
        $d1 = "?p=plain&amp;lang={$lang}&amp;hbp={$r['trans_rev']}&amp;f=$key&amp;c=on";
        //plaintext
        $d2 = "?p=plain&amp;lang={$lang}&amp;hbp={$r['trans_rev']}&amp;f=$key&amp;c=off";

        $h1 = '<button class="btn copy" data-clipboard-text="';
        $h1 .= $r['en_rev'] . '">Copy</button> ';
        $h1 .= "<a href='https://github.com/php/doc-en/blob/{$r['en_rev']}/$key'>{$r['en_rev']}</a>";

        $h2 = "<a href='https://github.com/php/doc-en/blob/{$r['trans_rev']}/$key'>{$r['trans_rev']}</a>";

        $nm = "<a href='$d2'>{$r['file']}</a> <a href='$d1'>[diff]</a>";

        if ($r['additions'] < 0)
            $ch = "<span style='color: firebrick;'>no data</span>";
        else
            $ch = "<span style='color: darkgreen;'>+{$r['additions']}</span> <span style='color: firebrick;'>-{$r['deletions']}</span>";

        // Write out the line for the current file (get file name shorter)

        echo <<<END_OF_MULTILINE
<tr>
<td class='n'>{$nm}</td>
<td class='c'>{$ch}</td>
<td class='n o6'>{$h1}</td>
<td class='n o3'>{$h2}</td>
<td class='c'>{$r['maintainer']}</td>
<td class='c'>{$r['status']}</td>
</tr>
END_OF_MULTILINE;
     }
     echo '</table>';
 }
 echo gen_date($DBLANG);
 break;

 case 'plain':
    showdiff();
    echo gen_date($DBLANG);
 break;

 case 'graph':
 default:
     if ($lang == 'en') {
         echo '<img src="img-status-all.php" width="662" height="262" alt="Info" class="chart">';
         echo '<p>This is all what we can show for original manual. To get more tools, please select translation language.</p>';
         echo gen_date($DBLANG);
         $sidebar = nav_languages();
         site_footer($sidebar);
     } else {
         echo '<h2>Intro for language</h2>';
         echo '<p>'.$lang_intro.'</p>';
         echo '<img src="img-status-lang.php?lang=', $lang, '" width="680" height="300" alt="info">';
         echo gen_date($DBLANG);
         echo '<p>Links to available tools are placed on the right sidebar.</p>';
     }
 break;
}

if ($lang != 'en') {
    $sidebar = nav_languages($lang);
    site_footer($sidebar);
}

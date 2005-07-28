<?php
/* $Id$ */

include '../include/init.inc.php';
include '../include/lib_revcheck.inc.php';

$LANG = LANGC;

if (SITE == 'www') {
    echo site_header('docweb.common.header.revcheck');
    echo revcheck_available_types();
    echo site_footer();
    die;
} else {
    $DBLANG = SQLITE_DIR . 'rev.' . SITE . '.sqlite';
    if (LANGC == 'en' ||LANGC == 'all') {
        if ($dbhandle = @sqlite_open($DBLANG)) {
            $type = SITE;
            $langs = revcheck_available_languages($dbhandle);
            if(!$langs) {
                echo site_header('docweb.common.header.revcheck');
              ?>
                <div class="ColoredDiv">
                <h2 class="c">Status of the translated <?php echo strtoupper(SITE); ?> Manual</h2>
                <p class="c" style="font-size:12px;"><span class="Flag">
                <?php 
                if (LANGC != 'all') {
                    echo '<img src="/images/flags/' . LANGC .'.png" alt="'. LANGD . '" title="' . LANGD . '" />';
                } ?></span> Language: <?php echo LANGD; ?> : This documentation don't exist yet.<br />
                </p>
              </div>
              <?php
              echo site_footer();
              die();
            } else {
                echo site_header('docweb.common.header.revcheck');
                echo "
                <p class=\"c\">
                <img src=\"/images/revcheck/info_revcheck_" . SITE . "_all_lang.png\" alt=\"Info\" />
                </p>";
                echo site_footer();
                die();
            }
        } else {
            echo site_header('docweb.common.header.revcheck');
            echo "Couldn't connect to the revcheck database";
            echo site_footer();
            die();
        }
    }
}
$sql = "SELECT charset FROM description WHERE lang='" . LANGC . "';";
if (!($dbhandle = @sqlite_open($DBLANG)) || !($res = @sqlite_query($dbhandle, $sql)) || !sqlite_num_rows($res))
{
    // if there's no description, there's no documentation
    echo site_header('docweb.common.header.revcheck');
?>
  <div class="ColoredDiv">
  <h2 class="c">Status of the translated <?php echo strtoupper(SITE); ?> Manual</h2>
  <p class="c" style="font-size:12px;"><span class="Flag">
  <?php 
  if (LANGC != 'all') {
      echo '<img src="/images/flags/' . LANGC .'.png" alt="'. LANGD . '" title="' . LANGD . '" />';
  } ?></span> Language: <?php echo LANGD; ?><br />
  </p>
  </div>
  <blockquote>
  This revision check don't exist yet.
  </blockquote>
<?php
echo site_footer();
die;
}
$charset = sqlite_fetch_single($res);
define('ENCODING', $charset);
sqlite_close($dbhandle);
echo site_header('docweb.common.header.revcheck');
if (isset($_GET['p'])) {
    $PART = $_GET['p'];
} else {
    $PART = 'start';
}


// Colors used to mark files by status (colors for the above types)
$CSS = array(
REV_UPTODATE => "act",
REV_NOREV    => "norev",
REV_CRITICAL => "crit",
REV_OLD      => "old",
REV_NOTAG    => "wip",
REV_NOTRANS  => "wip",
REV_CREDIT   => "wip",
REV_WIP      => "wip",
);


$file_types = array(
array (REV_UPTODATE, "Up to date files"),
array (REV_OLD,      "Old files"),
array (REV_CRITICAL, "Critical files"),
array (REV_WIP,      "Work in progress"),
array (REV_NOREV,    "Files without revision number"),
array (REV_NOTAG,    "Files without revision tag"),
array (REV_NOTRANS,  "Files available for translation")
);

?>

<div class="ColoredDiv">
  <h2 class="c">Status of the translated <?php echo strtoupper(SITE); ?> Manual</h2>
  <p class="c" style="font-size:12px;">
   Generated: <?php echo date("r", filemtime($DBLANG)); ?> &nbsp; / <span class="Flag"><img src="/images/flags/<?php echo LANGC; ?>.png" alt="<?php echo LANGD; ?>" title="<?php echo LANGD; ?>" /></span> Language: <?php echo LANGD; ?><br />
  </p>
</div>
 <ul>
  <li<?php echo ($PART == 'intro' ) ? ' class="liSelected"' : ''; ?>><a href="<?php echo BASE_URL . '/revcheck.php?p=intro">Introduction</a>'; ?></li>
  <li<?php echo ($PART == 'translators' ) ? ' class="liSelected"' : ''; ?>><a href="<?php echo BASE_URL . '/revcheck.php?p=translators">Translators</a>'; ?></li>
  <li<?php echo ($PART == 'filesummary' ) ? ' class="liSelected"' : ''; ?>><a href="<?php echo BASE_URL . '/revcheck.php?p=filesummary">File summary by type</a>'; ?></li>
  <li<?php echo ($PART == 'files' ) ? ' class="liSelected"' : ''; ?>><a href="<?php echo BASE_URL . '/revcheck.php?p=files">Files</a>'; ?></li>
  <li<?php echo ($PART == 'misstags' ) ? ' class="liSelected"' : ''; ?>><a href="<?php echo BASE_URL . '/revcheck.php?p=misstags">Missing revision numbers</a>'; ?></li>
  <li<?php echo ($PART == 'missfiles' ) ? ' class="liSelected"' : ''; ?>><a href="<?php echo BASE_URL . '/revcheck.php?p=missfiles">Untranslated files</a>'; ?></li>
  <li<?php echo ($PART == 'oldfiles' ) ? ' class="liSelected"' : ''; ?>><a href="<?php echo BASE_URL . '/revcheck.php?p=oldfiles">Not in EN tree</a>'; ?></li>
  <li<?php echo ($PART == 'graph' ) ? ' class="liSelected"' : ''; ?>><a href="<?php echo BASE_URL . '/revcheck.php?p=graph">Graph</a>'; ?></li>
 </ul>
<?php

if ($dbhandle = sqlite_open($DBLANG)) {
    switch($PART) {
        case 'translators' :

        $translators = get_translators($dbhandle, $LANG);

        $uptodate = translator_get_uptodate($dbhandle, $LANG);
        $old      = translator_get_old($dbhandle, $LANG);
        $critical = translator_get_critical($dbhandle, $LANG);
        $wip      = translator_get_wip($dbhandle, $LANG);

        foreach($translators as $nick =>$data) {
            $files_w[$nick] = array('uptodate' => '', 'old' =>'', 'critical' => '', 'norev' => '', 'wip' => '');
            $files_w[$nick]['uptodate'] = isset($uptodate[$nick]) ? $uptodate[$nick] : '';
            $files_w[$nick]['wip'] = isset($wip[$nick]) ? $wip[$nick] : '';
            $files_w[$nick]['old'] = isset($old[$nick]) ? $old[$nick] : '';
            $files_w[$nick]['critical'] = isset($critical[$nick]) ? $critical[$nick] : '';
        }

        echo '<a name="translators"></a>';
        if (empty($translators)) {
            echo 'No translators info';
        } else {
            echo <<<TRANSLATORS_HEAD
<div style="text-align:center"> <!-- Compatibility with old IE -->
<table width="820" border="0" cellpadding="4" cellspacing="1" class="Tc">
<tr class="blue">
<th rowspan="2">Name</th>
<th rowspan="2">Contact email</th>
<th rowspan="2">Nick</th>
<th rowspan="2">CVS</th>
<th colspan="7">Files maintained</th>
</tr>
<tr>
<th class="wip" style="color:#000000">cre-<br />dits</th>
<th class="act" style="color:#000000">upto-<br />date</th>
<th class="old" style="color:#000000">old</th>
<th class="crit" style="color:#000000">cri-<br />tical</th>
<th class="norev" style="color:#000000">no<br />rev</th>
<th class="wip" style="color:#000000">wip</th>
<th class="blue">sum</th>
</tr>
TRANSLATORS_HEAD;
            $class = 'old';

            foreach ($translators as $nick => $data) {
                if ($data['cvs'] == 'yes') {
                    $cvsu = 'x';
                    $col = 'old';
                } else {
                    $cvsu = '&nbsp;';
                    $col = 'wip';
                }
                echo '<tr class="', $col, '">' . "\n";
                echo '<td><a name="maint-'.$nick.'">'. $data['name'] . '</a></td>', "\n";
                echo '<td>', $data['mail'], '</td>', "\n";
                echo '<td>', $nick, '</td>', "\n";
                echo '<td class="c">', $cvsu, '</td>', "\n";
                echo '<td class="c">&nbsp;</td>' ,
                '<td class="c">' , @$files_w[$nick]['uptodate'], "</td>" ,
                '<td class="c">' , @$files_w[$nick]['old'] , "</td>" ,
                '<td class="c">' , $files_w[$nick]['critical'], "</td>" ,
                '<td class="c">&nbsp;</td>' ,
                '<td class="c">', $files_w[$nick]['wip'], '</td>' ,
                '<th class="blue">' , @array_sum($files_w[$nick]), "</th>" , "\n";
                echo '</tr>', "\n";
            }
            echo '</table>'."\n</div>";
        }
        break;

        case 'missfiles' :
        $missfiles = get_missfiles($dbhandle, $LANG);
        if (!$missfiles) {
            echo 'No missfile info';
        } else {
            $num = count($missfiles);
            echo <<<MISSTAGS_HEAD
<p>&nbsp;</p>
<div style="text-align:center"> <!-- Compatibility with old IE -->
<table width="400" border="0" cellpadding="3" cellspacing="1" class="Tc">
        <tr class="blue">
                <th rowspan="1">Available for translation ($num files):</th>
                <th colspan="1">kB</th>
        </tr>

MISSTAGS_HEAD;

            $last_dir = false;
            $total_size = 0;
            foreach ($missfiles as $miss) {
                if (!$last_dir || $last_dir != $miss['dir']) {
                    echo '<tr class="blue"><th colspan="2">' . $miss['dir'] . '</th></tr>';
                    $last_dir = $miss['dir'];
                }
                echo '<tr class="wip"><td>', $miss['file'], '</td><td class="r">', $miss['size'], '</td></tr>';
                $total_size += $miss['size'];
                // flush every 200 kbytes
                if (($total_size % 200) == 0) {
                    flush();
                }
            }
            echo '<tr class="blue">
                      <th colspan="2">Total Size ('.$num.' files): '.$total_size.' kB</th>
                  </tr>';
            echo '</table></div>';
        }
        break;

        case 'oldfiles' :
        $oldfiles = get_oldfiles($dbhandle, $LANG);
        if (!$oldfiles) {
            echo 'No old file info';
        } else {
            $num = count($oldfiles);
            echo <<<OLDTAGS_HEAD
<p>&nbsp;</p>
<div style="text-align:center"> <!-- Compatibility with old IE -->
<table width="400" border="0" cellpadding="3" cellspacing="1" class="Tc">
        <tr class="blue">
                <th rowspan="1">Not in EN tree ($num files):</th>
                <th colspan="1">kB</th>
        </tr>

OLDTAGS_HEAD;

            $last_dir = false;
            $total_size = 0;
            foreach ($oldfiles as $old) {
                if (!$last_dir || $last_dir != $old['dir']) {
                    echo '<tr class="blue"><th colspan="2">' . $old['dir'] . '</th></tr>';
                    $last_dir = $old['dir'];
                }
                echo '<tr class="wip"><td>', $old['file'], '</td><td class="r">', $old['size'], '</td></tr>';
                $total_size += $old['size'];
                // flush every 200 kbytes
                if (($total_size % 200) == 0) {
                    flush();
                }
            }
            echo '<tr class="blue">
                      <th colspan="2">Total Size ('.$num.' files): '.$total_size.' kB</th>
                  </tr>';
            echo '</table></div>';
        }
        break;

        case 'misstags' :
        $sql = 'select
             d.name as dir, b.size as en_size, a.size as trans_size, a.name as name
             from files a, dirs d 
             left join files b on a.dir = b.dir and a.name = b.name 
             where a.lang="' . $LANG .'" and b.lang="en" and a.revision is NULL 
             and a.size is not NULL and a.dir = d.id';

        $result = sqlite_query($dbhandle, $sql);
        $num = sqlite_num_rows($result);
        if ($num == 0) {
            echo 'No misstags info';
        } else {
            echo <<<MISSTAGS_HEAD
<p>&nbsp;</p>
<div style="text-align:center"> <!-- Compatibility with old IE -->
<table width="400" border="0" cellpadding="3" cellspacing="1" class="Tc">
        <tr class="blue">
                <th rowspan="2">Files without Revision-comment ($num files):</th>
                <th colspan="3">Sizes in kB</th>
        </tr>
        <tr class="blue">
                <th>en</th>
                <th>$LANG</th>
                <th>diff</th>
        </tr>

MISSTAGS_HEAD;

            $last_dir = false;

            while($row = sqlite_fetch_array($result, SQLITE_ASSOC)) {
                if (!$last_dir || $last_dir != $row['dir']) {
                    echo '<tr class="blue"><th colspan="4">' . $row['dir'] . '</th></tr>';
                    $last_dir = $row['dir'];
                }
                echo '<tr class="wip"><td>', $row['name'], '</td><td class="r">', $row['en_size'], '</td><td class="r">', $row['trans_size'], '</td><td class="r">', (intval($row['en_size'] - $row['trans_size'])) ,'</td></tr>';
            }
            echo '</table></div>';
        }
        break;


        case 'filesummary' :

        $file_summary_array = array(
        REV_UPTODATE => array(0,0),
        REV_NOREV    => array(0,0),
        REV_CRITICAL => array(0,0),
        REV_OLD      => array(0,0),
        REV_NOTAG    => array(0,0),
        REV_NOTRANS  => array(0,0),
        REV_CREDIT   => array(0,0),
        REV_WIP      => array(0,0)
        );


        $file_summary_array[REV_WIP]      = get_stats_wip($dbhandle, $LANG);
        $file_summary_array[REV_CRITICAL] = get_stats_critical($dbhandle, $LANG);
        $file_summary_array[REV_UPTODATE] = get_stats_uptodate($dbhandle, LANGC);
        $file_summary_array[REV_OLD]      = get_stats_old($dbhandle, $LANG);
        $file_summary_array[REV_NOTAG]    = get_stats_notag($dbhandle, $LANG);
        $file_summary_array[REV_NOTRANS]  = get_stats_notrans($dbhandle, $LANG);

        echo <<<END_OF_MULTILINE
<div style="text-align:center"> <!-- Compatibility with old IE -->
<table width="450" border="0" cellpadding="4" cellspacing="1" class="Tc">
<tr class="blue">
<th>File status type</th>
<th>Number of files</th>
<th>Percent of files</th>
<th>Size of files (kB)</th>
<th>Percent of size</th>
</tr>
END_OF_MULTILINE;

        $percent = array(0, 0);

        foreach($file_summary_array as $t => $a) {
            $percent[0] += $a[0];
            $percent[1] += $a[1];
        }

        foreach ($file_types as $num => $type) {

            $tmp_num_percent_0 = ($percent[0] == 0) ? 0 : number_format($file_summary_array[$type[0]][0] * 100 / $percent[0], 2 );
            $tmp_num_percent_1 = ($percent[0] == 0) ? 0 : number_format($file_summary_array[$type[0]][1] * 100 / $percent[1], 2);


            echo "<tr class=\"".$CSS[$type[0]]."\">".
            "<td>".$type[1]."</td>".
            "<td class=\"c\">" . $file_summary_array[$type[0]][0] . "</td>".
            "<td class=\"c\">" . $tmp_num_percent_0 . "%</td>".
            "<td class=\"c\">".$file_summary_array[$type[0]][1]."</td>".
            "<td class=\"c\">" . $tmp_num_percent_1 . "%</td></tr>\n";
        }

        echo "<tr class=\"blue\"><th>Files total</th><th>$percent[0]</th><th>100%</th><th>" . $percent[1] . "</th><th>100%</th></tr>\n".
        "</table></div>\n<p>&nbsp;</p>\n";

        break;


        case 'files' :
        // we need a dir to browse
        $dirs = get_dirs($dbhandle, $LANG);
        if (empty($dirs)) {
            echo 'no files';
        } else {
            echo '<p>Choose a directory :</p>';
            echo '<form method="get" action="' . BASE_URL . '/revcheck.php?p=files"><p><select name="dir">';
            foreach ($dirs as $id => $name) {
                if (isset($_GET['dir']) && $_GET['dir'] == $id) {
                    $selected = ' selected="selected"';
                } else {
                    $selected = '';
                }
                echo '<option value="' . $id . '"' . $selected . '>' . $name . '</option>' . "\n";
            }
            echo '</select><input type="hidden" name="p" value="files" /><input type="submit" value="See outdated files" /></p></form>';
        }

        if (isset($_GET['dir'])) {
            $outdated = get_outdated_files($dbhandle, $LANG, $_GET['dir']);
            if (empty($outdated)) {
                echo 'No files info';
            } else {
                echo <<<END_OF_MULTILINE
<div style="text-align:center"> <!-- Compatibility with old IE -->
<table width="820" border="0" cellpadding="4" cellspacing="1" class="Tc">
  <tr class="blue">
    <th rowspan="2">Translated file</th>
    <th colspan="3">Revision</th>
    <th colspan="3">Size in kB</th>
    <th colspan="3">Age in days</th>
    <th rowspan="2">Maintainer</th>
    <th rowspan="2">Status</th>
  </tr>
  <tr class="blue">
    <th>en</th>
    <th>$LANG</th>
    <th>diff</th>
    <th>en</th>
    <th>$LANG</th>
    <th>diff</th>
    <th>en</th>
    <th>$LANG</th>
    <th>diff</th>
  </tr>
  <tr class="blue"><th colspan="12">{$outdated[0]['name']}</th></tr>

END_OF_MULTILINE;
                $last_dir = false;

                foreach ($outdated as $r) {
                    $r['en_date'] = intval((time() - $r['en_date']) / 86400);
                    $r['trans_date'] = intval((time() - $r['trans_date']) / 86400);
                    // Make decision on file category by revision, date and size
                    $rev_diff  = intval($r['en_rev']) - intval($r['trans_rev']);
                    $size_diff = intval($r['en_size']) - intval($r['trans_size']);
                    $date_diff = $r['en_date'] - $r['trans_date'];
                    if ($rev_diff >= ALERT_REV || $size_diff >= ALERT_SIZE || $date_diff <= ALERT_DATE) {
                        $status_mark = REV_CRITICAL;
                    } elseif ($rev_diff === "n/a") {
                        $status_mark = REV_NOREV;
                    } else {
                        $status_mark = REV_OLD;
                    }
                    // Make the maintainer a link, if we have that maintainer in the list
                    if ($r['maintainer'] && $r["maintainer"] != 'nobody') {
                        $r["maintainer"] = '<a href="?p=translators#maint-' . $r['maintainer'] . '">' . $r["maintainer"] . '</a>';
                    }

                    // If we have a 'numeric' revision diff and it is not zero,
                    // make a link to the CVS repository's diff script
                    $r["short_name"] = "<a href=\"http://cvs.php.net/diff.php/" .
                    $PROJECTS[$project][2] . $r['name'] .'/' . $r['file'] .
                    "?r1=1." . $r["trans_rev"] .
                    "&amp;r2=1." . $r["en_rev"] .
                    "&amp;ty=u\">" . $r["file"] . "</a>";

                    // Add a [NoWS] link
                    $r['short_name'] .= ' <a href="http://cvs.php.net/diff.php/' .
                    $PROJECTS[$project][2] . $r['name'] .'/' . $r['file'] .
                    '?r1=1.' . $r['trans_rev'] .
                    '&amp;r2=1.' . $r['en_rev'] .
                    '&amp;ty=u&amp;ws=0">[NoWS]</a>';


                    // Write out the line for the current file (get file name shorter)
                    echo "<tr class=\"{$CSS[$status_mark]}\">".
                    "<td>{$r['short_name']}</td>".
                    "<td>1.{$r['en_rev']}</td>" .
                    "<td>1.{$r['trans_rev']}</td>" .
                    "<td align=\"right\"><strong>" . $rev_diff . "</strong></td>" .
                    "<td> {$r['en_size']}</td>" .
                    "<td> {$r['trans_size']}</td>" .
                    "<td align=\"right\"><strong>" . $size_diff . "</strong></td>" .
                    "<td> {$r['en_date']}</td>" .
                    "<td> {$r['trans_date']}</td>" .
                    "<td align=\"right\"><strong>" . $date_diff . "</strong></td>" .
                    "<td> {$r['maintainer']}</td>" .
                    "<td> {$r['status']}</td></tr>\n";

                }
                echo '</table></div>';
}
}
break;

case "graph" :
if (is_readable("images/revcheck/info_revcheck_" . SITE . "_" . LANGC . ".png")) {
    echo "<p class=\"c\">
                <img src=\"/images/revcheck/info_revcheck_" . SITE . "_" . LANGC . ".png\" alt=\"Info\" />
                </p>";
} else {
    echo 'Can\'t find graph';
}
break;

default :
echo '<p>' . get_description($dbhandle, $LANG) . '</p>';
break;
    }
}

echo site_footer();

?>

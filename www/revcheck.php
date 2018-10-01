<?php
error_reporting(E_ALL ^ E_NOTICE);

include '../include/init.inc.php';
include '../include/lib_revcheck.inc.php';

if (isset($_GET['lang']) && in_array($_GET['lang'], array_keys($LANGUAGES))) {
	$lang = $_GET['lang'];
}
else {
	$lang = 'en';
}

$lang_name = $LANGUAGES[$lang];

if (isset($_GET['p'])) {
	$tool = $_GET['p'];
}

// Prevent viewing other tools in EN
if ($lang == 'en') {
	$tool = 'default';
}

$DBLANG = SQLITE_DIR . 'rev.php.sqlite';

// Check if db connection can be established and if revcheck for requested lang exists
if ($dbhandle = new SQLite3($DBLANG)) {
    $check_lang_tmp = $dbhandle->query("SELECT COUNT(lang) AS count FROM description WHERE lang = '$lang'");
    $check_lang = $check_lang_tmp->fetchArray();
    if ($lang != 'en' && $check_lang['count'] < 0) {
        site_header();
        echo "<p>This revision check doesn't exist yet.</p>";
        site_footer();
        die;
    }
}
else {
	site_header();
	echo "<p>Database connection couldn't be established</p>";
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
			$uptodate = get_translators_stats($dbhandle, $lang, 'uptodate');
			$outdated = get_translators_stats($dbhandle, $lang, 'outdated');
			$wip      = get_translators_stats($dbhandle, $lang, 'wip');

			foreach($translators as $nick =>$data) {
				$files_w[$nick] = array('uptodate' => '', 'outdated' => '', 'norev' => '', 'wip' => '');
				$files_w[$nick]['uptodate'] = isset($uptodate[$nick]) ? $uptodate[$nick] : '';
				$files_w[$nick]['wip'] = isset($wip[$nick]) ? $wip[$nick] : '';
				$files_w[$nick]['outdated'] = isset($outdated[$nick]) ? $outdated[$nick] : '';
			}

			echo <<<TRANSLATORS_HEAD
<table border="0" cellpadding="4" cellspacing="1">
<tr>
<th rowspan="2">Name</th>
<th rowspan="2">Nick</th>
<th rowspan="2">SVN</th>
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
				'<td>'.(($data['svn'] == 'yes') ? '✓' : '&nbsp;').'</td>',
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
		}
		else {
			$num = count($missfiles);
			echo '<table border="0" cellpadding="3" cellspacing="1" style="text-align:center">';
			echo '<tr><th rowspan="1">Available for translation ('.$num.' files):</th><th colspan="1">kB</th></tr>';

			$last_dir = false;
			$total_size = 0;
			foreach ($missfiles as $miss) {
				if (!$last_dir || $last_dir != $miss['dir']) {
					echo '<tr><th colspan="2">'.$miss['dir'].'</th></tr>';
					$last_dir = $miss['dir'];
				}
				echo '<tr><td>'.$miss['file'].'</td><td>'.$miss['size'].'</td></tr>';
				$total_size += $miss['size'];
				// flush every 200 kbytes
				if (($total_size % 200) == 0) {
					flush();
				}
			}
			echo '<tr><th colspan="2">Total Size ('.$num.' files): '.$total_size.' kB</th></tr>';
			echo '</table>';
		}
		echo gen_date($DBLANG);
	break;

	case 'oldfiles':
		$oldfiles = get_oldfiles($dbhandle, $lang);
		if (!$oldfiles) {
			echo '<p>Good, it seems that this translation doesn\'t contain any file which is not present in English tree.</p>';
		}
		else {
			$num = count($oldfiles);
			echo '<table width="400" border="0" cellpadding="3" cellspacing="1" style="text-align:center">';
			echo '<tr><th rowspan="1">Not in EN tree ('.$num.' files):</th><th colspan="1">kB</th></tr>';

			$last_dir = false;
			$total_size = 0;
			foreach ($oldfiles as $old) {
				if (!$last_dir || $last_dir != $old['dir']) {
					echo '<tr><th colspan="2">'.$old['dir'].'</th></tr>';
					$last_dir = $old['dir'];
				}
				echo '<tr><td>', $old['file'], '</td><td class="r">'.$old['size'].'</td></tr>';
				$total_size += $old['size'];
				// flush every 200 kbytes
				if (($total_size % 200) == 0) {
					flush();
				}
			}
			echo '<tr><th colspan="2">Total Size ('.$num.' files): '.$total_size.' kB</th></tr>';
			echo '</table>';
		}
		echo gen_date($DBLANG);
	break;

	case 'misstags':
		$misstags = get_misstags($dbhandle, $lang);
		$num = count($misstags);
		if (!$num) {
			echo '<p>Good, all files contain revision numbers.</p>';
		}
		else {
			echo '<table border="0" cellpadding="3" cellspacing="1" style="text-align:center">';
			echo '<tr><th rowspan="2">Files without EN-Revision number ('.$num.' files):</th><th colspan="3">Sizes in kB</th></tr>';
			echo '<tr><th>en</th><th>'.$lang.'</th><th>diff</th></</tr>';

			$last_dir = false;

			foreach ($misstags as $row) {
				if (!$last_dir || $last_dir != $row['dir']) {
					echo '<tr><th colspan="4">'.$row['dir'].'</th></tr>';
					$last_dir = $row['dir'];
				}
				echo '<tr><td>'.$row['name'].'</td><td>'.$row['en_size'].'</td><td>'.$row['trans_size'].'</td><td>'.(intval($row['en_size'] - $row['trans_size'])).'</td></tr>';
			}
			echo '</table>';
		}
		echo gen_date($DBLANG);
	break;

	case 'filesummary':
		$file_types = array(
			array (REV_UPTODATE, 'Up to date files'),
			array (REV_OUTDATED, 'Outdated files'),
			array (REV_WIP,      'Work in progress'),
			array (REV_NOREV,    'Files without revision number'),
			array (REV_NOTRANS,  'Files available for translation')
		);

		$file_summary_array = array(
			REV_UPTODATE => array(0,0),
			REV_OUTDATED => array(0,0),
			REV_NOREV    => array(0,0),
			REV_NOTRANS  => array(0,0),
			REV_WIP      => array(0,0)
		);

		$file_summary_array[REV_UPTODATE] = get_stats($dbhandle, $lang, 'uptodate');
		$file_summary_array[REV_OUTDATED] = get_stats($dbhandle, $lang, 'outdated');
		$file_summary_array[REV_NOREV]    = get_stats($dbhandle, $lang, 'norev');
		$file_summary_array[REV_NOTRANS]  = get_stats($dbhandle, $lang, 'notrans');
		$file_summary_array[REV_WIP]      = get_stats($dbhandle, $lang, 'wip');

		echo '<table border="0" cellpadding="4" cellspacing="1" style="text-align:center;">';
		echo '<tr><th>File status type</th><th>Number of files</th><th>Percent of files</th><th>Size of files (kB)</th><th>Percent of size</th></tr>';

		$percent = array(0, 0);

		foreach($file_summary_array as $t => $a) {
			$percent[0] += $a[0];
			$percent[1] += $a[1];
		}

		foreach ($file_types as $num => $type) {
			$tmp_num_percent_0 = ($percent[0] == 0) ? 0 : number_format($file_summary_array[$type[0]][0] * 100 / $percent[0], 2 );
			$tmp_num_percent_1 = ($percent[0] == 0) ? 0 : number_format($file_summary_array[$type[0]][1] * 100 / $percent[1], 2);

			echo '<tr>';
			echo '<td>'.$type[1].'</td>';
			echo '<td>'.$file_summary_array[$type[0]][0].'</td>';
			echo '<td>'.$tmp_num_percent_0.'%</td>';
			echo '<td>'.$file_summary_array[$type[0]][1].'</td>';
			echo '<td>'.$tmp_num_percent_1.'%</td>';
			echo '</tr>';
		}

		echo '<tr><th>Files total</th><th>'.$percent[0].'</th><th>100%</th><th>'.$percent[1].'</th><th>100%</th></tr>';
		echo '</table>';
		echo gen_date($DBLANG);
	break;


	case 'files' :
		// we need a dir to browse
		$dirs = get_dirs($dbhandle, $lang);
		$users = get_translators($dbhandle, $lang);

		if (empty($dirs)) {
			echo '<p>Error: no directories found in database.</p>';
			$sidebar = nav_tools($lang);
			site_footer($sidebar);
			die;
		}

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
			}
			else {
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
			}
			else {
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
		}
		elseif (isset($_GET['dir'])) {
			$outdated = get_outdated_files($dbhandle, $lang, 'dir', $_GET['dir']);
		}
		else {
			$outdated = get_outdated_files($dbhandle, $lang, 'all');
		}

		if (empty($outdated)) {
			echo '<p>Good, it seems that all files are up to date for these conditions.</p>';
		}
		else {
			echo <<<END_OF_MULTILINE
<table border="0" cellpadding="4" cellspacing="1" style="text-align:center">
<tr>
<th rowspan="2">Translated file</th>
<th colspan="2">Revision</th>
<th rowspan="2">Maintainer</th>
<th rowspan="2">Status</th>
</tr>
<tr>
<th>en</th>
<th>$lang</th>
</tr>
<tr><th colspan="5">{$outdated[0]['name']}</th></tr>
END_OF_MULTILINE;
			$last_dir = false;
			$prev_name = $outdated[0]['name'];

			foreach ($outdated as $r) {
				if ($r['name'] != $prev_name) {
				   echo '<tr><th colspan="5">'.$r['name'].'</th></tr>';
				   $prev_name = $r['name'];
				}

				// Make the maintainer a link, if we have that maintainer in the list
				if ($r['maintainer'] && $r["maintainer"] != 'nobody') {
					$r["maintainer"] = '<a href="?p=translators&amp;lang=' . $lang . '">' . $r["maintainer"] . '</a>';
				}

				// Make a link to the SVN repository's diff script
				$r['short_name'] = '<a href="http://svn.php.net/viewvc/phpdoc/en/trunk' . $r['name'] . '/' . $r['file'] .
				'?r1=' . $r['trans_rev'] . '&amp;r2=' . $r['en_rev'] . '&amp;view=patch">' . $r['file'] . '</a>';

				// Add a [diff] link
				$r['short_name'] .= ' <a href="http://svn.php.net/viewvc/phpdoc/en/trunk' . $r['name'] . '/' . $r['file'] .
				'?r1=' . $r['trans_rev'] . '&amp;r2=' . $r['en_rev'] . '">[diff]</a>';

				// Write out the line for the current file (get file name shorter)
				echo '<tr>'.
				"<td>{$r['short_name']}</td>".
				"<td>{$r['en_rev']}</td>" .
				"<td>{$r['trans_rev']}</td>" .
				"<td> {$r['maintainer']}</td>" .
				"<td> {$r['status']}</td></tr>\n";
			}
			echo '</table>';
		}
		echo gen_date($DBLANG);
	break;

	case 'graph':
		$path = "images/revcheck/info_revcheck_php_$lang.png";
		if (is_readable($path)) {
			echo '<img src="'.$path.'" alt="info">';
			echo gen_date($DBLANG);
		}
		else {
			echo "<p>Can't find graph.</p>";
		}
	break;

	default:
		if ($lang == 'en') {
			echo '<img src="images/revcheck/info_revcheck_php_all_lang.png" alt="Info" class="chart">';
			echo '<p>This is all what we can show for original manual. To get more tools, please select translation language.</p>';
			echo gen_date($DBLANG);
			$sidebar = nav_languages();
			site_footer($sidebar);
		}
		else {
			$intro_result = $dbhandle->query("SELECT intro FROM description WHERE lang = '$lang'");
			$intro = $intro_result->fetchArray();
			echo '<h2>Intro for language</h2>';
			echo '<p>'.$intro[0].'</p>';
			echo '<p>Links to available tools are placed on the right sidebar.</p>';
		}
	break;
}

if ($lang != 'en') {
	$sidebar = nav_tools($lang);
	site_footer($sidebar);
}
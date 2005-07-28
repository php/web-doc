<?php
/* $Id$ */

include '../include/init.inc.php';
require_once '../include/lib_auth.inc.php';

auth();
if (!is_admin()) {
	die('you are not an admin!');
}

ob_start(); // hack for phpinfo()
echo site_header('Admin Zone');


// helper functions

function info() {
	ob_end_clean(); //get ride of the headers
	phpinfo();
	exit;
}


function sql()
{
	if (empty($_POST['command'])) {
		sql_print_textarea('', @$_REQUEST['file']);

	// execute the sql
	} else {
		$idx = sqlite_open(dirname(__FILE__) . '/../sqlite/' . $_POST['file'], 0666, $error);
		if (!$idx) {
			echo "<p>$error</p>";
			sql_print_textarea($_POST['command'], $_POST['file']);
			return;
		}

		$result = @sqlite_query($idx, $_POST['command']);
		if (!$result) {
			echo '<p><strong>There was an error in the query:</strong> ' . sqlite_error_string(sqlite_last_error($idx)) . '</p><p>&nbsp;</p>';
			sql_print_textarea($_POST['command'], $_POST['file']);
			return;
		}

		echo '<p>Affected rows: ' . sqlite_changes($idx) , '</p>';
		echo '<pre>' . htmlspecialchars(print_r(sqlite_fetch_all($result), true)) . '</pre>';
		sqlite_close($idx);
	}
}


function sql_print_textarea($txt, $file)
{
	$dbs = glob(dirname(__FILE__) . '/../sqlite/*.sqlite');
	if ($dbs) {
		echo '<p>Available DBs:</p><ul>';
		foreach ($dbs as $db) {
			$db = basename($db);
			echo "<li><a href='$_SERVER[REQUEST_URI]&file=$db'>$db</a></li>";
		}
		echo '</ul>';
	} else {
		echo '<p>There are no DBs currently available</p>';
	}

	echo <<< HTML
<p>&nbsp;</p>
<form method="POST" action="$_SERVER[REQUEST_URI]">
 <p>SQL: <textarea name="command" rows="5" cols="70">$txt</textarea></p>
 <p>DB: <input type="text" name="file" value="$file" /></p>
 <p><input type="submit" value="Execute" /></p>
</form>
HTML;

}

// control flow
if (empty($_GET['z'])) {

	echo <<< HTML
<p>Menu:</p>
<ul>
 <li><a href="?z=sql">SQL Injector</a></li>
 <li><a href="?z=info">PHP info</a></li>
</ul>
HTML;

} else {
	switch ($_GET['z']) {
		case 'sql':
		case 'info':
			$_GET['z']();
			break;

		default:
			echo '<p>wrong zone!</p>';
	}
}


echo site_footer();
?>
<?php
/*
+----------------------------------------------------------------------+
| PHP Documentation Site Source Code                                   |
+----------------------------------------------------------------------+
| Copyright (c) 1997-2011 The PHP Group                                |
+----------------------------------------------------------------------+
| This source file is subject to version 3.0 of the PHP license,       |
| that is bundled with this package in the file LICENSE, and is        |
| available at through the world-wide-web at                           |
| http://www.php.net/license/3_0.txt.                                  |
| If you did not receive a copy of the PHP license and are unable to   |
| obtain it through the world-wide-web, please send a note to          |
| license@php.net so we can mail you a copy immediately.               |
+----------------------------------------------------------------------+
| Authors: Nuno Lopes <nlopess@php.net>                                |
+----------------------------------------------------------------------+
$Id$
*/

include '../include/init.inc.php';

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

function pearinfo() {
    ob_end_clean();
    require_once 'PEAR/Info.php';
    $info = new PEAR_Info('/usr/local/share/pear');
    $info->show();
    exit;
}

function print_file_list($base)
{
    if (!empty($_GET['file']) && !is_dir(dirname(__FILE__) . "/../$_GET[file]"))
        return;

    $files = glob(dirname(__FILE__) . '/../' .  @$_GET['file'] . $base);
    $uri   = preg_replace('/&file=[^&]*/', '', $_SERVER['REQUEST_URI']);
    if ($files) {
        echo '<p>Available files:</p><ul>';
        foreach ($files as $file) {
            $file = basename($file);
            if ($file == '.svn') continue;
            echo "<li><a href='$uri&file=". urlencode(@$_GET['file'] . "/$file")  . "'>$file</a></li>";
        }
        echo '</ul>';
    } else {
        echo '<p>There are no files currently available</p>';
    }

}

function sql()
{
    if (empty($_POST['command'])) {
        sql_print_textarea('', @$_REQUEST['file']);

    // execute the sql
    } else {
        $file = dirname(__FILE__) . '/../sqlite/' . $_POST['file'];
        $idx = sqlite_open($file, 0666, $error);
        if (!$idx) {
            echo "<p>$error</p>";
            sql_print_textarea($_POST['command'], $_POST['file']);
            return;
        }

        $result = sqlite_query($idx, $_POST['command']);

        if (!$result) {
            echo '<p><strong>There was an error in the query:</strong> ' . sqlite_error_string(sqlite_last_error($idx)) . '</p><p>&nbsp;</p>';
            sql_print_textarea($_POST['command'], $_POST['file']);
            return;
        }

        echo '<p>File size: ' . filesize($file) / 1024 . ' KB</p>';
        echo '<p>Affected rows: ' . sqlite_changes($idx) , '</p>';
        echo '<pre>' . htmlspecialchars(print_r(sqlite_fetch_all($result), true)) . '</pre>';
        sqlite_close($idx);
        sql_print_textarea($_POST['command'], $_POST['file']);
    }
}


function sql_print_textarea($txt, $file)
{
    print_file_list('sqlite/*.sqlite');

    echo <<< HTML
<p>&nbsp;</p>
<form method="POST" action="$_SERVER[REQUEST_URI]">
 <p>SQL: <textarea name="command" rows="5" cols="70">$txt</textarea></p>
 <p>DB: <input type="text" name="file" value="$file" /></p>
 <p><input type="submit" value="Execute" /></p>
</form>
HTML;

}


function chmodf()
{
    if (empty($_POST['mod']) || empty($_REQUEST['file'])) {
        rmch_print_html(@$_REQUEST['file'], @$_POST['mod'], true);

    // change the permissions
    } else {
        $path = realpath(dirname(__FILE__) . "/../$_POST[file]");
        $allowed = dirname(dirname(__FILE__));

        if (strncmp($path, $allowed, strlen($allowed))) {
            echo "<p>The file isn't within an allowed directory!</p>";
            return;
        }

        if (chmod($path, octdec($_POST['mod'])))
            echo '<p>chmod() ok!</p>';
        else
            echo '<p>chmod() failed!</p>';
    }
}


function rm()
{
    if (empty($_REQUEST['file'])) {
        rmch_print_html(@$_REQUEST['file'], '', false);

    // change the permissions
    } else {
        $path = realpath(dirname(__FILE__) . "/../$_REQUEST[file]");
        $allowed = dirname(dirname(__FILE__));

        if (strncmp($path, $allowed, strlen($allowed))) {
            echo "<p>The file isn't within an allowed directory!</p>";
            return;
        }

        if (unlink($path))
            echo '<p>unlink() ok!</p>';
        else
            echo '<p>unlink() failed!</p>';
    }
}


function rmch_print_html($file, $val, $mod)
{
    print_file_list('/*');

    echo <<< HTML
<p>&nbsp;</p>
<form method="POST" action="$_SERVER[REQUEST_URI]">
 <p>File: <input type="text" name="file" value="$file" /></p>
HTML;

    if ($mod)
        echo '<p>Permissions: <input type="text" name="mod" value="' . $val . '" /></p>';

    echo <<< HTML
 <p><input type="submit" value="Execute" /></p>
</form>
HTML;

}


// control flow
if (empty($_GET['z'])) {
$lastSVNUpdate = date ('r', filemtime('./.svn/entries'));

    echo <<< HTML
<p>Menu:</p>
<ul>
 <li><a href="?z=sql">SQL Injector</a></li>
 <li><a href="?z=chmodf">chmod</a></li>
 <li><a href="?z=rm">remove files</a></li>
 <li><a href="?z=info">PHP info</a></li>
 <!--<li><a href="?z=pearinfo">PEAR info</a></li>
     pearinfo is broken for everyone-->
</ul>
<br />
<p>Last SVN Update: $lastSVNUpdate</p>
HTML;

} else {
    switch ($_GET['z']) {
        case 'sql':
        case 'chmodf':
        case 'rm':
        case 'info':
        case 'pearinfo':
            $_GET['z']();
            break;

        default:
            echo '<p>wrong zone!</p>';
    }
}


echo site_footer();
?>

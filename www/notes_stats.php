<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4:
+----------------------------------------------------------------------+
| PHP Documentation Site Source Code                                   |
+----------------------------------------------------------------------+
| Copyright (c) 1997-2005 The PHP Group                                |
+----------------------------------------------------------------------+
| This source file is subject to version 3.0 of the PHP license,       |
| that is bundled with this package in the file LICENSE, and is        |
| available at through the world-wide-web at                           |
| http://www.php.net/license/3_0.txt.                                  |
| If you did not receive a copy of the PHP license and are unable to   |
| obtain it through the world-wide-web, please send a note to          |
| license@php.net so we can mail you a copy immediately.               |
+----------------------------------------------------------------------+
| Authors: Vincent Gevers <vincent@php.net>                            |
+----------------------------------------------------------------------+
$Id$
*/

require_once '../include/init.inc.php';

$minact = 100;

$DBFile = SQLITE_DIR.'notes_stats.sqlite';

if (@filesize($DBFile) < 10) {
    echo site_header('Statistics not available');
    echo '<h2>Statistics not available</h2>';
    echo site_footer();
    exit;
}

$sqlite = sqlite_open($DBFile);

$sql = "SELECT * FROM notes_info";
$info = sqlite_fetch_array(sqlite_query($sqlite, $sql), SQLITE_ASSOC);

if ($info['subjects'] == 0) {
    echo site_header('Statistics not available yet');
    echo '<h2>Statistics not available yet</h2>';
    echo site_footer();
    exit;
}

echo site_header("Note Statistics for ".date('j F Y', $info['build_date']));

?>
<h3><strong><?php echo $info['subjects']; ?></strong> subjects parsed</h1>

<table border='0' cellspacing="10"><tr valign="top"><td valign="top">
<table border='0'>
    <tr>
        <th colspan="5" align="center">Editors Stats with more than 100 actions</th>
    </tr>
    <tr>
        <th>user</th>
        <th>deleted</th>
        <th>rejected</th>
        <th>modified</th>
        <th>total</th>
    </tr>

<?php

$sql = "SELECT * FROM notes_stats ORDER BY total DESC LIMIT 0,$minact";
$tmp = array();
$res = sqlite_query($sqlite, $sql);
while($tmp[] = sqlite_fetch_array($res, SQLITE_ASSOC)) {
// nothing here
}

$bg = '#EBEBEB';
foreach ($tmp as $id => $c) {

    if($c['total'] >= $minact) { 
        echo "<tr bgcolor=\"";
        $bg = ($bg == '#EBEBEB') ? '#BEBEBE' : '#EBEBEB';
        echo "$bg\">\n\t<td>".$c['username']."</td>\n\t<td>";
        echo isset($c['deleted']) ? $c['deleted'] : '0';
        echo "</td>\n\t<td>";
        echo isset($c['rejected']) ? $c['rejected'] : '0';
        echo "</td>\n\t<td>";
        echo isset($c['modified']) ? $c['modified'] : '0';
        echo "</td>\n\t<td>";
        echo $c['total'];
        echo "</td>\n</tr>\n";
    }
    
}

?>
</table>

</td><td valign="top">
Last half year (with more than <?php echo $minact; ?> actions counted)
<table border='0'>
    <tr>
        <th colspan="5" align="center">Recent Editors stats</th>
    </tr>
    <tr>
        <th>user</th>
        <th>deleted</th>
        <th>rejected</th>
        <th>modified</th>
        <th>total</th>
    </tr>

<?php

$sql = "SELECT * FROM notes_stats_new ORDER BY total DESC LIMIT 0,$minact";
$tmp = array();
$res = sqlite_query($sqlite, $sql);
while($tmp[] = sqlite_fetch_array($res, SQLITE_ASSOC)) {
// nothing here
}

$bg = '#EBEBEB';
foreach ($tmp as $id => $c) {

    if($c['total'] >= $minact) {    
        echo "<tr bgcolor=\"";
        $bg = ($bg == '#EBEBEB') ? '#BEBEBE' : '#EBEBEB';
        echo "$bg\">\n\t<td>".$c['username']."</td>\n\t<td>";
        echo $c['deleted'];
        echo "</td>\n\t<td>";
        echo $c['rejected'];
        echo "</td>\n\t<td>";
        echo $c['modified'];
        echo "</td>\n\t<td>";
        echo $c['total'];
        echo "</td>\n</tr>\n";
    }

}

?>
</table>

<br />
Before the last half year (with more than <?php echo $minact; ?> actions counted)
<table border='0'>
    <tr>
        <th colspan="5" align="center">Older Editors Stats</th>
    </tr>
    <tr>
        <th>user</th>
        <th>deleted</th>
        <th>rejected</th>
        <th>modified</th>
        <th>total</th>
    </tr>

<?php

$sql = "SELECT * FROM notes_stats_old ORDER BY total DESC LIMIT 0,$minact";
$tmp = array();
$res = sqlite_query($sqlite, $sql);
while($tmp[] = sqlite_fetch_array($res, SQLITE_ASSOC)) {
// nothing here
}

$bg = '#EBEBEB';
foreach ($tmp as $id => $c) {

    if($c['total'] >= $minact) {
        echo "<tr bgcolor=\"";
        $bg = ($bg == '#EBEBEB') ? '#BEBEBE' : '#EBEBEB';
        echo "$bg\">\n\t<td>".$c['username']."</td>\n\t<td>";
        echo $c['deleted'];
        echo "</td>\n\t<td>";
        echo $c['rejected'];
        echo "</td>\n\t<td>";
        echo $c['modified'];
        echo "</td>\n\t<td>";
        echo $c['total'];
        echo "</td>\n</tr>\n";
    }

}


?>
</table>
</td></tr></table>


<br />

<table border="0" cellspacing="10"><tr valign="top"><td valign="top">
<table border='0'>
    <tr>
        <th colspan="5" align="center">Total Editors Stats</th>
    </tr>
    <tr>
        <th>user</th>
        <th>deleted</th>
        <th>rejected</th>
        <th>modified</th>
        <th>total</th>
    </tr>

<?php

$sql = "SELECT * FROM notes_stats ORDER BY username ASC";
$tmp = array();
$res = sqlite_query($sqlite, $sql);
while($tmp[] = sqlite_fetch_array($res, SQLITE_ASSOC)) {
// nothing here
}

$bg = '#EBEBEB';

foreach ($tmp as $id => $c) {
    if ($c['username'] == '')
        continue;
 
    echo "<tr bgcolor=\"";
    $bg = ($bg == '#EBEBEB') ? '#BEBEBE' : '#EBEBEB';
    echo "$bg\">\n\t<td>".$c['username']."</td>\n\t<td>";
    echo $c['deleted'];
    echo "</td>\n\t<td>";
    echo $c['rejected'];
    echo "</td>\n\t<td>";
    echo $c['modified'];
    echo "</td>\n\t<td>";
    echo $c['total'];
    echo "</td>\n</tr>\n";

}


?>
</table>

</td><td valign="top">
<table border='0'>
    <tr>
        <th colspan="3" align="center">Editors top 15</th>
    </tr>
    <tr>
        <th>rank</th>
        <th>user</th>
        <th>total</th>
    </tr>
<?php

$sql = "SELECT username, total FROM notes_stats ORDER BY total DESC LIMIT 0,15";
$tmp = array();
$res = sqlite_query($sqlite, $sql);
while($tmp[] = sqlite_fetch_array($res, SQLITE_ASSOC)) {
// nothing here
}

$i = 0;
foreach($tmp as $id => $c) {
       
    $i++;
    echo "<tr bgcolor=\"";
    $bg = ($bg == '#EBEBEB') ? '#BEBEBE' : '#EBEBEB'; 
    echo "$bg\">\n\t".
    "<td>".$i."</td>\n\t".
    "<td>".$c['username']."</td>\n\t".
    "<td>".$c['total']."</td>\n".
    "</tr>\n";
    
    if ($i == 15)
        break;
}       

?>
</table>

<br />
<table border='0'>
    <tr>
        <th colspan="3" align="center">Manual pages most active top 20</th>
    </tr>
    <tr>
        <th>rank</th>
        <th>page</th>
        <th>total</th>
    </tr>
<?php

$sql = "SELECT * FROM notes_files ORDER BY total DESC LIMIT 0,20";
$tmp = array();
$res = sqlite_query($sqlite, $sql);
while($tmp[] = sqlite_fetch_array($res, SQLITE_ASSOC)) {
// nothing here
}

$i = 0;
foreach($tmp as $id => $c) {
       
    $i++;
    echo "<tr bgcolor=\"";
    $bg = ($bg == '#EBEBEB') ? '#BEBEBE' : '#EBEBEB'; 
    echo "$bg\">\n\t".
    "<td>".$i."</td>\n\t".
    "<td>".$c['page']."</td>\n\t".
    "<td>".$c['total']."</td>\n".
    "</tr>\n";
    
    if ($i == 20)
        break;
}       

?>
</table>

</td></tr></table>
<?php

echo 'Last updated ' . date('r', $info['build_date']) . "\n";

sqlite_close($sqlite);
echo site_footer();

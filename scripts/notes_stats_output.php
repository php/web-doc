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

echo "<?php require_once '../include/init.inc.php';\n";

// Settings for this script can be found in ./notes_stats.php

if (@filesize($DBFile) < 3000000) { // require at least 3 MBs
    echo "echo site_header('Statistics not available');\n";
    echo "echo '<h2>Statistics not available</h2>';\n";
    echo "echo site_footer();";
    return;
}

$sqlite = sqlite_open($DBFile);

$info = sqlite_fetch_array(sqlite_query($sqlite, 'SELECT * FROM info'), SQLITE_ASSOC);

if ($info['last_article'] < 50000) {
    echo "echo site_header('Statistics not available yet');\n";
    echo "echo '<h2>Statistics not available yet</h2>';\n";
    echo "echo site_footer();";
    return;
}

echo "echo site_header('Note Statistics for " . date('j F Y', $info['build_date']) . "'); ?>\n";

?>
<h3><strong><?php echo $info['last_article']; ?></strong> subjects parsed</h3>

<table border='0' cellspacing="10"><tr valign="top"><td valign="top">
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

$array = sqlite_fetch_all(sqlite_query($sqlite, 'SELECT * FROM notes'), SQLITE_ASSOC);
$time  = time() - 60*60*24*180;

foreach($array as $row) {
    @++$data[$row['who']][$row['action']];
    @++$total[$row['who']];
    @++$manual[$row['manpage']];

    if ($row['time'] >= $time) {
        @++$data_new[$row['who']][$row['action']];
        @++$data_new[$row['who']]['total'];
    }

}

unset($data['']);
ksort($data);
ksort($data_new);

$bg = '#EBEBEB';
foreach ($data as $id => $c) {

    echo "<tr bgcolor=\"";
    $bg = ($bg == '#EBEBEB') ? '#BEBEBE' : '#EBEBEB';
    echo "$bg\">\n\t<td>".$id."</td>\n\t<td>";
    echo isset($c['deleted']) ? $c['deleted'] : '0';
    echo "</td>\n\t<td>";
    echo isset($c['rejected']) ? $c['rejected'] : '0';
    echo "</td>\n\t<td>";
    echo isset($c['modified']) ? $c['modified'] : '0';
    echo "</td>\n\t<td>";
    echo $total[$id];
    echo "</td>\n</tr>\n";
}

unset($data);

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

$bg = '#EBEBEB';
foreach ($data_new as $id => $c) {

    if($c['total'] >= $minact) {    
        echo "<tr bgcolor=\"";
        $bg = ($bg == '#EBEBEB') ? '#BEBEBE' : '#EBEBEB';
        echo "$bg\">\n\t<td>".$id."</td>\n\t<td>";
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

unset($data_new);

?>
</table>
<br/>
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
arsort($total);

$i = 0;
foreach($total as $id => $val) {
       
    ++$i;
    echo "<tr bgcolor=\"";
    $bg = ($bg == '#EBEBEB') ? '#BEBEBE' : '#EBEBEB'; 
    echo "$bg\">\n\t".
    "<td>".$i."</td>\n\t".
    "<td>".$id."</td>\n\t".
    "<td>".$val."</td>\n".
    "</tr>\n";
    
    if ($i == 15)
        break;
}       

unset($total);
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
arsort($manual);

$i = 0;
foreach($manual as $id => $c) {
       
    ++$i;
    echo "<tr bgcolor=\"";
    $bg = ($bg == '#EBEBEB') ? '#BEBEBE' : '#EBEBEB'; 
    echo "$bg\">\n\t".
    "<td>".$i."</td>\n\t".
    "<td>".$id."</td>\n\t".
    "<td>".$c."</td>\n".
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
echo '<?php echo site_footer(); ?>';

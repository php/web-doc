<?php
/*
  TODO:
    - template this
    - Hyperlink <a ... /> the URLs
    - Shrink long URLs: http://example.org/this/is/a...long/url
*/
?>

<h2>Entities last checked: <?php echo date('Y-m-d H:i:s', $startTime); ?></h2>
<?php if (!$isComplete) { ?>
    <h3>Check not complete.</h3>
<?php } ?>
<h2>Protocols: <?php echo $schemes; ?></h2>
<br />

<?php foreach ($entData as $resultType => $results) { ?>
    <h2><?php echo $resultLkp[$resultType]; ?> (<?php echo count($results); ?>)</h2>
    <table border="0">
        <tr>
            <th>Entity Name</th>
            <?php if ($extraCol[$resultType]) { ?>
                <th>Redirect URL</th>
            <?php } else { ?>
                <th>URL</th>
	    <?php } ?>
        </tr>
        <?php foreach ($results AS $r) { ?>
	    <tr>
                <td><?php echo $r['entity']; ?></td>
                <?php if ($extraCol[$resultType]) { ?>
                    <td><?php echo $r['return_val']; ?></td>
                <?php } else { ?>
                    <td><?php echo $r['url']; ?></td>
                <?php } ?>
	    </tr>
        <?php } ?>
    </table>
    <br />
<?php } ?>

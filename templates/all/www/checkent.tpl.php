<?php
/*
  TODO:
    - language-entity  this
    - Shrink long URLs: http://example.org/this/is/a...long/url
*/

if ($isComplete) { ?>
<h2>Entities last checked: <?php echo date('Y-m-d H:i:s', $startTime); ?></h2>
<h2>Protocols: <?php echo $schemes; ?></h2>
<?php } else { ?>
    <h3>Check not complete.</h3>
<?php } ?>
<br />

<?php foreach ($entData as $resultType => $results) { ?>
    <h2><?php echo $resultLkp[$resultType]; ?> (<?php echo count($results); ?>)</h2>
    <table border="0">
        <tr>
            <th>Entity Name</th>
            <th>URL</th>
            <?php if ($extraCol[$resultType]) { ?>
                <th>Redirect URL</th>
        <?php } ?>
        </tr>
        <?php foreach ($results AS $r) { ?>
            <tr>
                <td><?php echo $r['entity']; ?></td>
                <?php if ($extraCol[$resultType]) { ?>
                    <td><a href ="<?php echo $r['url']; ?>" rel="nofollow"><?php echo $r['url']; ?></a></td>
                    <td><a href ="<?php echo $r['return_val']; ?>" rel="nofollow"><?php echo $r['return_val']; ?></a></td>
                <?php } else { ?>
                    <td><a href="<?php echo $r['url']; ?>" rel="nofollow"><?php echo $r['url']; ?></a></td>
                <?php } ?>
            </tr>
        <?php } ?>
    </table>
    <br />
<?php } ?>

<?php
/*
  TODO:
    - language-entity  this
*/

if ($isComplete) { ?>
<h2>Entities last checked: <?php echo date('Y-m-d H:i:s', $startTime); ?></h2>
<h2>Protocols: <?php echo $schemes; ?></h2>
<?php } else { ?>
    <h3>Check not complete.</h3>
<?php } ?>
<?php if($wideMode) { ?>
Viewing in wide mode. <a href="checkent.php?wideMode=0">Switch to normal mode?</a><br />
<?php } else { ?>
<a href="checkent.php?wideMode=1">Switch to wide mode?</a><br />
<?php } ?>
<br />

<?php foreach ($entData as $resultType => $results) { ?>
    <h2><?php echo $resultLkp[$resultType]; ?> (<?php echo count($results); ?>)</h2>
    <table border="0">
        <tr>
            <th>Entity Name</th>
            <?php if ($extraCol[$resultType]) { ?>
	        <?php if ($wideMode) { ?>
                    <th>URL</th>
		<?php } ?>
                <th>Redirect URL</th>
            <?php } else { ?>
                <th>URL</th>
	    <?php } ?>
        </tr>
        <?php foreach ($results AS $r) { ?>
            <tr>
                <?php if ($extraCol[$resultType]) { ?>
		    <?php if ($wideMode) { ?>
                        <td><?php echo $r['entity']; ?></td>
                        <td><a href="<?php echo $r['url']; ?>" rel="nofollow"><?php echo $r['url']; ?></a></td>
                        <td><a href="<?php echo $r['return_val']; ?>" rel="nofollow"><?php echo $r['return_val']; ?></a></td>
		    <?php } else { ?>
                        <td><a href="<?php echo $r['url']; ?>"><?php echo $r['entity']; ?></a></td>
                        <td>
			    <a href="<?php echo $r['return_val']; ?>" rel="nofollow" title="<?php echo $r['return_val']; ?>">
			        <?php echo str_chop($r['return_val'], 60, TRUE); ?>
		            </a>
			</td>
		    <?php } ?>
                <?php } else { ?>
                    <td><?php echo $r['entity']; ?></td>
		    <?php if ($wideMode) { ?>
                        <td><a href="<?php echo $r['url']; ?>" rel="nofollow"><?php echo $r['url']; ?></a></td>
		    <?php } else { ?>
		        <td>
			    <a href="<?php echo $r['url']; ?>" rel="nofollow" title="<?php echo $r['url']; ?>">
			        <?php echo str_chop($r['url'], 60, TRUE); ?>
			    </a>
			</td>
		    <?php } ?>
                <?php } ?>
            </tr>
        <?php } ?>
    </table>
    <br />
<?php } ?>

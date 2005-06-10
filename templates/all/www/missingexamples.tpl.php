<h1>Functions with missing examples</h1>
There are <?php echo $missingEgCount; ?> functions that are missing examples.

<table>
 <?php foreach ($missingEgData as $name => $ext) { ?>
  <tr>
   <th><?php echo $name.' ('.count($ext).')'; ?></th>
  </tr>
  <?php foreach ($ext as $function) { ?>
  <tr><td valign="top"><?php echo $function; ?></td></tr>
  <?php } ?>
 <?php } ?>
</table>

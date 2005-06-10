<h1>Undocumented Functions</h1>
There are <?php echo $undocCount; ?> undocumented functions.

<table>
 <?php foreach ($undocData as $name => $ext) { ?>
  <tr>
   <th><?php echo $name.' ('.count($ext).')'; ?></th>
  </tr>
  <?php foreach ($ext as $function) { ?>
  <tr><td valign="top"><?php echo $function; ?></td></tr>
  <?php } ?>
 <?php } ?>
</table>

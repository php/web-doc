<h1>Functions with missing examples</h1>
There are <?php echo $missingEgCount; ?> functions that are missing examples.

<table>
 <?php foreach ($missingEgData as $name => $ext) { ?>
  <tr>
   <th><a href="http://php.net/<?php echo $name ?>"><?php echo $name.'</a> ('.count($ext).')'; ?></th>
  </tr>
  <?php foreach ($ext as $function) { ?>
  <tr><td valign="top"><a href="http://php.net/<?php echo $function; ?>"><?php echo $function; ?>()</a></td></tr>
  <?php } ?>
 <?php } ?>
</table>

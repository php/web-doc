<h1>Entities in the PHP Documentation</h1>
(non-file entities)

<table>
 <tr>
  <th>Entitiy ID</th>
  <th>Value</th>
 </tr>

 <?php foreach ($entData as $eID => $eVal) { ?>
  <tr>
   <td valign="top"><a name="ent-<?php echo $eID;?>"><?php echo $eID; ?></a></td>
   <td valign="top"><?php echo $eVal; ?></td>
  </tr>
 <?php } ?>
</table>

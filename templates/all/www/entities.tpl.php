<h1>Entities in the PHP Documentation</h1>
(non-file entities)

<table>
 <tr>
  <th>Entitiy ID</th>
  <th>Value</th>
 </tr>

 <?php foreach ($entData as $eID => $eVal) { ?>
  <tr>
   <td><a name="ent-<?php echo $eID;?>"><?php echo $eID; ?></a></td>
   <td><?php echo ent_value(ent_link(strip_tags($eVal))); ?></td>
  </tr>
 <?php } ?>
</table>

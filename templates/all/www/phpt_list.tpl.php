<?php

if ($ntotal == 200) {
    $limitError = '<strong>&docweb.phpt.limitreached;</strong><br />';
} else {
    $limitError = '';
}

echo '
<h1>&docweb.phpt.list;</h1>';

if(is_admin()) {
    echo '
<a href="?import">&docweb.phpt.import;</a><br />';
}

echo '
<a href="?generateAll">&docweb.phpt.generateall;</a><br /><br />

<form action="phpt_generator.php" method="post" class="phpt">

<input type="text" name="q" value="'.$search.'" style="display:inline; margin: 0px;" /> <input type="submit" name="search" value="&docweb.phpt.search;"/>';

if (!empty($tests)) {

    echo '
<br /><br /><br />

<input type="submit" name="generate" value="&docweb.phpt.generate;"/> ';

    if (is_admin()) {
        echo '<input type="submit" name="delete" value="&docweb.phpt.delete;"/>';
    }

    echo '
<br /><br />
 '.$limitError.'Stats: '.$ntotal.' examples, '.$napproved.' approved, '.$nfilled.' filled, '.$nimported.' imported.<br />
 <table border="1">
  <tr>
   <th>&docweb.phpt.list.id;</th>
   <th>&docweb.phpt.list.location;</th>
   <th>&docweb.phpt.list.title;</th>
   <th>&docweb.phpt.list.status;</th>
  </tr>';


    foreach ($tests as $test) {

        echo '
  <tr class="'.$test['class'].'">
   <td><input type="checkbox" name="ids['.$test['id'].']" /> <a href="?editId='.$test['id'].'&q='.$search_enc.'" title="Edit">'.$test['id'].'</a></td>
   <td><a href="'.$test['cvs_link'].'" target="_blank">'.$test['location'].'</a></td>
   <td>'.$test['title_limited'].'</td>
   <td>'.$test['status'].'</td>
  </tr>';

    }

    echo '
 </table>
 
 <br /><input type="submit" name="generate" value="&docweb.phpt.generate;"/> ';
    if (is_admin()) {
        echo '<input type="submit" name="delete" value="&docweb.phpt.delete;"/>';
    }

}
?>
</form>

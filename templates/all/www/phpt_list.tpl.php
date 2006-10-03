<?php

if ($ntotal == 200) {
    $limitError = '<strong>&docweb.phpt.limitreached;</strong><br />';
} else {
    $limitError = '';
}

echo <<<HTML

<h1>&docweb.phpt.list;</h1>

<a href="?import">&docweb.phpt.import;</a><br /><br />

<form action="phpt_generator.php" method="post" class="phpt">

<input type="text" name="q" value="{$search}" style="display:inline; margin: 0px;" /> <input type="submit" name="search" value="&docweb.phpt.search;"/>
HTML;

if (!empty($tests)) {

    echo <<<HTML
<br /><br /><br />

<input type="submit" name="generate" value="&docweb.phpt.generate;"/> <input type="submit" name="delete" value="&docweb.phpt.delete;"/><br /><br />
 {$limitError}Stats: $ntotal examples, $napproved approved, $nfilled filled, $nimported imported.<br />
 <table border="1">
  <tr>
   <th>&docweb.phpt.list.id;</th>
   <th>&docweb.phpt.list.location;</th>
   <th>&docweb.phpt.list.title;</th>
   <th>&docweb.phpt.list.status;</th>
  </tr>
HTML;


    foreach ($tests as $test) {

        echo <<<HTML

  <tr class="{$test['class']}">
   <td><input type="checkbox" name="ids[{$test['id']}]" /> <a href="?editId={$test['id']}&q={$search_enc}" title="Edit">{$test['id']}</a></td>
   <td><a href="{$test['cvs_link']}" target="_blank">{$test['location']}</a></td>
   <td>{$test['title_limited']}</td>
   <td>{$test['status']}</td>
  </tr>
HTML;

    }

echo <<<HTML
 </table>
 
 <br /><input type="submit" name="generate" value="&docweb.phpt.generate;"/> <input type="submit" name="delete" value="&docweb.phpt.delete;"/>
HTML;

}
?>
</form>

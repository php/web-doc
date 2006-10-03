<?php
if (!empty($error)) {

    echo <<<HTML
<h2>Error:</h2>
$error;
<br /><br /><a href="phpt_generator.php?q={$q}">&docweb.phpt.return;</a>
HTML;

    return;
}

if ($edited === false) {
    echo '<h2>&docweb.phpt.edit.error;</h2>';
} else if ($edited === true) {
    echo '<h2>&docweb.phpt.edit.ok;</h2>';
}

echo <<<HTML
<h1>&docweb.phpt.edit;</h1>

<a href="phpt_generator.php?q={$q}">&docweb.phpt.return;</a><br />
<a href="phpt_generator.php?generateId={$test['id']}">&docweb.phpt.generate;</a><br />

<form action="" method="POST" class="phpt">
  <h2>&docweb.phpt.edit.title;</h2>
  <input name="title" type="text" value="{$test['title']}" size="80" />
  
  <h2>&docweb.phpt.edit.skipif;</h2>
  <textarea name="skipif" rows="{$test['skipif_lines']}" cols="80">{$test['skipif']}</textarea>
  
  <h2>&docweb.phpt.edit.test;</h2>
  <textarea name="test" rows="{$test['test_lines']}" cols="80">{$test['test']}</textarea>

  <h2>&docweb.phpt.edit.expected;</h2>
  <textarea name="expected" rows="{$test['expected_lines']}" cols="80">{$test['expected']}</textarea>

  <h2>&docweb.phpt.edit.approve;</h2>
  <input name="approve" type="checkbox"{$test['approve_checkbox']}/>
  <input type="submit" name="commit" value="&docweb.phpt.commit;">
</form>
HTML;

?>

<?php
global $data;

echo <<< HTML
<table border="1">
 <tr>
  <td>&docweb.users.username;</td>
  <td>&docweb.users.name;</td>
  <td>&docweb.users.country;</td>
 </tr>
HTML;


foreach ($data as $user) {

    $user['country'] = empty($user['country']) ? '&nbsp;' : $user['country'];

    echo <<< HTML
 <tr>
  <td><a href="/user/$user[username]">$user[username]</a></td>
  <td>$user[name]</td>
  <td>$user[country]</td>
 </tr>
HTML;

}

echo '</table>';
?>

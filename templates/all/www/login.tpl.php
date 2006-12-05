<p>
 &docweb.login.subhead;
</p>

<p>
<form method="post" name="login" action="/login.php">
<input type="hidden" name="return" value="<?php

    if (isset($_REQUEST['return'])) {
        echo htmlspecialchars($_REQUEST['return']);
    }

?>" />
<table border="0" cellspacing="2" cellpadding="2">
  <tr>
    <th>Username</th>
    <td><input type="text" name="username" value="" /></td>
  </tr>
  <tr>
    <th>Password</th>
    <td><input type="password" name="passwd" value="" /></td>
  </tr>
  <tr>
    <td>
    <td><input type="submit" value="Login &raquo;" /></td>
  </tr>
</table>
</form>
</p>

<p>
 &docweb.login.text;
</p>

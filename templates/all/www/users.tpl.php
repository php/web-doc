<?php
global $info;

echo <<< HTML
<p>&docweb.users.username;: $info[username]<br />
&docweb.users.name;: $info[name]<br />
&docweb.users.country;: $info[country]<br />
HTML;

if ($info['whishlist'])
	echo "<a href='$info[whishlist]'>&docweb.users.whishlist;</a><br />";

if (is_file(dirname(__FILE__) . '/images/users/' . $info['username'] . '.jpg'))
	echo "<img src='/images/users/$info[username].jpg' alt='&docweb.users.photo;'/>";

echo '</p>';
?>

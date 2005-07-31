<?php
global $info, $doEdit, $pictureError;

if (isset($doEdit)) {
auth();
switch ($pictureError) {
    case 'succes':
        $errors = "&docweb.users.succes;";
    break;
    case 'size':
        $errors = "&docweb.users.error.size;";
    break;
    case 'format':
        $errors = "&docweb.users.error.format;";
    break;
    default:
        $errors = '';
}
echo <<< HTML
<h3>$info[username], you can edit your info here</h3>
$errors
<form enctype="multipart/form-data" action="$_SERVER[REQUEST_URI]" method="post">
<table>
<tr><td>&docweb.users.name;</td><td>
    <input type="text" name="name" value="$info[name]" /></td></tr>
<tr><td>&docweb.users.country;</td><td>
    <input type="text" name="country" value="$info[country]" /></td></tr>
<tr><td>&docweb.users.website;</td><td>
    <input type="text" name="site" value="$info[site]" /></td></tr>
<tr><td>&docweb.users.wishlist;</td><td>
    <input type="text" name="wishlist" value="$info[wishlist]" /></td></tr>
<tr><td>&docweb.users.photo;</td><td><input type="file" name="photo"/></td></tr>
<tr><td>&nbsp;</td><td><input type="submit" name="editSubmit" value="&docweb.common.submit;"></td></tr>
</table>
</form>
HTML;

} else {
echo <<< HTML
<p><strong>&docweb.users.username;</strong>: $info[username]<br />
<strong>&docweb.users.name;</strong>: $info[name]<br />
<strong>&docweb.users.country;</strong>: $info[country]<br />
<strong>&docweb.users.website;</strong>: <a href="$info[site]">$info[site]</a><br />
HTML;

if ($info['wishlist'])
	echo "<a href='$info[wishlist]'>&docweb.users.wishlist;</a><br />";

if (is_file($_SERVER['DOCUMENT_ROOT'] . '/images/users/' . $info['username'] . '.jpg'))
	echo "<img src='/images/users/$info[username].jpg' alt='&docweb.users.photo;'/>";

echo '</p>';

}
?>

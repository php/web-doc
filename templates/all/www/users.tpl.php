<?php
global $info, $doEdit, $pictureError;

if (isset($doEdit)) {

if ($GLOBALS['user'] == $GLOBALS['userid']) {
// only edit yourself

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
$countries = '';
foreach ($GLOBALS['COUNTRIES'] as $code => $cntry) {
if ($info['country'] == $code) {
    $selected = ' selected="selected" ';
} else {
    $selected = '';
}
    $countries .= "<option value=\"".$code."\"$selected>".$cntry."</option>\n";
}
echo <<< HTML
<h3>$info[name], you can edit your info here</h3>
$errors
<form enctype="multipart/form-data" action="$_SERVER[REQUEST_URI]" method="post">
<table>
<tr><td>&docweb.users.name;</td><td>
    <input type="text" name="name" value="$info[name]" /></td></tr>
<tr><td>&docweb.users.country;</td><td>
<select name="country">
<option value="">Please Select</option>
$countries
</select></td></tr>
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
echo '<h3>&docweb.users.error.notyou;</h3>';
}

} else {
if (isset($info['country'])) {
$cntrycode = $GLOBALS['COUNTRIES'][$info['country']];
}
echo <<< HTML
<p><strong>&docweb.users.username;</strong>: $info[username]<br />
<strong>&docweb.users.name;</strong>: $info[name]<br />
<strong>&docweb.users.country;</strong>: $cntrycode<br />
<strong>&docweb.users.website;</strong>: <a href="$info[site]">$info[site]</a><br />
HTML;

if ($info['wishlist'])
	echo "<a href='$info[wishlist]'>&docweb.users.wishlist;</a><br />";

if (is_file($_SERVER['DOCUMENT_ROOT'] . '/images/users/' . $info['username'] . '.jpg'))
	echo "<img src='/images/users/$info[username].jpg' alt='&docweb.users.photo;'/>";

echo '</p>';

if ($GLOBALS['user'] == $GLOBALS['userid']) {
    echo '<p><a href="'.$info['username'].'/edit">Edit Your profile</a>';
}

}
?>

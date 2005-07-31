<?php
/* $Id$ */

include '../include/init.inc.php';
require_once '../include/lib_auth.inc.php';

if(empty($userid) || !is_string($userid) || !ctype_alpha($userid)) {
        echo site_header('docweb.common.header.users');
        echo '<h3>No such user found</h3>';
        echo site_footer();
        exit;
}

if (!$info = user_info($userid)) {
	$info[  'name'  ] = master_user_name($userid);
	$info['username'] = $userid;
	$info['country' ] = $info['site'] = $info['wishlist'] = '';
}

if (isset($_POST['editSubmit'])) {

$sql = 'UPDATE users SET
       name = "'.sqlite_escape_string(htmlspecialchars($_POST['name'])).'",
       country = "'.sqlite_escape_string(htmlspecialchars($_POST['country'])).'",
       site = "'.sqlite_escape_string(htmlspecialchars($_POST['site'])).'",
       wishlist = "'.sqlite_escape_string(htmlspecialchars($_POST['wishlist'])).'"
       WHERE username = "'.sqlite_escape_string($info['username']).'"';
$result = sqlite_query($sql, $idx);
if (!$result) {
    sqlite_error_string(sqlite_last_error($idx));
}    

        if (!empty($_FILES['photo']['name'])) {
            // this will need some more security checks
            if ($_FILES['photo']['size'] >= round((1024 * 1024)/10)) {
                $pictureError = 'size';
                unlink($_FILES['photo']['tmp_name']);
            } else {
                $img = getimagesize($_FILES['photo']['tmp_name']);
                if (!$img or $img[0] >= 500 or $img[1] >= 500 or $img['mime'] != 'image/jpeg') {
                    $pictureError = 'format';
                } else {
                    move_uploaded_file($_FILES['photo']['tmp_name'],
                         $_SERVER['DOCUMENT_ROOT'].'/images/users/' . $info['username'] . '.jpg');
                }
            }
        }
$pictureError = 'succes';
unset($info);
$info = user_info($userid);
}

if (isset($doEdit)) {
    auth();
    echo site_header('docweb.common.header.users.edit');
} else {
    echo site_header('docweb.common.header.users');
}
    echo DocWeb_Template::get('users.tpl.php');
    echo site_footer();

?>

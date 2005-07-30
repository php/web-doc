<?php
/* $Id$ */

include '../include/init.inc.php';
require_once '../include/lib_auth.inc.php';

if(empty($userid) || !is_string($userid) || !ctype_alpha($userid))
	die('no hacking allowed :)');

$info = user_info($userid);

echo site_header('docweb.common.header.users');
echo DocWeb_Template::get('users.tpl.php');
echo site_footer();

?>

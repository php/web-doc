<?php
/* $Id$ */

include_once '../include/init.inc.php';

echo site_header('docweb.common.header.welcome');
echo DocWeb_Template::get('home.tpl.php');

echo site_footer();

?>
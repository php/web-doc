<?php
/* $Id$ */

$project = "phd";
include_once '../../include/init.inc.php';

echo site_header('PhD: The [PH]P based [D]ocbook renderer');
echo DocWeb_Template::get('home.tpl.php');
echo site_footer();

?>

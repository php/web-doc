<?php
/* $Id$ */

// TODO: list of all project mailing lists somewhere (not here!)
include_once '../include/init.inc.php';
echo site_header('docweb.common.header.contact');
echo DocWeb_Template::get('contact.tpl.php');
echo site_footer();

?>
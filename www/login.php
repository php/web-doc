<?php
/* $Id$ */

include '../include/init.inc.php';

if (isset($_COOKIE['MAGIC_COOKIE']) || !empty($_POST)) {
    require_once '../include/lib_auth.inc.php';
    auth();
        
    if (isset($_REQUEST['return']) && !empty($_REQUEST['return'])
        && ctype_print($_REQUEST['return'])) {

        header('Location: http://'.$_SERVER['HTTP_HOST'].$_REQUEST['return']);
    }
    echo 'You are logged in';
    echo is_admin() ? ' <strong>with admin rights</strong>.' : '.';
} else {
    echo site_header('docweb.common.header.login');
    echo DocWeb_Template::get('login.tpl.php');
    echo site_footer();
}
?>

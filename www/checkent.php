<?php

include '../include/init.inc.php';

if(is_file('checkent_' . SITE . '.php')) {
    include 'checkent_' . SITE . '.php';
} else {
    echo site_header('docweb.common.header.checkent'); 
    echo '<p>checkent not found!</p>';
    echo site_footer();
}

?>

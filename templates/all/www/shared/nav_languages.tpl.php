<?php
foreach ($languages as $code => $name) {
    if ($code == 'all') {
        echo '<a href="' . get_insite_address(NULL, 'all') . '">&docweb.common.all;</a> '."\n";
    }
    else {
        // @@@ TODO: translate/entity names
        echo '<a href="' . get_insite_address(NULL, $code) . '" title="' . $name . '" ><img src="/images/flags/' . $code . '.png" alt="' . $name . '" /></a>'."\n";
    }
}
?>
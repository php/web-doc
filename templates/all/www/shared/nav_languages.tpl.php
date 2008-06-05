<?php
foreach ($languages as $code => $name) {
    if ($code == 'all') {
        echo '<a href="' . get_insite_address(NULL, 'all') . '">&docweb.common.all;</a> '."\n";
    }
    else {

        // Show specific country flags when language/country codes differ
        $alt_flags = array(
            'fa' => 'ir',
        );
        $flag_code = isset($alt_flags[$code]) ? $alt_flags[$code] : $code;

        // @@@ TODO: translate/entity names
        echo '<a href="' . get_insite_address(NULL, $code) . '" title="' . $name . '" ><img src="/images/flags/' . $flag_code . '.png" alt="' . $name . '" /></a>'."\n";
    }
}
?>
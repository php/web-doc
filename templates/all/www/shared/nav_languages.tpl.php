<?php
echo "&nbsp;"; // required for proper positioning of the active background
foreach ($languages as $code => $name) {

    if (LANGC == $code) {
        echo '<span class="active"></span><a class="active" ';
    } else {
        echo '<a ';
    }

    if ($code == 'all') {
        echo 'href="' . get_insite_address(NULL, 'all') . '">&docweb.common.all;</a>&nbsp;'."\n";
    }
    else {

        // Show specific country flags when language/country codes differ
        $alt_flags = array(
            'fa' => 'ir',
        );
        $flag_code = isset($alt_flags[$code]) ? $alt_flags[$code] : $code;

        // @@@ TODO: translate/entity names
        
        echo 'href="' . get_insite_address(NULL, $code) . '" title="' . $name . '" ><img src="/images/flags/' . $flag_code . '.png" alt="' . $name . '" /></a>'."\n";
    }
}
?>
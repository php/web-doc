<?php
if ($links) {
    foreach ($links AS $linkName => $url) {
        echo '<a href="'. $url ."\">&docweb.common.linkname.$linkName;</a><br />\n";
    }
} else {
    echo "&docweb.common.n-a;<br />\n";
}
?>
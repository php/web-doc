<?php
// Common Error Template
// yes, this is logic, but it's DISPLAY logic

// header
if ($header) {
    echo site_header($header);
}

// body
if ($body) {
    if (isset($body[0])) {
        echo "<h2>{$body[0]}</h2>";
    }
    if (isset($body[1])) {
        echo $body[1];
    }
}

// footer
if (!isset($footer) || !$footer) {
    echo site_footer();
}
?>

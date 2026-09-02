<?php

require_once __DIR__ . '/../include/init.inc.php';

site_header(['http_response_code' => 404]);

echo '<h2>Error 404</h2>';
if ($_SERVER['REDIRECT_STATUS'] == 404) {
	echo '<p>File not found: '.htmlspecialchars($_SERVER['REQUEST_URI']).'</p>';
}
else {
	echo '<p>An error occurred</p>';
}

site_footer();

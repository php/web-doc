<?php
require '../include/init.inc.php';
require '../include/Parsedown.php';

$parsedown = new Parsedown();
$chapter = isset($_GET['chapter']) ? $_GET['chapter'] : 'intro';
$chapter = preg_replace("/[^a-z0-9-]/", "", $chapter);
$path = '../tutorial/' . $chapter . '.md';

if (file_exists($path)) {
    $content = file_get_contents($path);
}
else {
    header('HTTP/1.1 404 Not Found');
    $_SERVER["REDIRECT_STATUS"] = '404';
    $uri = '/tutorial/'.$chapter.'.php';
    require 'error.php';
    die;
}

$edit_url = "https://github.com/php/web-doc/edit/master/tutorial/{$chapter}.md";

site_header();

echo '<p style="text-align: right; font-size: 85%"><a href="' . $edit_url . '">Edit this page</a></p>';

echo $parsedown->text($content);

site_footer();

<?php
require_once __DIR__ . '/../include/init.inc.php';
require_once __DIR__ . '/../include/Parsedown.php';

$BASE_DOCS_PATH = getenv('BASE_DOCS_PATH') ?: (__DIR__ . '/../../phpdoc/doc-base/docs');

$parsedown = new Parsedown();
$chapter = ($_GET['chapter'] ?? $_SERVER['PATH_INFO']) ?: 'README';
$chapter = preg_replace('/\.md$/', '', $chapter);
$chapter = preg_replace("/[^a-z0-9-]/i", "", $chapter);
$path = $BASE_DOCS_PATH . '/' . $chapter . '.md';
error_log("looking for $path");

if (file_exists($path)) {
    $content = file_get_contents($path);
}
else {
    header('HTTP/1.1 404 Not Found');
    $_SERVER["REDIRECT_STATUS"] = '404';
    $uri = '/tutorial/'.$chapter;
    require 'error.php';
    die;
}

$edit_url = "https://github.com/php/doc-base/edit/master/docs/{$chapter}.md";
$report_url = "https://github.com/php/doc-base/issues/new?body=From%20guide%20page:%20https:%2F%2Fdoc.php.net%2Fguide%2F{$chapter}.md%0A%0A---";

site_header();

echo $parsedown->text($content);

echo '</section><section class="secondscreen">';
echo $parsedown->text(file_get_contents($BASE_DOCS_PATH . '/toc.md'));
?>
<h2>Contribute</h2>
<ul>
    <li><a href="<?= $edit_url ?>">Edit this page</a>
    <li><a href="<?= $report_url ?>">Report a problem</a>
</ul>
<?php
site_footer();

<?php
include_once __DIR__ . '/../include/init.inc.php';

$requestUri = $_SERVER['REQUEST_URI'];
$matches = [];

if (preg_match('#^/guide(/.*)?$#i', $requestUri, $matches)) {
    if ($matches[1] == '') {
        header("301 Moved Permanently");
        header("Location: /guide/");
        exit;
    }
    // Strip the leading / for guide.php
    $_GET['chapter'] = substr($matches[1],1);
    include 'guide.php';
    exit;
}

site_header();
?>

<h2>What is this site?</h2>

<p>
  This site has instructions and tools for building, translating, and
  maintaining the 
  <a href="https://www.php.net/docs.php">PHP Documentation</a>, which
  is currently available in multiple languages plus the original English.
</p>

<h2>Join Us!</h2>

<p>
  Besides the PHP language itself and its core features, the documentation
  includes material about most <a href="https://www.php.net/extensions">PHP
  extensions</a>, which often could use additional contributions.
</p>

<p>
  Most of the translation teams are also in need of more helping hands. If
  you are interested, you should contact the relevant mailing lists. The
  projects and translations have separate mailing lists.
</p>

<?php
$sidebar = nav_languages();
echo site_footer($sidebar);

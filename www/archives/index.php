<?php
include_once __DIR__ . '/../../include/init.inc.php';

site_header();
?>

<h1>PHP Manual Archives</h1>

<p>
    Please remember that these documentation versions should not be used
    in everyday development, unless you are maintaining applications written
    to those specific version. These manuals lack many topics connected with
    newer PHP versions, are not updated anymore, and these versions of PHP
    are no longer receiving any security or other updates.
</p>

<h2>PHP 5 Documentation</h2>

<ul>
  <li><a href="php5/php_manual_en.html.gz">Single HTML file</a>
  <li><a href="php5/php_manual_en.tar.gz">Many HTML files (tar.gz)</a>
  <li><a href="php5/php_manual_en.chm">HTML Help file</a>
</ul>

<h2>PHP 4 Documentation</h2>

<ul>
  <li><a href="php4/bigxhtml.tar.gz">Single HTML file (tar.gz)</a>
  <li><a href="php4/chunked-xhtml.tar.gz">Many HTML files (tar.gz)</a>
  <li><a href="php4/chm.tar.gz">HTML Help file (tar.gz)</a>
</ul>

<?php
$sidebar = nav_languages();
echo site_footer($sidebar);

<?php
include_once '../include/init.inc.php';

site_header();
?>

<h2>What is this site?</h2>
<p>There are different documentation
teams operating somewhat independently on PHP.net subprojects. Only a
few people are involved in more than one documentation project currently,
and that limits the know-how sharing, which would otherwise be possible and
desirable. All of the documentation teams use an early fork of the original
PHP manual build system. These systems evolved independently, and took over
some improvements from the others on an occasional basis (the PEAR
documentation team uses a variant of the revcheck developed for phpdoc for
example). This site is aimed at getting documentation efforts closer, and
provide more tools and information for manual authors and translators.</p>

<h2>Join Us!</h2>
<p>The developers of PHP extensions all
have access to the PHP documentation modules, although the different projects
have different policies of accepting contributions. You are welcome to join
our efforts! Authoring documentation for an extension is the second best thing
to implementing the extension itself if you are interested in the
capabilities, features and usage possibilities even in extreme conditions. We
also have translated versions of the different manuals, and most of the
translation teams are always in need of more helping hands. In case you are
interested, you should contact the relevant mailing lists. The projects and
translations have separate mailing lists.</p>

<h2>Latest News!</h2>
<p>In March 2014 site was almost totally rebuilt. Now it uses same design as
new php.net pages. Many of tools and features have been removed, because they weren't used.
If you think that site is lacking some of them, please contact us on <code>php.webmaster</code>
mailing list.</p>

<?php
$sidebar = nav_languages();
echo site_footer($sidebar);

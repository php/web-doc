<?php
/*
+----------------------------------------------------------------------+
| PHP Documentation Tools Site Source Code                             |
+----------------------------------------------------------------------+
| Copyright (c) 1997-2014 The PHP Group                                |
+----------------------------------------------------------------------+
| This source file is subject to version 3.0 of the PHP license,       |
| that is bundled with this package in the file LICENSE, and is        |
| available through the world-wide-web at the following url:           |
| http://www.php.net/license/3_0.txt.                                  |
| If you did not receive a copy of the PHP license and are unable to   |
| obtain it through the world-wide-web, please send a note to          |
| license@php.net so we can mail you a copy immediately.               |
+----------------------------------------------------------------------+
| Authors:          Yannick Torres <yannick@php.net>                   |
|                   Mehdi Achour <didou@php.net>                       |
|                   Gabor Hojtsy <goba@php.net>                        |
|                   Sean Coates <sean@php.net>                         |
|                   Maciej Sobaczewski <sobak@php.net>                 |
+----------------------------------------------------------------------+
*/

function get_svn_dir($project)
{
    // @@@ make this return something until the function is found
    return $GLOBALS['PROJECTS'][$project][1] . '/';
}

function site_header()
{
   $TITLE = 'Documentation Tools';
   $SUBDOMAIN = 'doc';
   $CSS = array('/styles/doc.css');
   $LINKS = array(
        array('href' => '/revcheck.php', 'text' => 'Documentation Tools'),
        array('href' => '/tutorial/', 'text' => 'Tutorial for Contributors'),
        array('href' => '/phd.php', 'text' => 'PhD Homepage'),
    );

    require __DIR__ . '/../shared/templates/header.inc';

    echo '<section class="mainscreen">';
}

function site_footer($SECONDSCREEN = false)
{
    echo '</section>';
    require __DIR__ . '/../shared/templates/footer.inc';
}

function nav_languages()
{
    global $LANGUAGES;
    $out = '<div class="panel">';
    $out .= '<p class="headline"><a href="revcheck.php">Translation status</a></p>';
    $out .= '<div class="body">';
    $out .= '<ul>';
    foreach ($LANGUAGES as $code => $name)
    {
        $out .='<li><a href="revcheck.php?lang='.$code.'">'.$name.'</a></li>';
    }
    $out .= '</ul></div></div>';
    return $out;
}

function nav_tools($lang)
{
    global $LANGUAGES;
    $out = '<div class="panel">';
    $out .= '<p class="headline">Tools ('.$LANGUAGES[$lang].' Manual)</p>';
    $out .= '<div class="body">';
    $out .= '<ul>';
    $out .= '<li><a href="revcheck.php?p=translators&amp;lang='.$lang.'">Translators</a></li>';
    $out .= '<li><a href="revcheck.php?p=filesummary&amp;lang='.$lang.'">File summary</a></li>';
    $out .= '<li><a href="revcheck.php?p=files&amp;lang='.$lang.'">Outdated files</a></li>';
    $out .= '<li><a href="revcheck.php?p=misstags&amp;lang='.$lang.'">Missing revision numbers</a></li>';
    $out .= '<li><a href="revcheck.php?p=missfiles&amp;lang='.$lang.'">Untranslated files</a></li>';
    $out .= '<li><a href="revcheck.php?p=oldfiles&amp;lang='.$lang.'">Not in EN tree</a></li>';
    $out .= '<li><a href="revcheck.php?p=graph&amp;lang='.$lang.'">Graph</a></li>';
    $out .= '</ul></div></div>';
    return $out;
}
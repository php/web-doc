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
| Authors:          Nilgün Belma Bugüner <nilgun@php.net>              |
|                   Yannick Torres <yannick@php.net>                   |
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
        array('href' => '/guide/', 'text' => 'How to Contribute'),
        array('href' => '/revcheck.php', 'text' => 'Translation Status'),
        array('href' => '/phd.php', 'text' => 'PhD Homepage'),
    );

    require __DIR__ . '/../shared/templates/header.inc';
   echo <<<END_OF_MULTILINE
<style type="text/css">
table { margin-left: auto; margin-right: auto; text-align: left; border-spacing: 2px; font-size: 14px;}
th { color: white; background-color: #666699; padding: 0.2em; text-align: center; vertical-align: middle; }
td { background-color: #dcdcdc; padding: 0.2em 0.3em; }
.c { text-align: center; }
.n { white-space: nowrap; }
.o3 { overflow: hidden; max-width: 3em; }
.o6 { overflow: hidden; max-width: 6em; }
.copy { margin:0; padding: 0; font-size:small; }
.copy:hover { text-transform: uppercase; }
.copy:active { background: aqua; font-weight: bold; }
pre {
    background: white;
    border: solid 1px rgb(214, 214, 214);
    padding: 0.75rem;
    overflow: auto;
    font: normal 0.875rem/1.5rem "Source Code Pro", monospace;
}
</style>
END_OF_MULTILINE;
    echo '<section class="mainscreen">';
}

function site_footer($SECONDSCREEN = false)
{
    echo '</section>';
    echo <<<END_OF_MULTILINE
<script src="//cdn.jsdelivr.net/npm/clipboard@2.0.8/dist/clipboard.min.js"></script>
<script>
  var clipboard = new ClipboardJS('.btn');
  clipboard.on('success', function (e) {
     console.log(e);
  });
  clipboard.on('error', function (e) {
     console.log(e);
  });
</script>
END_OF_MULTILINE;
    require __DIR__ . '/../shared/templates/footer.inc';
}

function nav_languages($lang = null)
{
    global $LANGUAGES;
    $out = '<div class="panel">';
    $out .= '<p class="headline"><a href="/revcheck.php">Translation status</a></p>';
    $out .= '<div class="body">';
    $out .= '<ul>';
    foreach ($LANGUAGES as $code => $name)
    {
        $out .= '<li><a href="/revcheck.php?lang='.$code.'">'.$name.'</a>';
        if ($lang === $code) {
            $out .= '<ul>';
            $out .= '<li><a href="/revcheck.php?p=translators&amp;lang='.$lang.'">Translators</a></li>';
            $out .= '<li><a href="/revcheck.php?p=filesummary&amp;lang='.$lang.'">File summary</a></li>';
            $out .= '<li><a href="/revcheck.php?p=files&amp;lang='.$lang.'">Outdated files</a></li>';
            $out .= '<li><a href="/revcheck.php?p=misstags&amp;lang='.$lang.'">Missing revision numbers</a></li>';
            $out .= '<li><a href="/revcheck.php?p=missfiles&amp;lang='.$lang.'">Untranslated files</a></li>';
            $out .= '<li><a href="/revcheck.php?p=oldfiles&amp;lang='.$lang.'">Not in EN tree</a></li>';
            $out .= '</ul>';
        }
        $out .= '</li>';
    }
    $out .= '</ul></div></div>';
    return $out;
}

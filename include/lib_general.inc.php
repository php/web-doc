<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4:
+----------------------------------------------------------------------+
| PHP Documentation Tools Site Source Code                             |
+----------------------------------------------------------------------+
| Copyright (c) 1997-2011 The PHP Group                                |
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
+----------------------------------------------------------------------+
$Id$
*/

function is_translation($project, $language)
{
    return is_dir(SVN_DIR . $GLOBALS['PROJECTS'][$project] . '/' . $language);
}

function is_project($project)
{
    return isset($GLOBALS['PROJECTS'][$project]);
}

function get_svn_dir($project)
{
    // @@@ make this return something until the function is found
    return $GLOBALS['PROJECTS'][$project][1] . '/';
}

function site_header($full_screen = false)
{
    $TITLE = 'Documentation Tools';
	$SUBDOMAIN = 'doc';
	$LINKS = array(
        array('href' => '/revcheck.php', 'text' => 'Documentation Tools'),
        array('href' => '/dochowto/', 'text' => 'Documentation Howto'),
        array('href' => '/phd/', 'text' => 'PhD Homepage'),
    );

    require __DIR__ . '/../shared/templates/header.inc';

    if ($full_screen) {
        echo '<section class="fullscreen">';
    }
    else {
        echo '<section class="mainscreen">';
    }
}

function site_footer($SECONDSCREEN = false)
{
	echo '</section>';
    require __DIR__ . '/../shared/templates/footer.inc';
}

// Format email address for spam protection
function format_email($email)
{
    return str_replace(array('@', '.'), array(' at ', ' dot '), $email);
}


/**
* @return string
* @param string $text
* @desc Turn URLs into links
*/
function extract_links($text)
{
    $text = eregi_replace(
        "([[:alnum:]]+)://([^[:space:]]*)([[:alnum:]#?/&=])",
        "<a href=\"\\1://\\2\\3\">\\1://\\2\\3</A>",
        $text
    );
    $text = eregi_replace(
        "([_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)+)",
        "<a href=\"mailto:\\1\">\\1</a>",
        $text
    );
    return $text;
}


// Returns an insite address for a specified project, language and path (with fallback)
function get_insite_address($project = NULL, $lang = NULL, $path = NULL)
{
    if (!isset($project))      { $project = SITE;  }
    if (!isset($lang))         { $lang    = LANGC; }
    if (!isset($path))         { $path    = URI;   }
    if ($path == '/index.php') { $path    = '/';   }

    if ($project != SITE && 
       ($path != '/revcheck.php' && !strstr($path, '/rfc/') && $path != '/checkent.php')) {
        $path = '/';
    }

    $ret = '/';
    if ($project != $GLOBALS['defaultProject']) {
        $ret .= "$project/";
    }
    if ($lang != $GLOBALS['defaultLanguage']) {
        $ret .= "$lang/";
    }
    return $ret . ltrim($path, '/');
}

function get_resource_url($url = '')
{
    return "/$url";
}

function get_bug_count ($project=SITE) {
    
    $localFile = FILES_DIR . "bugs_{$project}.count";

    // $package is rawurlencode()'ed
    switch ($project)
    {
        case 'php':
            $package = 'Documentation+problem';
            break;
        
        case 'phd':
            $package = 'Doc+Build+problem';
            break;

        case 'gtk':
            $package = 'PHP-GTK+related';
            break;
            
        default:
            return false;
    }

    $link = 'https://bugs.php.net/search.php?cmd=display&bug_type=All&status=Open&by=Any' . 
            '&package_name[]=' . $package;

    // Cached (CACHE_BUGS_COUNT defined in init.inc.php)
    if (!(is_readable($localFile) && (filemtime($localFile) > (time() - CACHE_BUGS_COUNT)))) {
        if (!$count = file_get_contents($link . '&count_only=1')) {
            return false;
        }
        file_put_contents($localFile, $count);        
    } else {
        $count = file_get_contents($localFile);
    }

    if (strlen($count) > 0) {
        return array ('count' => (int) $count,
                      'link'  => $link,
                     );
    }
    return false;
}

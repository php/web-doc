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

require_once dirname(__FILE__) . '/lib_auth.inc.php';

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

function site_header($title = '', $style = array())
{
    define('IN_HEAD', true);
    $lang       = 'en'; //LANGC;
    $encoding   = 'UTF-8';
    $page_title = ($title ? $GLOBALS['Language']->get($title) . ' - ' : '') . $GLOBALS['Language']->get('docweb.common.title.default');
    $h1         = ($title ? $GLOBALS['Language']->get($title) : $GLOBALS['Language']->get('docweb.common.title.default'));
    list($page_h1) = explode("\n", wordwrap($h1, 50, "...\n"));

    $languages = site_nav_langs();
    $projects  = site_nav_projects();

    $langdisplay = $GLOBALS['LANGUAGES'][LANGC];
    $projdisplay = $GLOBALS['PROJECTS'][SITE][0];
    // this will prevent 404 errors
    $project     = (in_array(SITE, array('www', 'phd')) ? 'php' : SITE);
    $locallinks  = site_nav_provider();
    $extlinks    = ext_nav_provider();

    $extra_style = '';
    // prevent errors
    $guess_style = (SITE == 'www') ? '' : SITE . '.css';
    $styles = array_filter(($style ? array($style) : array($guess_style)));

    // Set proper encoding with HTTP header first
    header("Content-type: text/html; charset=$encoding");

    // bugs for count
    if ($bugs = get_bug_count()) {
        $showBugs = true;
        $bugCount = $bugs['count'];
        $bugsLink = $bugs['link'];
    } else {
        $showBugs = $bugCount = $bugsLink = false;
    }

    return DocWeb_Template::get(
        'shared/header.tpl.php',
        array(
            'encoding'    => $encoding,
            'lang'        => $lang,
            'project'     => $project,
            'page_h1'     => $page_h1,
            'styles'      => $styles,
            'projects'    => $projects,
            'languages'   => $languages,
            'projdisplay' => $projdisplay,
            'langdisplay' => $langdisplay,
            'locallinks'  => $locallinks,
            'extlinks'    => $extlinks,
            'page_title'  => $page_title,
            'showBugs'    => $showBugs,
            'bugCount'    => $bugCount,
            'bugsLink'    => $bugsLink,
        )
    );
}

function site_nav_projects()
{
    return DocWeb_Template::get('shared/nav_projects.tpl.php');
}

function site_nav_langs()
{
    return DocWeb_Template::get(
        'shared/nav_languages.tpl.php',
        array('languages' => $GLOBALS['LANGUAGES'])
    );
}

function site_footer($svn_version = NULL)
{
    $master = get_insite_address(NULL, NULL, '');
    return DocWeb_Template::get(
        'shared/footer.tpl.php',
        array('master'      => $master,
              'svn_version' => $svn_version)
    );
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

// This function provides the relevant insite navigation options for the
// particular page the user is viewing currently
function site_nav_provider()
{
    $links['subsite-homepage'] = BASE_URL . '/';
    if (in_array(SITE, array('gtk', 'pear', 'php'))) {
        $links['rev-check'] = BASE_URL .'/revcheck.php';
    }
    if (SITE == 'php') {
        $links['doc-howto'] = BASE_URL . '/dochowto/index.php';
//        FIXME: Disabled until livedocs requirement is removed
//        $links['entities']  = BASE_URL . '/entities.php';
    }

    switch(SITE) {
        case 'php':
        case 'pear':
        case 'gtk':
            $links['checkent']     = BASE_URL . '/checkent.php';
            break;
   }

    // English only
    if (LANGC == 'all' || LANGC == 'en') {

        switch(SITE) {
            case 'php':
                $links['orphan-notes']     = BASE_URL . '/orphan_notes.php';
                $links['notes-stats']      = BASE_URL . '/notes_stats.php';
                $links['undoc-functions']  = BASE_URL . '/undoc_functions.php';
                $links['missing-examples'] = BASE_URL . '/missing_examples.php';
                $links['phpt-generator']   = BASE_URL . '/phpt_generator.php';
    
            case 'pear':
        }
    }

    if (is_admin())
        $links['admin'] = BASE_URL . '/admin.php';

    return DocWeb_Template::get(
        'shared/nav_links.tpl.php',
        array('links' => $links, 'Language' => &$GLOBALS['Language'])
    );
}

// This function provides the relevant offsite navigation options for the
// particular page the user is viewing currently
function ext_nav_provider()
{
    $links = array();
    switch (SITE) {
    case 'php':
        switch (LANGC) {
        case 'hu':
            $links['translation-info'] = 'http://wfsz.njszt.hu/projektek_phpdoc.php';
            break;
        case 'it':
            $links['translation-info'] = 'http://cortesi.com/php/';
            break;
        }
        break;
    }
    return DocWeb_Template::get(
        'shared/nav_links.tpl.php',
        array('links' => $links, 'Language' => &$GLOBALS['Language'])
    );
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

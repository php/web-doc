<?php
/*
+----------------------------------------------------------------------+
| PHP Documentations Site Source Code                                  |
+----------------------------------------------------------------------+
| Copyright (c) 1997-2004 The PHP Group                                |
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
+----------------------------------------------------------------------+
$Id$
*/

error_reporting(E_ALL);

// Copy lib_site_conf.inc.php and add proper settings!
include_once 'lib_site_conf_local.inc.php';

define('SQLITE_DIR', PATH_ROOT . '/sqlite/');
define('CVS_DIR',    PATH_ROOT . '/cvs/');

// Map of supported documentation types to CVS module names
$PROJECTS = array(
    'php'      => array('PHP Documentation',     'phpdoc-all'),
    'smarty'   => array('Smarty',                'smarty/docs'),
    'pear'     => array('PEAR Documentation',    'peardoc'),
    'gtk'      => array('PHP-GTK Documentation', 'php-gtk-docs'),
    'livedocs' => array('Livedocs',              ''),
    'pecl'     => array('PECL Documentation',    ''),
    'www'      => array('Documentations',        ''),
);

// Supported languages
$LANGUAGES = array(
    'all' => 'All',
    'pt_BR' => 'Brazilian Portuguese',
    'zh' => 'Chinese (Simplified)',
    'hk' => 'Chinese (Hong Kong Cantonese)',
    'tw' => 'Chinese (Traditional)',
    'cs' => 'Czech',
    'nl' => 'Dutch',
    'fi' => 'Finnish',
    'fr' => 'French',
    'de' => 'German',
    'el' => 'Greek',
    'he' => 'Hebrew',
    'hu' => 'Hungarian',
    'it' => 'Italian',
    'ja' => 'Japanese',
    'kr' => 'Korean',
    'pl' => 'Polish',
    'ro' => 'Romanian',
    'ru' => 'Russian',
    'sk' => 'Slovak',
    'sl' => 'Slovenian',
    'es' => 'Spanish',
    'sv' => 'Swedish',
    'tr' => 'Turkish',
    'en' => 'English'
);

// If PHP is running as an Apache module, get
if (substr(php_sapi_name(), 0, 6) == 'apache') {

    // Grab subdomains if provided
    $domains = explode(
    ".",
    preg_replace("!\\.?" . preg_quote(WEB_ROOT) . "$!", '', $_SERVER['HTTP_HOST'])
    );

    $project = 'www'; $language = 'all';
    foreach($domains as $domain) {
        $domain = ($domain == 'pt_br' ? 'pt_BR' : $domain);
        if (in_array($domain, array_keys($PROJECTS))) {
            $project = $domain;
        }
        elseif (in_array($domain, array_keys($LANGUAGES))) {
            $language = $domain;
        }
    }

    define('SITE',  $project);
    define('LANGC', $language);
    define('LANGD', $LANGUAGES[$language]);
}

function is_translation($project, $language)
{
    return is_dir(CVS_DIR . $GLOBALS['PROJECTS'][$project] . '/' . $language);
}

function is_project($project)
{
    return isset($GLOBALS['PROJECTS'][$project]);
}

function get_cvs_dir($project)
{
/** make this return something until the function is found **/
//    return documentation_exists($project) ? $GLOBALS['PROJECTS'][$project] . '/' : FALSE;
      return $GLOBALS['PROJECTS'][$project][1];
}

function site_header($title = '')
{
    $lang       = 'en'; //LANGC;
    $master_url = get_resource_url();
    $encoding   = "iso-8859-1"; // for now
    $page_title = ($title ? $title . ' - PHP Documentations' : 'PHP Documentations');
    $page_h1    = ($title ? "<h1>$title</h1>" : '<h1>PHP Documentations</h1>');

    $languages = site_nav_langs();
    $projects  = site_nav_projects();

    $langdisplay = $GLOBALS['LANGUAGES'][LANGC];
    $projdisplay = $GLOBALS['PROJECTS'][SITE][0];
    $locallinks  = site_nav_provider();
    $extlinks    = ext_nav_provider();

    // Set proper encoding with HTTP header first
    header("Content-type: text/html; charset=$encoding");

    $buff = <<<END_OF_BUFFER
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="$lang" lang="$lang">
<head>
 <title>$page_title</title>
 <meta http-equiv="Content-Type" content="text/html; charset=$encoding" />
 <meta http-equiv="Content-Script-Type" content="text/javascript" />
 <meta http-equiv="Content-Style-Type" content="text/css" />
 <meta http-equiv="Content-Language" content="$lang" />
 <style type="text/css">@import url({$master_url}style/site.css);</style>
</head>
<body>
 <div id="header">
  <h1>$page_h1</h1>
  <div id="logos">
   {$projects}
  </div>
 </div>
 <div id="langs">
  {$languages}
 </div>
 <div id="page">
  <div id="sidebar">
   <div class="sidebox">
    <dl>
     <dt>Currently focused on</dt>
     <dd>{$projdisplay} | {$langdisplay}</dd>
     <dt>Insite contextual navigation</dt>
     <dd>{$locallinks}</dd>
     <dt>Offsite contextual navigation</dt>
     <dd>{$extlinks}</dd>
    </dl>
   </div>
  </div>
END_OF_BUFFER;
    return $buff;
}

function site_nav_projects()
{
    return '
   <a href="' . get_insite_address('www') . '" class="logo logo-all">All</a>
   <a href="' . get_insite_address('php') . '" class="logo logo-php">PHP</a>
   <a href="' . get_insite_address('pear') . '" class="logo logo-pear">PEAR</a>
   <a href="' . get_insite_address('pecl') . '" class="logo logo-pecl">PECL</a>
   <a href="' . get_insite_address('gtk') . '" class="logo logo-gtk">PHP-<span class="logo-g">G</span><span class="logo-t">T</span><span class="logo-k">K</span></a>
   <a href="' . get_insite_address('smarty') . '" class="logo logo-smarty">Smarty</a>
   <a href="' . get_insite_address('livedocs') . '" class="logo logo-livedocs">Livedocs</a>';
}

function site_nav_langs()
{
    $navlist = '';
    foreach ($GLOBALS['LANGUAGES'] as $code => $name) {
        if ($code == 'all') {
            $navlist .= '<a href="' . get_insite_address(NULL, 'all') . '">All</a> ';
        }
        else {
            $navlist .= '<a href="' . get_insite_address(NULL, $code) . '" title="' . $name . '"><img src="' . get_resource_url("images/flags/$code.png") . '"></a> ';
        }
    }
    return $navlist;
}

function site_footer()
{
    $master = get_resource_url();
    $buff = <<<END_OF_BUFFER
 </div>
 <div id="footer">
  <p>
   <a href="{$master}copyright.php">Copyright</a> 2004 The PHP Documentation Teams - All rights reserved.
  <p>
  <p>
   <a href="{$master}credits.php">Credits</a> | <a href="{$master}contact.php">Contact</a>
  </p>
 </div>
</body>
</html>
END_OF_BUFFER;
    return $buff;
}

// Format email address for spam protection
function format_email($email)
{
    return str_replace(array('@', '.'), array(' at ', ' dot '), $email);
}

function get_comment($idx, $section, $doc, $file)
{
    $data = array();

    $sql = 'SELECT
             id, file, doc, user, date, note

             FROM
             comments

             WHERE
             section=\'' . $section . '\' AND
             file=\'' . $file . '\' AND
             doc=\'' . $doc . '\'
      
             ORDER BY 
             date DESC';
    $result = @sqlite_query($idx, $sql);

    $data['nb_rows'] = sqlite_num_rows($result);

    if ($data['nb_rows'] > 0) {
        $data['rows'] = array();
        while ($row = sqlite_fetch_array($result)) {
            $data['rows'][] = $row;
        }
    }

    return $data;
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
    if (!isset($project)) { $project = SITE; }
    if (!isset($lang))    { $lang    = LANGC; }
    if (!isset($path))    { $path    = $_SERVER['PHP_SELF']; }

    $project = ($project == 'www') ? '' : "$project.";
    $lang    = ($lang == 'all')    ? '' : "$lang.";
    
    return 'http://' . $project . $lang . WEB_ROOT . $path;
}

function get_resource_url($url = '')
{
    return 'http://' . WEB_ROOT . '/' . $url;
}

// This function provides the relevant insite navigation options for the
// particular page the user is viewing currently
function site_nav_provider()
{
    $links = array('<a href="/">Subsite homepage</a>');
    if (in_array(SITE, array('gtk', 'pear', 'php', 'smarty'))) {
        $links[] = '<a href="/revcheck.php">Revision check</a>';
    }
    if (SITE == 'php') {
        $links[] = '<a href="/dochowto/index.php">Documentation Howto</a>';
    }
    if (count($links) == 0) {
        $links[] = 'N/A';
    }
    return join("<br />", $links);
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
            $links[] = '<a href="http://wfsz.njszt.hu/projektek_phpdoc.php">Translation information</a>';
            break;
            case 'it':
            $links[] = '<a href="http://cortesi.com/php/">Translation information</a>';
            break;
        }
        break;
    }

    if (count($links) == 0) {
        $links[] = 'N/A';
    }
    return join("<br />", $links);
}

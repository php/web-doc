<?php
/*
vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4:
+----------------------------------------------------------------------+
| PHP Documentation Tools Site Source Code                             |
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
|                   Sean Coates <sean@php.net>                         |
+----------------------------------------------------------------------+
$Id$
*/

// split from lib_general.inc.php

// Map of supported documentation types to CVS module names
$PROJECTS = array(
    'www'      => array('Documentation',         ''),
    'php'      => array('PHP Documentation',     'phpdoc-all'),
    'smarty'   => array('Smarty',                'smarty/docs'),
    'pear'     => array('PEAR Documentation',    'peardoc'),
    'gtk'      => array('PHP-GTK Documentation', 'php-gtk-docs'),
    'livedocs' => array('Livedocs',              ''),
    'pecl'     => array('PECL Documentation',    ''),
);

// Supported languages
$LANGUAGES = array(
    'all'   => 'All',
    'ar'    => 'Arabic',
    'pt_BR' => 'Brazilian Portuguese',
    'zh'    => 'Chinese (Simplified)',
    'hk'    => 'Chinese (Hong Kong Cantonese)',
    'tw'    => 'Chinese (Traditional)',
    'cs'    => 'Czech',
    'da'    => 'Danish',
    'nl'    => 'Dutch',
    'fi'    => 'Finnish',
    'fr'    => 'French',
    'de'    => 'German',
    'el'    => 'Greek',
    'he'    => 'Hebrew',
    'hu'    => 'Hungarian',
    'it'    => 'Italian',
    'ja'    => 'Japanese',
    'kr'    => 'Korean',
    'pl'    => 'Polish',
    'pt'    => 'Portuguese',
    'ro'    => 'Romanian',
    'ru'    => 'Russian',
    'sk'    => 'Slovak',
    'sl'    => 'Slovenian',
    'es'    => 'Spanish',
    'sv'    => 'Swedish',
    'tr'    => 'Turkish',
    'en'    => 'English',
);

?>

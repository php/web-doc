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
// project => (Name, local folder, cvs module)
    'www'      => array('Documentation',         '',        ''),
    'php'      => array('PHP Documentation',     DOC_DIR,  'phpdoc/en'),
    'smarty'   => array('Smarty',                'smarty/docs', 'smarty/docs/en'),
    'pear'     => array('PEAR Documentation',    PEAR_DIR, 'peardoc/en'),
    'gtk'      => array('PHP-GTK Documentation', GTK_DIR, 'php-gtk-doc/manual/en'),
    'phd'      => array('phd',                   '',        ''),
);

// Supported languages
$LANGUAGES = array(
    'all'   => 'All',
    'ar'    => 'Arabic',
    'pt_BR' => 'Brazilian Portuguese',
    'bg'    => 'Bulgarian',
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
    'no'    => 'Norwegian',
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

$COUNTRIES = array(
    "US" => "United States",
    "AI" => "Anguilla",
    "AR" => "Argentina",
    "AU" => "Australia",
    "AT" => "Austria",
    "BY" => "Belarus",
    "BE" => "Belgium",
    "BR" => "Brazil",
    "BG" => "Bulgaria",
    "CA" => "Canada",
    "CL" => "Chile",
    "C2" => "China",
    "CR" => "Costa Rica",
    "CY" => "Cyprus",
    "CZ" => "Czech Republic",
    "DK" => "Denmark",
    "DO" => "Dominican Republic",
    "EC" => "Ecuador",
    "EE" => "Estonia",
    "FI" => "Finland",
    "FR" => "France",
    "DE" => "Germany",
    "GR" => "Greece",
    "HK" => "Hong Kong",
    "HU" => "Hungary",
    "IS" => "Iceland",
    "IN" => "India",
    "IE" => "Ireland",
    "IL" => "Israel",
    "IT" => "Italy",
    "JM" => "Jamaica",
    "JP" => "Japan",
    "LV" => "Latvia",
    "LT" => "Lithuania",
    "LU" => "Luxembourg",
    "MY" => "Malaysia",
    "MT" => "Malta",
    "MX" => "Mexico",
    "NL" => "Netherlands",
    "NZ" => "New Zealand",
    "NO" => "Norway",
    "PL" => "Poland",
    "PT" => "Portugal",
    "RU" => "Russia",
    "SG" => "Singapore",
    "SK" => "Slovakia",
    "SI" => "Slovenia",
    "ZA" => "South Africa",
    "KR" => "South Korea",
    "ES" => "Spain",
    "SE" => "Sweden",
    "CH" => "Switzerland",
    "TW" => "Taiwan",
    "TH" => "Thailand",
    "TR" => "Turkey",
    "GB" => "United Kingdom",
    "UY" => "Uruguay",
    "VE" => "Venezuela",
);

?>

<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4:
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
| Authors:          Sean Coates <sean@php.net>                         |
+----------------------------------------------------------------------+
$Id$
*/

/**
* PHP DocWeb - Language Class
*
* Simple language class. Allows for very simple i18n of DocWeb content
* Gets data from [cvs]phpdoc-all
*
* @package  docweb
* @since    2004.09.25
* @author   Sean Coates <sean@php.net>
*/
class DocWeb_Language
{
    var $lang;
    var $defaultEntities = array();
    var $nativeEntities  = array();
    var $entities        = array();

    /**
    * Constructor -- (old school (wrapper))
    */
    function DocWeb_Language($lang)
    {
        return $this->__construct($lang);
    }

    /**
    * Constructor -- sets the language
    */
    function __construct($lang)
    {
        // verify $lang
        if (in_array($lang, array_keys($GLOBALS['LANGUAGES']))) {
            
            // get default text
            $this->defaultEntities = $this->getTextData($GLOBALS['defaultFallbackLanguage']);

            // fixate default language ('all' isn't a language)
            // and get native entities (if applicable)
            if ($lang == $GLOBALS['defaultLanguage']) {
                $this->lang = $GLOBALS['defaultFallbackLanguage'];
                $this->nativeEntities =& $this->defaultEntities;
            } else {
                $this->lang = $lang;
                $this->nativeEntities  = $this->getTextData($this->lang);
            }
            
            // now, merge the default and native arrays, to form a full set
            $this->entities = array_merge($this->defaultEntities, $this->nativeEntities);
            
        } else {
            trigger_error("Invalid language ('$lang');\n", E_USER_ERROR);
        }
    }

    /**
    * Gets entity data from cvs:phpdoc-all/$lang/docweb/main.ent
    *
    * @param  string  $lang language to 'get'
    * @return array native language texts
    */
    function getTextData($lang=FALSE)
    {
        // use class default, if $lang is not provided
        if (!$lang) {
            $lang = $this->lang;
        }

        // use this file:
        $entFile = CVS_DIR ."/phpdoc-all/$lang/docweb/main.ent";

        if (is_readable($entFile)) {
            // file is good; get its contents
            $entData = file_get_contents($entFile);

            // try to find the encoding of the file
            preg_match('/<?.*encoding\s*=\s*["\']([^"\']+)["\'].*?>/', $entData, $matches);
            $charset = isset($matches[1]) ? $matches[1] : 'UTF-8';

            // find entities in the file
            $entMatchRegex = '/<!ENTITY ('. preg_quote(DOCWEB_ENTITIY_PREFIX)
                            .'\..*?) ([\'"])(.*?)\2>/s';            
            preg_match_all($entMatchRegex, $entData, $matches);

            // init local entities variable
            $entities = array();

            // loop through matches, creating entities dataset
            foreach ($matches[0] as $index => $val) {
                $entities[$matches[1][$index]] = @iconv($charset, 'UTF-8//IGNORE', $matches[3][$index]);
            }
            return $entities;
        } else {
            // entities file not found
            // return empty array, to allow default
            return array();
        }
    }

    /**
    * Gives a single entity's text
    *
    * @param string $ent    Entity to get
    * @param array  $params Optional array of dynamic parameters
    * @return mixed text associated with $ent, or FALSE on failure (not present)
    */
    function get($ent, $params=FALSE)
    {

        $params = (array) $params;
        
        static $paramEntMatchRegex = FALSE;
        if (!$paramEntMatchRegex) {
            $paramEntMatchRegex = '/&('. preg_quote(DOCWEB_PARAM_ENTITIY_PREFIX) .'\..*?);/';
        }
        
        if ($this->entities) {
            $text = isset($this->entities[$ent]) ? stripslashes($this->entities[$ent]) : $ent;
            preg_match_all($paramEntMatchRegex, $text, $matches);
            for ($i=0; $i<count($matches[0]); $i++) {
                $rep = substr($matches[1][$i], strlen(DOCWEB_PARAM_ENTITIY_PREFIX) + 1);
                if (isset($params[$rep])) {
                    $text = str_replace($matches[0][$i], $params[$rep], $text);
                }
            }
            return $text;
        } else {
            return $ent;
        }
    }
}
?>

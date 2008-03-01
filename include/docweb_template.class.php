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
* PHP DocWeb - Templating Class
*
* This is a very simple templating system, if we decide to go with one of
* the more "mature" template engines, down the road, this can be used as
* a wrapper to maintain BC. Please see docweb/templates/README for the rules.
*
* @package  docweb
* @since    2004.09.25
* @author   Sean Coates <sean@php.net>
*/
class DocWeb_Template
{
    var $rendered = FALSE;
    var $params   = array();

    var $language;

    /**
    * BC constructor
    *
    * @param  string  $filename template file
    * @see    DocWeb_Template::__construct()
    */
    function DocWeb_Template($filename)
    {
        return $this->__construct($filename);
    }

    /**
    * Template constructor
    *
    * Use DocWeb_Template::factory(...) to instanciate this object
    *
    * @param  string  $filename template file
    */
    function __construct($filename)
    {
        $this->filename = $filename;
        // always pass $Language
        $this->params['Language'] =& $GLOBALS['Language'];
        return TRUE;
    }

    /**
    * Template Factory
    *
    * Safely instanciates DocWeb_Template object
    * Encapsulates external data (project, lang & defaults)
    *
    * @param  string  $template template (file) name
    * @return mixed   DocWeb_Template object on success, FALSE on failure
    */
    function factory($template)
    {
        // vars (become onject properties after instanciation)
        $language        = LANGC;
        $project         = SITE;
        $defaultLanguage = $GLOBALS['defaultLanguage'];
        $defaultProject  = $GLOBALS['defaultProject'];

        // set up paths:
        $templateRoot = PATH_ROOT . '/templates';

        // determine proper filename (fallback to defaults)
        if (is_readable("$templateRoot/$language/$project/$template")) {
            // specific (language:project) template exists, and is usable
            $filename = "$templateRoot/$language/$project/$template";
        } elseif (is_readable("$templateRoot/$language/$defaultProject/$template")) {
            // semi-specific (language:default) template exists, use it
            $filename = "$templateRoot/$language/$defaultProject/$template";
        } elseif (is_readable("$templateRoot/$defaultLanguage/$defaultProject/$template")) {
            // default template exists:
            $filename = "$templateRoot/$defaultLanguage/$defaultProject/$template";
        } else {
            // can't find usable template
            trigger_error("No usable template ('$template');\n", E_USER_ERROR);
            return FALSE;
        }

        // template file is readable, instanciate class
        $Template = new DocWeb_Template($filename);
        $Template->language = $language;
        return $Template;
    }

    /**
    * Renders template from file
    *
    * @return string rendered template (output)
    */
    function render()
    {
        extract($this->params);
        ob_start();
        require($this->filename);
        $tplOut = ob_get_contents();
        ob_end_clean();
        $this->output = $this->replace_entities($tplOut);
        $this->rendered = TRUE;
        return $this->output;
    }

    /**
    * Get a template
    *
    * Called statically
    * Convenience function to instanciate & render
    *
    * @param  array $template template (file) name
    * @param  array $params
    */
    function get($template, $params=FALSE)
    {
    
        $Template =& DocWeb_Template::factory($template);
        if ($params) {
            $Template->params = $params;
        }
        return $Template->render();
    }
    
    /**
    * For the given text, replace docweb entities with their appropriate values
    *
    * @param  string  $text text in which to replace entities
    * @return string  text containing expanded entities
    */
    function replace_entities($text)
    {
        $Language =& $GLOBALS['Language'];
        
        static $entMatchRegex = FALSE;
        if (!$entMatchRegex) {
            $entMatchRegex = '/&('. preg_quote(DOCWEB_ENTITIY_PREFIX) .'\..*?);/';
        }
        
        preg_match_all($entMatchRegex, $text, $matches);
        $replaceCounter = 0;
        for ($i=0; $i<count($matches[0]); $i++) {
            if ($rep = $Language->get($matches[1][$i])) {
                $text = str_replace($matches[0][$i], $rep, $text);
                $replaceCounter++;
            }
        }

        // recurse, if there were replacements, and there are possibly more
        if ($replaceCounter && preg_match($entMatchRegex, $text)) {
            $text = $this->replace_entities($text);
        }
        
        return $text;
    }
}
?>

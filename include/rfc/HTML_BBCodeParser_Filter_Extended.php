<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2011 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.02 of the PHP license,      |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Author: Tobias Schlitt <toby@php.net>                               |
// +----------------------------------------------------------------------+
//
// $Id$
//

/**
* @package  HTML_BBCodeParser
* @author   Stijn de Reede  <sjr@gmx.co.uk>
*/


require_once('HTML/BBCodeParser.php');




class HTML_BBCodeParser_Filter_Extended extends HTML_BBCodeParser
{

    /**
    * An array of tags parsed by the engine
    *
    * @access   private
    * @var      array
    */
    var $_definedTags = array(  'size' => array('htmlopen'  => 'font',
                                                'htmlclose' => 'font',
                                                'allowed'   => 'all',
                                                'attributes'=> array('size' =>'size=%2$s%1$s%2$s')),
                                'color' => array('htmlopen'  => 'font',
                                                'htmlclose' => 'font',
                                                'allowed'   => 'all',
                                                'attributes'=> array('color' =>'color=%2$s%1$s%2$s')),
                                'quote' => array('htmlopen'  => 'q',
                                                'htmlclose' => 'q',
                                                'allowed'   => 'all^img',
                                                'attributes'=> array('quote' =>'cite=%2$s%1$s%2$s')),
                                'code' => array('htmlopen'  => 'code',
                                                'htmlclose' => 'code',
                                                'allowed'   => 'all^img'),
                            );

    /**
    * Executes statements before the actual array building starts
    *
    * This method should be overwritten in a filter if you want to do
    * something before the parsing process starts. This can be useful to
    * allow certain short alternative tags which then can be converted into
    * proper tags with preg_replace() calls.
    * The main class walks through all the filters and and calls this
    * method if it exists. The filters should modify their private $_text
    * variable.
    *
    * @return   none
    * @access   private
    * @see      $_text
    * @author   Stijn de Reede  <sjr@gmx.co.uk>
    */

    function _preparse()
    {
        $pattern = "/color=#([0-9a-fA-F])/i";
        $replace = "color=\\1";
        $this->_preparsed = preg_replace($pattern, $replace, $this->_text);
    }
}

?>
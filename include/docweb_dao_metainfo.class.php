<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4:
+----------------------------------------------------------------------+
| PHP Documentation Site Source Code                                   |
+----------------------------------------------------------------------+
| Copyright (c) 2005 The PHP Group                                     |
| Copyright (c) 1997-2004 Dave Barr                                    |
+----------------------------------------------------------------------+
| This source file is subject to version 3.0 of the PHP license,       |
| that is bundled with this package in the file LICENSE, and is        |
| available through the world-wide-web at the following url:           |
| http://www.php.net/license/3_0.txt.                                  |
| If you did not receive a copy of the PHP license and are unable to   |
| obtain it through the world-wide-web, please send a note to          |
| license@php.net so we can mail you a copy immediately.               |
+----------------------------------------------------------------------+
| Author:        Sean Coates <sean@php.net>                            |
+----------------------------------------------------------------------+
$Id$
*/
require_once 'docweb_dao_common.class.php';

class DocWeb_DAO_MetaInfo extends DocWeb_DAO_Common
{
    /**
     * Constructor - instanciate parent
     *
     * @param  bool   Check the schema & create missing tables? (set to true
     *                from generation scripts)
     */
    function DocWeb_DAO_MetaInfo($checkSchema = FALSE)
    {
        $this->DocWeb_DAO_Common($checkSchema);
    }
    
    /**
     * Store function alias data
     * 
     * @param string $ext   Extension to which this alias belongs
     * @param string $alias Alias function name
     * @param string $func  Reference function name
     * @return bool
     */
    function storeFunctionAlias($ext, $alias, $func)
    {
        $sql = "
            INSERT
            INTO
                function_aliases
                (
                    extension,
                    alias,
                    function
                )
            VALUES
                (
                  '". $this->DB->escapeSimple($ext) ."',
                  '". $this->DB->escapeSimple($alias) ."',
                  '". $this->DB->escapeSimple($func) ."'
                )
        ";
        if (PEAR::isError($this->DB->query($sql))) {
            echo " ** Query failed.\n";
            return FALSE;
        }
        return TRUE;
    }
    
    /**
     * Purges function_aliases table
     */
    function purgeAliases()
    {
        $sql = "
            DELETE
            FROM
              function_aliases
        ";
        if (PEAR::isError($this->DB->query($sql))) {
            echo " ** Purge Query failed.\n";
            return FALSE;
        }
        return TRUE;
    }
    
    /**
     * Determines if the passed function name is an alias
     * 
     * @param   string $func
     * @return bool
     */
    function isAlias($func)
    {
        $sql = "
            SELECT
                COUNT(function)
            FROM
                function_aliases
            WHERE
                alias = '". $this->DB->escapeSimple($func) ."'
        ";
        if (PEAR::isError($match = $this->DB->getOne($sql))) {
            echo " ** Query failed.\n";
            return FALSE;
        }
        return $match ? TRUE : FALSE;
    }

    /**
     * Purges missing_examples table
     */
    function purgeExamples()
    {
        $sql = "
            DELETE
            FROM
              missing_examples
        ";
        if (PEAR::isError($this->DB->query($sql))) {
            echo " ** Purge Query failed.\n";
            return FALSE;
        }
        return TRUE;
    }

    /**
     * Store missing example data
     * 
     * @param string $ext   Extension to which this function belongs
     * @param string $func  Function name
     * @return bool
     */
    function storeMissingExample($ext, $func)
    {
        $sql = "
            INSERT
            INTO
                missing_examples
                (
                    extension,
                    function
                )
            VALUES
                (
                  '". $this->DB->escapeSimple($ext) ."',
                  '". $this->DB->escapeSimple($func) ."'
                )
        ";
        if (PEAR::isError($this->DB->query($sql))) {
            echo " ** Query failed.\n";
            return FALSE;
        }
        return TRUE;
    }

    /**
     * Purges undocumented_functions table
     */
    function purgeUndocumented()
    {
        $sql = "
            DELETE
            FROM
              undocumented_functions
        ";
        if (PEAR::isError($this->DB->query($sql))) {
            echo " ** Purge Query failed.\n";
            return FALSE;
        }
        return TRUE;
    }

    /**
     * Store missing example data
     * 
     * @param string $ext   Extension to which this function belongs
     * @param string $func  Function name
     * @return bool
     */
    function storeUndocumentedFunction($ext, $func)
    {
        $sql = "
            INSERT
            INTO
                undocumented_functions
                (
                    extension,
                    function
                )
            VALUES
                (
                  '". $this->DB->escapeSimple($ext) ."',
                  '". $this->DB->escapeSimple($func) ."'
                )
        ";
        if (PEAR::isError($this->DB->query($sql))) {
            echo " ** Query failed.\n";
            return FALSE;
        }
        return TRUE;
    }

}    
?>

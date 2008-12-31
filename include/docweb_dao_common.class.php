<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4:
+----------------------------------------------------------------------+
| PHP Documentation Site Source Code                                   |
+----------------------------------------------------------------------+
| Copyright (c) 2005-2009 The PHP Group                                |
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

define('DOCWEB_DAO_DB_FILE', SQLITE_DIR . 'docweb.sqlite');

class DocWeb_DAO_Common
{
    /**
     * @var object PEAR::DB object
     */
    var $DB;
  
    /**
     * Constructor - Connect to the DocWeb DB
     *
     * @param  bool   Check the schema & create missing tables? (set to true
     *                from generation scripts)
     */
    function DocWeb_DAO_Common($checkSchema = FALSE)
    {
        require_once 'PEAR.php';
        require_once 'DB.php'; // PEAR::DB
        $this->dsn = 'sqlite:///' . DOCWEB_DAO_DB_FILE . '?mode=0666';
        if (PEAR::isError($this->DB = DB::connect($this->dsn)))
        {
            echo "Error connecting to DocWeb database: ". DOCWEB_DAO_DB_FILE ."\n";
            echo " * Error message: ". $this->DB->getMessage() ."\n";
            die();
        }
        if ($checkSchema) {
            $this->checkSchema();
        }
    }
    
    /**
     * Stores a key-value pair in the meta_data table (deletes old pair, if
     * exists, and inserts a new pair)
     * 
     * @param string $keyName name of unique key to stor
     * @param string $val     value to associate with $keyName
     */
    function storeMetaData($keyName, $val)
    {
        $sql = "
            DELETE
            FROM
                meta_data
            WHERE
                keyname = '" . $this->DB->escapeSimple($keyName) . "'
        ";
        if (PEAR::isError($this->DB->query($sql))) {
            die(" ** Store Meta Data (delete) query failed. (Key: '$keyName')\n");
        }
        $sql = "
            INSERT
            INTO
                meta_data (keyname, val)
            VALUES
                ('" . $this->DB->escapeSimple($keyName) . "', '" . $this->DB->escapeSimple($val) . "')
        ";
        if (PEAR::isError($this->DB->query($sql))) {
            die(" ** Store Meta Data (insert) query failed. (Key: '$keyName', Val: '$val')\n");
        }
        return TRUE;        
    }
    
    /**
     * Logs the current time as meta_data (start time) (Helper method)
     * 
     * @param string $type type to store
     */
    function metaLogStartTime($type)
    {
        $this->storeMetaData("{$type}_start_time", time());  
    }

    /**
     * Logs the current time as meta_data (end time) (Helper method)
     * 
     * @param string $type type to store
     */
    function metaLogEndTime($type)
    {
        $this->storeMetaData("{$type}_end_time", time());  
    }

    /**
     * Checks if a given table already exists
     * 
     * @param string   $tableName table to check
     * @return bool
     */
    function tableExists($tableName)
    {
        $sql = "
            SELECT
                COUNT(name)
            FROM
                sqlite_master
            WHERE
                type = 'table'
                AND
                name = '". sqlite_escape_string($tableName) ."'
        ";
        if (PEAR::isError($exists = $this->DB->getOne($sql))) {
            echo "Error checking table: $tableName.\n";
            echo " * Error message: ". $r->getMessage() ."\n";
            die();
        }
        return $exists ? TRUE : FALSE;
    }
    
    /**
     * Creates the database schema
     * 
     * Add any new tables. Tables should be preceeded with a call to the check
     * mechanism. Keep these organized. Also, leave them at the end of the file
     * (they'll be the most changed)
     */
    function checkSchema()
    {
        // meta_data
        if (!$this->tableExists('meta_data')) {
            $sql = "
                CREATE
                TABLE
                    meta_data
                    (
                        keyname VARCHAR(100) PRIMARY KEY,
                        val     VARCHAR(255)
                    );
            ";
            if (PEAR::isError($create = $this->DB->query($sql)))
            {
                die("Query Error: ". $create->getMessage() ."\n");
            }
        }
        
        // function_aliases 
        if (!$this->tableExists('function_aliases')) {
            $sql = "
                CREATE
                TABLE
                    function_aliases
                    (
                        extension VARCHAR(100),
                        alias     VARCHAR(100),
                        function  VARCHAR(100)
                    )
            ";
            if (PEAR::isError($create = $this->DB->query($sql)))
            {
                die("Query Error: ". $create->getMessage() ."\n");
            }
        }
        
        // missing_examples 
        if (!$this->tableExists('missing_examples')) {
            $sql = "
                CREATE
                TABLE
                    missing_examples
                    (
                        extension VARCHAR(100),
                        function  VARCHAR(100)
                    )
            ";
            if (PEAR::isError($create = $this->DB->query($sql)))
            {
                die("Query Error: ". $create->getMessage() ."\n");
            }
        }

        // undocumented_functions 
        if (!$this->tableExists('undocumented_functions')) {
            $sql = "
                CREATE
                TABLE
                    undocumented_functions
                    (
                        extension VARCHAR(100),
                        function  VARCHAR(100)
                    )
            ";
            if (PEAR::isError($create = $this->DB->query($sql)))
            {
                die("Query Error: ". $create->getMessage() ."\n");
            }
        }

        // all complete
        return TRUE;
    }


    
}
?>
<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4:
+----------------------------------------------------------------------+
| PHP Documentation Site Source Code                                   |
+----------------------------------------------------------------------+
| Copyright (c) 1997-2005 The PHP Group                                |
+----------------------------------------------------------------------+
| This source file is subject to version 3.01 of the PHP license,      |
| that is bundled with this package in the file LICENSE, and is        |
| available at through the world-wide-web at                           |
| http://www.php.net/license/3_01.txt.                                 |
| If you did not receive a copy of the PHP license and are unable to   |
| obtain it through the world-wide-web, please send a note to          |
| license@php.net so we can mail you a copy immediately.               |
+----------------------------------------------------------------------+
| Authors: Etienne Kneuss <colder@php.net>                             |
+----------------------------------------------------------------------+
$Id$
*/

require_once '../include/init.inc.php';
require_once '../include/lib_phpt_generator.inc.php';
require_once '../include/lib_auth.inc.php';
require_once 'Archive/Tar.php';

// clean magic_quotes_gpc

if (get_magic_quotes_gpc()) {
    array_walk_recursive($_GET,     'phpt_clean_gpc');
    array_walk_recursive($_POST,    'phpt_clean_gpc');
    array_walk_recursive($_REQUEST, 'phpt_clean_gpc');
    array_walk_recursive($_COOKIE,  'phpt_clean_gpc');
}


if (!$sqlite = phpt_sqlite_open()) {
    echo site_header('docweb.common.header.phptgen'); 
    echo '<p>Unable to open examples database</p>';
    echo site_footer();
    exit();
}


/**
 * Generate tests
 */
if (!empty($_REQUEST['ids']) && isset($_REQUEST['generate'])) {
    phpt_generate_list($sqlite, array_keys($_REQUEST['ids']));
    exit;
}

/**
 * Generate every filled tests
 */
if (isset($_REQUEST['generateAll'])) {

    phpt_generate_all($sqlite, PHPT_FLAG_FILLED);

    exit;
}

/**
 * Generate one test
 */
if (!empty($_REQUEST['generateId'])) {

    $sql_query = 'SELECT id,
                         title,
                         location,
                         test,
                         skipif,
                         expected,
                         edit_date,
                         import_date,
                         flags
                    FROM tests
                   WHERE id = '.(int)$_REQUEST['generateId'];
               
    $results = sqlite_query($sqlite, $sql_query);

    phpt_download(sqlite_fetch_array($results, SQLITE_ASSOC));
    exit;
}


/**
 * check rights when admin required.
 */
if ((!empty($_REQUEST['ids']) && isset($_REQUEST['delete']))
     || isset($_REQUEST['import'])
     || (!empty($_REQUEST['editId']) && isset($_REQUEST['commit']))) {

    auth();
    if (!is_admin()) {
	    die('you are not an admin!');
    }
}

echo site_header('docweb.common.header.phptgen'); 

     

/**
 * Remove tests
 */
if (!empty($_REQUEST['ids']) && isset($_REQUEST['delete'])) {
    phpt_delete_list($sqlite, array_keys($_REQUEST['ids']));
}


/**
 * Import tests
 */
if (isset($_REQUEST['import'])) {
    // this operation requires authentication as 
    // it can waste a lot of server resources

    $num = phpt_import_from_source($sqlite);
    echo "<h2>Imported $num examples</h2>";
}

/**
 * Handle edition
 */
if (!empty($_REQUEST['editId'])) {
    
    $test_id  = (int)$_REQUEST['editId'];
    $edited   = null;
    $error    = '';
    $testData = array();
    $q        = !empty($_REQUEST['q']) ? rawurlencode($_REQUEST['q']) : '';

    if (isset($_REQUEST['commit'])) {
        $edited = phpt_commit_changes($sqlite, $_REQUEST);
    }

    $sql_query = 'SELECT id,
                         title,
                         location,
                         test,
                         skipif,
                         expected,
                         edit_date,
                         import_date,
                         flags
                    FROM tests
                   WHERE id = '.$test_id;
               
    $results = sqlite_query($sqlite, $sql_query);
    
    
    $row = sqlite_fetch_array($results, SQLITE_ASSOC);
    
    if (empty($row)) {
        $error    = '&docweb.phpt.editidnotfound;';
    } else {
        $testData = phpt_extrapolate($row);
    }
    
    
    echo DocWeb_Template::get(
        'phpt_edit.tpl.php',
        array(
            'test'   => $testData,
            'error'  => $error,
            'edited' => $edited,
            'q'      => $q,
        )
    );
    echo site_footer();
    exit;

}
/**
 * Display listing
 */
$testsData = array();

$imported = 0;
$filled   = 0;
$approved = 0;
$total    = 0;
$q        = '';

if (!empty($_REQUEST['q'])) {
    
    $q     = trim($_REQUEST['q']);
    $q_sql = str_replace('\%', '%', sqlite_escape_string($q));
    $sql_query = 'SELECT id,
                         title,
                         location,
                         test,
                         expected,
                         edit_date,
                         import_date,
                         flags
                    FROM tests
                   WHERE location LIKE \'%'.$q_sql.'%\'
                ORDER BY flags DESC, location, id
                   LIMIT '.PHPT_LIMIT_RESULTS;
        
    $results = sqlite_query($sqlite, $sql_query);


    while ($row = sqlite_fetch_array($results, SQLITE_ASSOC)) {
        $testsData[$row['id']] = phpt_extrapolate($row);
        if (($row['flags'] & PHPT_FLAG_APPROVED) === PHPT_FLAG_APPROVED) {
            $approved ++;
        }
        if (($row['flags'] & PHPT_FLAG_FILLED) === PHPT_FLAG_FILLED) {
            $filled ++;
        }
        if ($row['flags'] === PHPT_FLAG_IMPORTED) {
            $imported ++;
        }

        $total++;
    }
}
echo DocWeb_Template::get(
    'phpt_list.tpl.php',
    array(
        'tests'       => $testsData,
        'search'      => htmlentities($q),
        'search_enc'  => rawurlencode($q),
        'nimported'   => $imported,
        'nfilled'     => $filled,
        'napproved'   => $approved,
        'ntotal'      => $total
        
    )
);
    
echo site_footer();


?>

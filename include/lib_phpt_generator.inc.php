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
/**
 * Parameters
 */
define('PHPT_EOL',           "\n");
define('PHPT_SQLITE_FILE',   SQLITE_DIR . 'tests.sqlite');
define('PHPT_DOC_PATH',      CVS_DIR.'phpdoc-all/en/');

define('PHPT_LIMIT_RESULTS', 200);

define('PHPT_EXAMPLE_REGEX', '#<example>\s*<title>(.+)</title>\s*<programlisting role="php">\s*<!\[CDATA\[(.+)]]>\s*</programlisting>(.+)</example>#isU');
define('PHPT_OUTPUT_REGEX',  '#&example\.outputs;\s*<screen>\s*<!\[CDATA\[(.+)]]>\s*</screen>#isU');

define('PHPT_FLAG_IMPORTED',    1);
define('PHPT_FLAG_FILLED',      2);
define('PHPT_FLAG_APPROVED',    4);
define('PHPT_FLAG_IMPLEMENTED', 8);

define('PHPT_FORMAT_SKIPIF', "--TEST--".PHPT_EOL.                        
                             "%s (generated)".PHPT_EOL. 
                             "Location: %s".PHPT_EOL. 
                             "--SKIPIF--".PHPT_EOL.
                             "%s".PHPT_EOL. 
                             "--FILE--".PHPT_EOL.                       
                             "%s".PHPT_EOL.                        
                             "--EXPECT--".PHPT_EOL.
                             "%s");
                      
define('PHPT_FORMAT', "--TEST--".PHPT_EOL.                        
                      "%s (generated)".PHPT_EOL. 
                      "Location: %s".PHPT_EOL. 
                      "--FILE--".PHPT_EOL.                       
                      "%s".PHPT_EOL.                        
                      "--EXPECT--".PHPT_EOL.
                      "%s");


// disable magic_quotes_gpc

function phpt_clean_gpc (&$item, $key) 
{
    $item = stripslashes($item);
}


/**
 * Opens a new SQLite connection for URLs
 *
 * @return resource SQLite connection
 */
function phpt_sqlite_open()
{
    return @sqlite_open(PHPT_SQLITE_FILE, 0666);
}
 

/**
 * Extrapolate informations from the sql result
 *
 * @return resource SQLite connection
 */
function phpt_extrapolate($row)
{

    $return = array('id'                => (int) $row['id'],
                    'title'             => $row['title'],
                    'location'          => '',
                    'class'             => '',
                    'status'            => '',
                    'expected'          => '',
                    'test'              => '',
                    'skipif'            => '',
                    'approve_checkbox'  => '',
                    'cvs_link'          => 'http://cvs.php.net/viewvc.cgi/phpdoc/en/'.$row['location'].'?view=markup',
                    'test_lines'        => 4,
                    'expected_lines'    => 4,
                    'skipif_lines'      => 1,
                    );

    // clean up location
    $path_pieces = explode('/', $row['location']);
    $return['location'] = implode('/', array_slice($path_pieces, -3));

    // generate the status and the status class
    if (($row['flags'] & PHPT_FLAG_IMPLEMENTED) === PHPT_FLAG_IMPLEMENTED) {
        $return['status'] = '&docweb.phpt.implemented;';
        $return['class']  = 'phpt_implemented';
    } else if (($row['flags'] & PHPT_FLAG_APPROVED) === PHPT_FLAG_APPROVED) {
        $return['status'] = '&docweb.phpt.approved;';
        $return['class']  = 'phpt_approved';
    } else if (($row['flags'] & PHPT_FLAG_FILLED) === PHPT_FLAG_FILLED) {
        $return['status'] = '&docweb.phpt.filled;';
        $return['class']  = 'phpt_filled';
    } else {
        $return['status'] = '&docweb.phpt.imported;';
        $return['class']  = 'phpt_none';
    }

    
    if (!empty($row['expected'])) {
        if(($num = substr_count($row['expected'], "\n")) > $return['expected_lines']) {
            if($num > 30) {
                $num = 30;
            }
            
            $return['expected_lines'] = $num;
            
        }
        $return['expected'] = htmlentities($row['expected']);
    }

    if (!empty($row['test'])) {
        if(($num = substr_count($row['test'], "\n")) > $return['test_lines']) {
            if($num > 30) {
                $num = 30;
            }
            $return['test_lines'] = $num;
        }
        $return['test'] = htmlentities($row['test']);
    }

    if (!empty($row['skipif'])) {
        if(($num = substr_count($row['skipif'], "\n")) > $return['skipif_lines']) {
            if($num > 30) {
                $num = 30;
            }
            $return['skipif_lines'] = $num;
        }
        $return['skipif'] = htmlentities($row['skipif']);
    }
    // approve checkbox

    if (($row['flags'] & PHPT_FLAG_APPROVED) === PHPT_FLAG_APPROVED) {
        $return['approve_checkbox'] = ' checked';
    }
    
    // careful with large titles
    $return['title_limited'] = trim($return['title']);
    
    if (strlen($return['title_limited']) > 40) {
        $return['title_limited'] = substr($return['title_limited'], 0, 37).'...';
    }
    
    $return['title']              = trim(htmlentities($return['title']));
    $return['title_limited'] = htmlentities($return['title_limited']);
    
    return $return;

}


/**
 * Import examples from doc sources.
 *
 * @return int, number of imported examples
 */
function phpt_import_from_source($sqlite)
{

    // Begin the listing
    $userdata = array('examples_added' => 0,
                      'sqlite'         => $sqlite);

    
    $path = '';

    phpt_list_files(PHPT_DOC_PATH, $path, $userdata);
    
    return $userdata['examples_added'];

}

/**
 * List files recursivly and scan them
 *
 * @return bool
 */
function phpt_list_files($prefix, $path, &$userdata) 
{
    
    if (is_dir($prefix.$path) && is_resource($handle = @opendir($prefix.$path))) {

        while ($name = readdir($handle)) {
            if (strpos($name, ".xml") !== false) {
                phpt_scan_file($prefix, $path.$name, $userdata);
            } else if(is_dir($prefix.$path.$name) && $name !== 'CVS' && $name !== '.' && $name !== '..') {
                phpt_list_files($prefix, $path.$name.DIRECTORY_SEPARATOR, $userdata);
            }

        }

        closedir($handle);
        return true;

    } else {
        return false;
    }
    
}

/**
 * Scan files for examples, and insert them
 *
 * @return null
 */
function phpt_scan_file($prefix, $path, &$userdata) 
{
    
    if (!is_file($prefix.$path)) {
        return false;
    }
    
    $content = file_get_contents($prefix.$path);

    if ($number = preg_match_all(PHPT_EXAMPLE_REGEX, $content, $matches)) {
        // found at least an example

        foreach ($matches[2] as $key => $example) {

            $expected = '';
            $flags    = 1;

            /* Parse the extra content */
            $extra = $matches[3][$key];
        
            if (preg_match(PHPT_OUTPUT_REGEX, $extra, $match)) {
                $expected = $match[1];
                $flags   |= PHPT_FLAG_FILLED; 
            }
        
            
            $title = preg_replace('#<function>([\w_-]+)</function>#', '\1()', $matches[1][$key]);


            // let's create the entry

            $sql_query = "INSERT INTO tests (
                                             title, 
                                             location, 
                                             test,
                                             expected,
                                             import_date,
                                             flags
                                            ) 
                                     VALUES (
                                             '".sqlite_escape_string($title)."',
                                             '".sqlite_escape_string($path)."',
                                             '".sqlite_escape_string(phpt_clean_ws($example))."',
                                             '".sqlite_escape_String(phpt_clean_ws($expected))."',
                                             datetime('now'),
                                             $flags
                                            )";
            // @ here to avoid "column test is not unique".
            if (!@sqlite_exec($userdata['sqlite'], $sql_query)) {
                $number--;
            }

        }

        $userdata['examples_added'] += $number;
    }
    
}

/**
 * Generate a list of examples
 *
 * @return null
 */
function phpt_generate_list($sqlite, $ids)
{
    $ids = array_map('intval', $ids);

    // select data

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
           WHERE id IN ('.implode(',', $ids).')';
               
    $results = sqlite_query($sqlite, $sql_query);
    
    
    if (count($ids) === 1) {
        phpt_download(sqlite_fetch_array($results, SQLITE_ASSOC));
    } else {
    
        $archivePath = '/tmp/phpt_'.md5(implode('-', $ids)).'.tgz';
    
        header('Content-Type: application/gzip');
        header('Content-Disposition: attachment; filename='.basename($archivePath));

        

        $myArchive = new Archive_Tar($archivePath, 'gz');

        while ($row = sqlite_fetch_array($results, SQLITE_ASSOC)) {
            $myArchive->addString('test'.$row['id'].'.phpt', phpt_generate($row));
        }
        
        // Destructor, to correctly close the file
        $myArchive->_Archive_Tar();

        readfile($archivePath);
        unlink($archivePath);

    }
}
/**
 * Generate a list of examples selected by flag
 *
 * @return null
 */
function phpt_generate_all($sqlite, $flags)
{

    // select data

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
           WHERE flags & '.$flags.' = '.$flags;
               
    $results = sqlite_query($sqlite, $sql_query);
    
    if (sqlite_num_rows($results) === 1) {
        phpt_download(sqlite_fetch_array($results, SQLITE_ASSOC));
    } else {
        $flags_verbose = array();

        if ($flags & PHPT_FLAG_FILLED) {
            $flags_verbose[] = 'filled';
        }

        if ($flags & PHPT_FLAG_APPROVED) {
            $flags_verbose[] = 'approved';
        }

        if ($flags & PHPT_FLAG_IMPLEMENTED) {
            $flags_verbose[] = 'implemented';
        }
        

        $archivePath = '/tmp/phpt_'.implode('_', $flags_verbose).'.tgz';
    
        header('Content-Type: application/gzip');
        header('Content-Disposition: attachment; filename='.basename($archivePath));

        

        $myArchive = new Archive_Tar($archivePath, 'gz');

        while ($row = sqlite_fetch_array($results, SQLITE_ASSOC)) {
            $myArchive->addString('test'.$row['id'].'.phpt', phpt_generate($row));
        }
        // Destructor, to correctly close the file
        $myArchive->_Archive_Tar();

        readfile($archivePath);
        unlink($archivePath);

    }
}
/**
 * Delete a list of tests
 *
 * @return null
 */
function phpt_delete_list($sqlite, $ids)
{
    $ids = array_map('intval', $ids);

    $sql_query = 'DELETE FROM tests
           WHERE id IN ('.implode(',', $ids).')';
               
    return var_dump(sqlite_exec($sqlite, $sql_query));

}

/**
 * Generate one example
 *
 * @return null
 */
function phpt_generate($infos)
{

    if (empty($infos['skipif'])) {

        return sprintf(PHPT_FORMAT, $infos['title'],
                                    $infos['location'],
                                    $infos['test'],
                                    $infos['expected']);
                                                            
    } else {
    
        return sprintf(PHPT_FORMAT_SKIPIF, $infos['title'],
                                           $infos['location'],
                                           $infos['skipif'],
                                           $infos['test'],
                                           $infos['expected']);
    }
}
    

/**
 * Generate one example and make it available to download
 *
 * @return null
 */
function phpt_download($infos)
{
    if (headers_sent()) {
        return false;
    }

    $path_pieces = explode('/', $infos['location']);
    $name =  implode('_', array_slice($path_pieces, -3));

    $name = substr($name, 0, -4).'.phpt';

    header('Content-Type: text/plain');
    header('Content-Disposition: attachment; filename="'.$name.'"');

    echo phpt_generate($infos);
}


/**
 * Commit changes
 *
 * @return true/false
 */
function phpt_commit_changes($sqlite, $input)
{
    $title     = !empty($input['title'])    ? sqlite_escape_string($input['title']) : '';
    $test      = !empty($input['test'])     ? sqlite_escape_string(phpt_clean_ws($input['test'])) : '';
    $skipif    = !empty($input['skipif'])   ? sqlite_escape_string(phpt_clean_ws($input['skipif'])) : '';
    $expected  = !empty($input['expected']) ? sqlite_escape_string(phpt_clean_ws($input['expected'])) : '';
    $flags     = isset($input['approve'])   ? '(flags | '.PHPT_FLAG_APPROVED.')' : '(flags & ~'.PHPT_FLAG_APPROVED.')';
    if (!empty($expected)) {
        $flags .= ' | '.PHPT_FLAG_FILLED;
    }
    
    $sql_query = "UPDATE tests SET edit_date = datetime('now'),
                                   title     = '$title',
                                   test      = '$test',
                                   skipif    = '$skipif',
                                   expected  = '$expected',
                                   flags     = ($flags)
                             WHERE id = ".intval($input['editId']);
    //echo $sql_query;
    return @sqlite_exec($sqlite, $sql_query);
}

/**
 * clean linefeeds and extra spaces
 */
function phpt_clean_ws($string)
{
    $string = trim($string);
    return str_replace(array("\r\n", "\r", "\n"), PHPT_EOL, $string);
}


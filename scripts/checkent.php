<?php
/**
 * +----------------------------------------------------------------------+
 * | PHP Documentation Site Source Code                                   |
 * +----------------------------------------------------------------------+
 * | Copyright (c) 1997-2005 The PHP Group                                |
 * +----------------------------------------------------------------------+
 * | This source file is subject to version 3.0 of the PHP license,       |
 * | that is bundled with this package in the file LICENSE, and is        |
 * | available at through the world-wide-web at                           |
 * | http://www.php.net/license/3_0.txt.                                  |
 * | If you did not receive a copy of the PHP license and are unable to   |
 * | obtain it through the world-wide-web, please send a note to          |
 * | license@php.net so we can mail you a copy immediately.               |
 * +----------------------------------------------------------------------+
 * | Authors: Nuno Lopes <nlopess@php.net>                                |
 * +----------------------------------------------------------------------+
 *
 * $Id$
 */


/*
 * checks for errors in URL entities. Currently supports FTP and HTTP.
 *
 * Based on phpdoc/scripts/checkent.php by Georg Richter and Gabor Hojtsy.
 */


set_time_limit(0);
$inCli = true;
include '../include/init.inc.php';

$filename = CVS_DIR . '/phpdoc-all/entities/global.ent';

// Schemes currently supported
$schemes = array('http', 'ftp');

// constants for errors
define('UNKNOWN_HOST', 0);
define('FTP_CONNECT', 1);
define('FTP_LOGIN', 2);
define('FTP_NO_FILE', 3);
define('HTTP_CONNECT', 4);
define('HTTP_MOVED', 5);
define('HTTP_WRONG_HEADER', 6);
define('HTTP_BOGUS_HEADER', 7);


if (!$file = file_get_contents($filename)) {
    exit;
}

$array = explode('<!-- Obsoletes -->', $file);


// Find entity names and URLs
$schemes_preg = '(?:' . join('|', $schemes) . ')';
preg_match_all("@<!ENTITY\s+(\S+)\s+([\"'])({$schemes_preg}://[^\\2]+)\\2\s*>@U", $array[0], $entities_found);

// These are the useful parts
$entity_names = $entities_found[1];
$entity_urls  = $entities_found[3];


// Walk through entities found
foreach($entity_urls as $num => $entity_url) {

    // Get the parts of the URL
    $url = parse_url($entity_url);
    $entity = $entity_names[$num];

    // Try to find host
    if (gethostbyname($url['host']) == $url['host']) {
        $errors[$num] = array(UNKNOWN_HOST);
        continue;
    }


    switch($url['scheme']) {
    
        case 'http':

            $url['path'] = isset($url['path']) ? $url['path'] : '/';

            if (!$fp = @fsockopen($url['host'], 80, $errno, $errstr, 30)) {
                $errors[$num] = array(HTTP_CONNECT);

            } else {
                fputs($fp, "HEAD {$url['path']} HTTP/1.0\r\nHost: {$url['host']}\r\nConnection: close\r\n\r\n");

                $str='';
                while (!feof($fp)) {
                    $str .= fgets ($fp);
                }
                fclose ($fp);

                if(preg_match('@HTTP/1.\d (\d+)(?: .+)?@S', $str, $match)) {

                    if($match[1] != '200') {

                        if(preg_match('/Location: (.+)/', $str, $redir)) {
                            $errors[$num] = array(HTTP_MOVED, $redir[1], $match[0]);
                        } else {
                            $errors[$num] = array(HTTP_WRONG_HEADER, $match[1], $match[0]);
                        }
                    } // error != 200

                } else {
                    $errors[$num] = array(HTTP_BOGUS_HEADER, $str);
                }
            }
        break;
    

        case 'ftp':
            if ($ftp = @ftp_connect($url['host'])) {
                if (@ftp_login($ftp, 'anonymous', 'IEUser@')) {
                    $flist = ftp_nlist($ftp, $url['path']);

                    if (!count($flist)) {
                        $errors[$num] = array(FTP_NO_FILE);
                    }
                } else {
                    $errors[$num] = array(FTP_LOGIN);
                    ftp_quit($ftp);
                }
            } else {
                $errors[$num] = array(FTP_CONNECT);
            }
        break;

    }
}


// ouput the html
echo "<?php include_once '../include/init.inc.php'; echo site_header('docweb.common.header.checkent'); ?><p>&nbsp;</p>".
     '<table class="Tc"><tr class="blue"><th>Entity Name</th><th>Current URL</th><th>Error</th><th>Notes</th></tr>';


foreach($errors as $num => $error) {

    // choose color for row background
    switch($error[0]) {
        case UNKNOWN_HOST:
        case HTTP_BOGUS_HEADER:
        case HTTP_WRONG_HEADER:
        case FTP_NO_FILE:
            $css = 'crit'; break;

        case HTTP_MOVED:
            $css = 'wip'; break;

        default:
            $css = 'old';
    }

    echo "<tr class=\"$css\"><td>".$entity_names[$num].'</td><td>'.$entity_urls[$num].'</td><td>';

    switch($error[0]) {
        case UNKNOWN_HOST:
            echo 'Unknown host</td><td>&nbsp;';
            break;

        case FTP_CONNECT:
        case HTTP_CONNECT:
            echo "Couldn't connect to remote host</td><td>&nbsp;";
            break;

        case FTP_LOGIN:
            echo "Couldn't login. 'anonymous' was rejected</td><td>&nbsp;";
            break;

        case FTP_NO_FILE:
            echo "The file doesn't exist on the server</td><td>&nbsp;";
            break;

        case HTTP_MOVED:
            echo 'Moved to ' . $error[1] . '</td><td>HTTP response: ' . $error[2];
            break;

        case HTTP_WRONG_HEADER:
            echo 'HTTP error ' . $error[1] . '</td><td>HTTP response: ' . $error[2];
            break;

        case HTTP_BOGUS_HEADER:
            echo 'Unknown HTTP error </td><td>HTTP headers: ' . $error[1];
            break;
    }

    echo '</td></tr>';
}

echo '</table><p>&nbsp;</p><p><b>Total</b>: '.count($errors).'</p><?php echo site_footer(); ?>';

?>
